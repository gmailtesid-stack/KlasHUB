<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\KelasHubEngineController;
use App\Models\Student;

Route::get('/', function () {
    return redirect()->route('login');
});

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

Route::get('/debug-config', function () {
    $config = config('database.connections.mysql');
    if (isset($config['password'])) {
        $config['password'] = 'REDACTED';
    }
    return response()->json($config);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [KelasHubEngineController::class, 'getStudentDashboard'])->name('dashboard');
    Route::post('/api/schedule/toggle-delivery', [KelasHubEngineController::class, 'toggleDeliveryType']);
    Route::post('/api/schedule', [KelasHubEngineController::class, 'storeSchedule']);
    Route::post('/api/master-subject', [KelasHubEngineController::class, 'storeMasterSubject']);
    Route::post('/api/assignment', [KelasHubEngineController::class, 'storeAssignment']);
    Route::post('/api/module', [KelasHubEngineController::class, 'storeModule']);
    Route::post('/api/cash', [KelasHubEngineController::class, 'storeCashLedger']);
    Route::post('/api/student', [KelasHubEngineController::class, 'storeStudent']);
    Route::post('/api/attendance', [KelasHubEngineController::class, 'storeAttendance']);
    Route::post('/api/password', [KelasHubEngineController::class, 'updatePassword']);
    Route::post('/api/validate', [KelasHubEngineController::class, 'validateData']);
    Route::delete('/api/subject/{id}', [KelasHubEngineController::class, 'deleteSubject']);
    Route::delete('/api/student/{id}', [KelasHubEngineController::class, 'deleteStudent']);
});
