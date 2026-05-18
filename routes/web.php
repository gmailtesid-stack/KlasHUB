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

Route::get('/debug-db', function () {
    $results = [];
    $results['php_version'] = phpversion();
    $results['openssl_version'] = OPENSSL_VERSION_TEXT;
    
    $caPath = base_path('cacert.pem');
    $results['ca_base_exists'] = file_exists($caPath);
    $results['ca_base_readable'] = is_readable($caPath);
    $results['ca_base_size'] = file_exists($caPath) ? filesize($caPath) : 0;
    
    $tmpCaPath = '/tmp/cacert.pem';
    $results['ca_tmp_exists'] = file_exists($tmpCaPath);
    $results['ca_tmp_readable'] = is_readable($tmpCaPath);
    $results['ca_tmp_size'] = file_exists($tmpCaPath) ? filesize($tmpCaPath) : 0;
    
    // Try PDO connection with base path
    try {
        $pdo = new PDO(
            'mysql:host=gateway01.ap-southeast-1.prod.aws.tidbcloud.com;port=4000;dbname=kelashub',
            'sBTXC6n9rnshkvy.root',
            '2a3zK3oA9d75P3a!',
            [
                PDO::MYSQL_ATTR_SSL_CA => $caPath,
            ]
        );
        $results['pdo_base_success'] = true;
    } catch (\Exception $e) {
        $results['pdo_base_success'] = false;
        $results['pdo_base_error'] = $e->getMessage();
    }
    
    // Try PDO connection with tmp path
    try {
        $pdo = new PDO(
            'mysql:host=gateway01.ap-southeast-1.prod.aws.tidbcloud.com;port=4000;dbname=kelashub',
            'sBTXC6n9rnshkvy.root',
            '2a3zK3oA9d75P3a!',
            [
                PDO::MYSQL_ATTR_SSL_CA => $tmpCaPath,
            ]
        );
        $results['pdo_tmp_success'] = true;
    } catch (\Exception $e) {
        $results['pdo_tmp_success'] = false;
        $results['pdo_tmp_error'] = $e->getMessage();
    }
    
    // Try PDO connection with Linux native path
    try {
        $nativePath = file_exists('/etc/pki/tls/certs/ca-bundle.crt') ? '/etc/pki/tls/certs/ca-bundle.crt' : '/etc/ssl/certs/ca-certificates.crt';
        $results['native_path_used'] = $nativePath;
        $pdo = new PDO(
            'mysql:host=gateway01.ap-southeast-1.prod.aws.tidbcloud.com;port=4000;dbname=kelashub',
            'sBTXC6n9rnshkvy.root',
            '2a3zK3oA9d75P3a!',
            [
                PDO::MYSQL_ATTR_SSL_CA => $nativePath,
            ]
        );
        $results['pdo_native_success'] = true;
    } catch (\Exception $e) {
        $results['pdo_native_success'] = false;
        $results['pdo_native_error'] = $e->getMessage();
    }
    
    return response()->json($results);
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
