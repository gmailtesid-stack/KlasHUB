<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\AcademicMaterialController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ClassManagementController;
use App\Http\Controllers\ValidationController;

// ROUTE DARURAT SEMENTARA: Reset Semua Password ke NIM+Suffix
Route::get('/kh/emergency-reset', function (Request $request) {
    $page = $request->query('page', 1);
    $limit = 10;

    // Ambil 10 siswa yang belum di-reset password-nya sesuai format baru
    // Kita filter di PHP karena bcrypt tidak bisa dicocokkan langsung di SQL
    $students = \App\Models\Student::offset(($page - 1) * $limit)->limit($limit)->get();

    if ($students->isEmpty()) {
        return "BERHASIL MEMULIHKAN KATA SANDI SECARA TOTAL.<br><br><b>ATURAN SANDI KINI TELAH DIKEMBALIKAN KE FORMAT STANDAR:</b><br>- Mahasiswa Biasa = NIM<br>- Pengurus Kelas = NIM + KK / SK / BD<br><br>Silakan coba Login kembali di aplikasi atau website.";
    }

    $count = 0;
    foreach ($students as $s) {
        $pw = $s->nim;
        if ($s->role === 'ketua_kelas' || $s->role === 'super_admin')
            $pw .= 'KK';
        elseif ($s->role === 'sekretaris')
            $pw .= 'SK';
        elseif ($s->role === 'bendahara')
            $pw .= 'BD';

        if (!\Illuminate\Support\Facades\Hash::check($pw, $s->password)) {
            $s->update(['password' => bcrypt($pw)]);
            $count++;
        }
    }

    $nextPage = $page + 1;
    return response("Memproses halaman $page ($count data)...<br><script>setTimeout(function(){ window.location.href='/kh/emergency-reset?page=$nextPage'; }, 1000);</script>");
});

Route::get('/kh/debug-database', function () {
    return response()->json([
        'master_subjects' => \App\Models\MasterSubject::withoutGlobalScopes()->get(['id', 'name', 'class_id']),
    ]);
});

Route::get('/kh/test-login', function () {
    $res = [];
    $res['ari_ketua'] = \Illuminate\Support\Facades\Auth::attempt(['nim' => '231011403268', 'password' => '231011403268KK']);
    $res['arf_mhs'] = \Illuminate\Support\Facades\Auth::attempt(['nim' => '231011402802', 'password' => '231011402802']);
    $res['dor_bendahara'] = \Illuminate\Support\Facades\Auth::attempt(['nim' => '231011402314', 'password' => '231011402314BD']);
    return response()->json($res);
});

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

    // Dukung login via NIM (angka) ATAU Nama Lengkap
    if (
        Auth::attempt(['nim' => $request->name, 'password' => $request->password]) ||
        Auth::attempt(['name' => $request->name, 'password' => $request->password])
    ) {

        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'name' => 'Nama atau Password (NIM+Kode) salah.',
    ])->onlyInput('name');
})->middleware('throttle:5,1');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::post('/kh/api/login', function (Request $request) {
    if (
        Auth::attempt(['nim' => $request->name, 'password' => $request->password]) ||
        Auth::attempt(['name' => $request->name, 'password' => $request->password])
    ) {

        $request->session()->regenerate();
        $user = Auth::user();
        return response()->json(['message' => 'Login successful', 'user' => $user->only(['id', 'name', 'nim', 'role', 'class_id'])]);
    }
    return response()->json(['message' => 'Invalid credentials'], 401);
})->middleware('throttle:10,1');

