<?php
$models = ['AcademicSchedule', 'Assignment', 'CashLedger', 'ClassAttendance', 'LearningModule', 'MasterSubject', 'Notification', 'Student'];
foreach ($models as $m) {
    if (!file_exists("app/Models/$m.php"))
        continue;
    $c = file_get_contents("app/Models/$m.php");
    if (strpos($c, 'BelongsToClass') === false) {
        $c = str_replace("namespace App\Models;\n", "namespace App\Models;\n\nuse App\Http\Traits\BelongsToClass;\n", $c);

        if (strpos($c, 'use HasFactory, Notifiable;') !== false) {
            $c = str_replace('use HasFactory, Notifiable;', 'use HasFactory, Notifiable, BelongsToClass;', $c);
        } else if (strpos($c, 'use HasFactory;') !== false) {
            $c = str_replace('use HasFactory;', 'use HasFactory, BelongsToClass;', $c);
        } else {
            $c = preg_replace('/(class\s+[a-zA-Z]+\s+extends\s+[a-zA-Z]+[^{]*\{)/', "$1\n    use BelongsToClass;\n", $c);
        }
        file_put_contents("app/Models/$m.php", $c);
    }
}
echo "Trait diinjeksi sukses: " . implode(", ", $models);
