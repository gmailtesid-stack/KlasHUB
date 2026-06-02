<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = App\Models\Student::find(2);
if ($s) {
    $s->role = 'super_admin';
    $s->save();
    echo "Success: User " . $s->name . " is now " . $s->role . "\n";
} else {
    echo "Failed: User ID 2 not found.\n";
}