Route::post('/kh/api/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['message' => 'Logged out successfully']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getStudentDashboard'])->name('dashboard');
    Route::post('/kh/password', [DashboardController::class, 'updatePassword']);
    Route::post('/kh/notifications/read', [DashboardController::class, 'markNotificationAsRead']);
    Route::post('/kh/device-token', [DashboardController::class, 'updateDeviceToken']);
    Route::post('/kh/attendance', [AttendanceController::class, 'storeAttendance']);
    Route::get('/kh/api/attendance', [AttendanceController::class, 'getAttendance']);
    Route::get('/kh/api/schedule', [AcademicController::class, 'getSchedule']);
    Route::get('/kh/api/profile', [UserManagementController::class, 'getProfile']);
    Route::get('/kh/api/students', [UserManagementController::class, 'getAllStudents']);
    Route::get('/kh/api/pending-validations', [ValidationController::class, 'getPendingValidations']);
    Route::get('/kh/api/dashboard-data', [DashboardController::class, 'getDashboardData']);
    Route::post('/kh/cash', [FinanceController::class, 'storeCashLedger']);



    // Routes khusus Pengurus (Ketua Kelas, Sekretaris, Bendahara)
    Route::middleware(['role:ketua_kelas,sekretaris,bendahara'])->group(function () {
        Route::post('/kh/schedule/toggle-delivery', [AcademicController::class, 'toggleDeliveryType']);
        Route::post('/kh/schedule', [AcademicController::class, 'storeSchedule']);
        Route::post('/kh/master-subject', [AcademicController::class, 'storeMasterSubject']);
        Route::post('/kh/upload-qris', [ClassManagementController::class, 'uploadQris']);
        Route::delete('/kh/qris', [ClassManagementController::class, 'deleteQris']);
        Route::post('/kh/assignment', [AcademicMaterialController::class, 'storeAssignment']);
        Route::post('/kh/module', [AcademicMaterialController::class, 'storeModule']);
        Route::get('/kh/module/{id}/download', [AcademicMaterialController::class, 'downloadModule']);
        Route::post('/kh/student', [UserManagementController::class, 'storeStudent']);
        // CRUD Academic
        Route::put('/kh/master-subject/{id}', [AcademicController::class, 'updateMasterSubject']);
        Route::put('/kh/schedule/{id}', [AcademicController::class, 'updateSchedule']);
        Route::delete('/kh/schedule/{id}', [AcademicController::class, 'deleteSchedule']);

        // CRUD Material
        Route::put('/kh/assignment/{id}', [AcademicMaterialController::class, 'updateAssignment']);
        Route::delete('/kh/assignment/{id}', [AcademicMaterialController::class, 'deleteAssignment']);

        Route::put('/kh/module/{id}', [AcademicMaterialController::class, 'updateModule']);
        Route::delete('/kh/module/{id}', [AcademicMaterialController::class, 'deleteModule']);

        // CRUD Student (Name & NIM edits)
        Route::put('/kh/student/{id}', [UserManagementController::class, 'updateStudent']);
        Route::put('/kh/student/{id}/role', [UserManagementController::class, 'updateStudentRole']);

        Route::delete('/kh/subject/{id}', [AcademicController::class, 'deleteSubject']);
        Route::delete('/kh/student/{id}', [UserManagementController::class, 'deleteStudent']);
    });

    // Routes khusus Ketua Kelas
    Route::get('/kh/debug', function () {
        return response()->json([
            'master_subjects' => \App\Models\MasterSubject::count(),
            'students' => \App\Models\Student::count()
        ]);
    });

    Route::middleware(['role:ketua_kelas'])->group(function () {
        Route::post('/kh/validate', [ValidationController::class, 'validateData']);

        Route::get('/debug-blade', function () {
            $student = \App\Models\Student::where('role', 'bendahara')->first();
            Auth::login($student);
            return app()->call([\App\Http\Controllers\DashboardController::class, 'getStudentDashboard']);
        });

        // Vercel Cron Bypass logic using CRON_SECRET
        Route::get('/kh/cron/reset-schedule', function (Request $request) {
            $authHeader = $request->header('Authorization');
            $cronSecret = config('app.cron_secret') ?? '';

            $isValidHeader = $authHeader && str_starts_with($authHeader, 'Bearer ') && hash_equals($cronSecret, substr($authHeader, 7));
            $isValidQuery = $request->has('key') && hash_equals($cronSecret, (string) $request->query('key'));

            if (!$isValidHeader && !$isValidQuery) {
                if (!Auth::check() || Auth::user()->role !== 'ketua_kelas') {
                    abort(401, 'Unauthorized Cron Access');
                }
            }

            $classId = Auth::check() ? Auth::user()->class_id : null;
            if ($classId) {
                \App\Models\AcademicSchedule::where('class_id', $classId)->delete();
            }
            return response()->json(['success' => true, 'message' => 'Academic schedule reset successfully']);
        });

        // Super Admin Management
        Route::get('/kh/setup/migrate-db', function () {
            if (Auth::check() && in_array(Auth::user()->role, ['ketua_kelas', 'super_admin'])) {
                try {
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE academic_classes MODIFY qris_image LONGTEXT;');
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE cash_ledgers MODIFY proof_image LONGTEXT;');
                    return "Migration Sukses! Kolom berhasil di-upgrade ke LONGTEXT. Silakan kembali dan upload ulang.";
                } catch (\Exception $e) {
                    return "Error: " . $e->getMessage();
                }
            }
            return "Akses ditolak. Anda harus login sebagai Ketua Kelas.";
        });

        Route::post('/kh/class', [ClassManagementController::class, 'storeUnifiedClass']);
        Route::delete('/kh/class/{id}', [ClassManagementController::class, 'deleteClass']);
        Route::post('/kh/class/next-semester', [ClassManagementController::class, 'nextSemester']);
        Route::post('/kh/student/{id}/role', [UserManagementController::class, 'updateStudentRole']);
    });

    // Fitur Pelaporan
    Route::get('/kh/reports/attendance/pdf', [\App\Http\Controllers\ReportController::class, 'exportAttendancePdf'])->name('reports.attendance.pdf');
    Route::get('/kh/reports/attendance/excel', [\App\Http\Controllers\ReportController::class, 'exportAttendanceExcel'])->name('reports.attendance.excel');
    Route::get('/kh/reports/cash/pdf', [\App\Http\Controllers\ReportController::class, 'exportCashPdf'])->name('reports.cash.pdf');
    Route::get('/kh/reports/cash/excel', [\App\Http\Controllers\ReportController::class, 'exportCashExcel'])->name('reports.cash.excel');

    Route::get('/kh/debug/attendances', function () {
        return response()->json([
            'auth_class_id' => \Illuminate\Support\Facades\Auth::user()->class_id,
            'count_all' => \App\Models\ClassAttendance::count(),
            'count_where_class' => \App\Models\ClassAttendance::where('class_id', \Illuminate\Support\Facades\Auth::user()->class_id)->count(),
            'records' => \App\Models\ClassAttendance::where('class_id', \Illuminate\Support\Facades\Auth::user()->class_id)->get()
        ]);
    });
});

