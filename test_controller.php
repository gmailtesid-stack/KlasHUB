<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\Student;

$user = Student::first();
if (!$user) {
    echo "No user found to test.";
    exit;
}
Auth::login($user);

try {
    $controller = new \App\Http\Controllers\DashboardController();
    $result = $controller->getStudentDashboard();
    echo "SUCCESS: Dashboard rendered successfully!\n";
} catch (\Exception $e) {
    echo "ERROR CAUGHT: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString();
}
