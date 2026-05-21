<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\KelasHubEngineController;
use App\Models\Student;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route debug-db dihapus untuk keamanan produksi

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'name' => ['required'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'name' => 'Nama atau Password (NIM+Kode) salah.',
    ])->onlyInput('name');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dipindahkan ke grup auth untuk keamanan
// Route::get('/kh/cron/reset-schedule', function (Request $request) { ... });

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [KelasHubEngineController::class, 'getStudentDashboard'])->name('dashboard');
    Route::post('/kh/password', [KelasHubEngineController::class, 'updatePassword']);
    Route::post('/kh/attendance', [KelasHubEngineController::class, 'storeAttendance']);

    // Routes khusus Pengurus (Ketua Kelas, Sekretaris, Bendahara)
    Route::middleware(['role:ketua_kelas,sekretaris,bendahara'])->group(function () {
        Route::post('/kh/schedule/toggle-delivery', [KelasHubEngineController::class, 'toggleDeliveryType']);
        Route::post('/kh/schedule', [KelasHubEngineController::class, 'storeSchedule']);
        Route::post('/kh/master-subject', [KelasHubEngineController::class, 'storeMasterSubject']);
        Route::post('/kh/assignment', [KelasHubEngineController::class, 'storeAssignment']);
        Route::post('/kh/module', [KelasHubEngineController::class, 'storeModule']);
        Route::get('/kh/module/{id}/download', [KelasHubEngineController::class, 'downloadModule']);
        Route::post('/kh/cash', [KelasHubEngineController::class, 'storeCashLedger']);
        Route::post('/kh/student', [KelasHubEngineController::class, 'storeStudent']);
        Route::delete('/kh/subject/{id}', [KelasHubEngineController::class, 'deleteSubject']);
        Route::delete('/kh/student/{id}', [KelasHubEngineController::class, 'deleteStudent']);
    });

    // Routes khusus Ketua Kelas
    Route::middleware(['role:ketua_kelas'])->group(function () {
        Route::post('/kh/validate', [KelasHubEngineController::class, 'validateData']);
        Route::get('/kh/cron/reset-schedule', function (Request $request) {
            \App\Models\AcademicSchedule::truncate();
            return response()->json(['success' => true, 'message' => 'Academic schedule reset successfully']);
        });

        // Super Admin Management
        Route::post('/kh/class', [KelasHubEngineController::class, 'storeUnifiedClass']);
        Route::post('/kh/student/{id}/role', [KelasHubEngineController::class, 'updateStudentRole']);

        Route::get('/kh/api/dashboard-data', [KelasHubEngineController::class, 'getDashboardData']);
    });

    // Fitur Pelaporan (Baru)
    Route::get('/kh/reports/attendance/pdf', [\App\Http\Controllers\ReportController::class, 'exportAttendancePdf'])->name('reports.attendance.pdf');
    Route::get('/kh/reports/attendance/excel', [\App\Http\Controllers\ReportController::class, 'exportAttendanceExcel'])->name('reports.attendance.excel');
    Route::get('/kh/reports/cash/pdf', [\App\Http\Controllers\ReportController::class, 'exportCashPdf'])->name('reports.cash.pdf');
    Route::get('/kh/reports/cash/excel', [\App\Http\Controllers\ReportController::class, 'exportCashExcel'])->name('reports.cash.excel');
});
