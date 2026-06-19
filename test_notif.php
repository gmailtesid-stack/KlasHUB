<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$students = \App\Models\Student::whereNotNull('onesignal_id')->where('onesignal_id', '!=', '')->get();
if ($students->count() > 0) {
    foreach ($students as $s) {
        \App\Services\NotificationService::notifyStudent($s->id, '🚀 Tes Sistem: Aplikasi KelasHUB sudah tersambung dengan server!');
        echo "Sent to " . $s->name . " (Player ID: " . $s->onesignal_id . ")\n";
    }
} else {
    echo "TIDAK ADA MAHASISWA DENGAN ONESIGNAL_ID. APK kemungkinan belum melakukan login, atau fitur push diblokir oleh HP.";
}
