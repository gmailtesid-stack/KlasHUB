<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UjiKomprehensifController extends Controller
{
    /**
     * Jalankan semua uji fitur dalam satu kali tembak (Satu-Klik).
     */
    public function jalankanSemuaUji()
    {
        $classId = 1; // Menggunakan kelas ID 1 (TPLE013)
        $studentId = DB::table('students')->where('nim', '231011403268')->value('id'); // ARIYAS sebagai subjek uji

        if (!$studentId)
            return response()->json(['error' => 'Student ARIYAS not found']);

        $res = [];

        try {
            // 1. Uji Upload Tugas
            $tugasId = DB::table('assignments')->insertGetId([
                'class_id' => $classId,
                'subject_name' => 'Uji Sistem Komprehensif',
                'title' => 'Tugas Uji Vercel ' . now()->timestamp,
                'description' => 'Materi pengujian fungsionalitas upload tugas.',
                'deadline' => now()->addDays(7),
                'type' => 'individual',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $res['upload_tugas'] = "SUCCESS (ID: $tugasId)";

            // 2. Uji Upload Modul
            $modulId = DB::table('learning_modules')->insertGetId([
                'class_id' => $classId,
                'subject_name' => 'Uji Sistem Komprehensif',
                'title' => 'Modul Uji Vercel ' . now()->timestamp,
                'type' => 'link',
                'link_url' => 'https://github.com/gmailtesid-stack/KlasHUB',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $res['upload_modul'] = "SUCCESS (ID: $modulId)";

            // 3. Uji Absensi & Notif 3x Alfa
            // Hapus absensi lama agar pas 3 kali
            DB::table('class_attendances')->where('student_id', $studentId)->where('subject_name', 'Uji Sistem Komprehensif')->delete();

            for ($i = 0; $i < 3; $i++) {
                DB::table('class_attendances')->insert([
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'subject_name' => 'Uji Sistem Komprehensif',
                    'attendance_date' => now()->subDays($i),
                    'status' => 'Alfa',
                    'is_validated' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $res['notif_3_alfa'] = "SUCCESS (3 Alfa Inserted for ARIYAS)";

            // 4. Uji Kas Input & Output
            $kasMasukId = DB::table('cash_ledgers')->insertGetId([
                'class_id' => $classId,
                'student_id' => $studentId,
                'type' => 'income',
                'amount' => 100000,
                'description' => 'Donasi Uji Sistem',
                'transaction_date' => now(),
                'is_validated' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $kasKeluarId = DB::table('cash_ledgers')->insertGetId([
                'class_id' => $classId,
                'student_id' => $studentId,
                'type' => 'expense',
                'amount' => 5000,
                'description' => 'Biaya Admin Uji',
                'transaction_date' => now(),
                'is_validated' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $res['kas_manajemen'] = "SUCCESS (IN: $kasMasukId, OUT: $kasKeluarId)";

            return response()->json([
                'status' => 'Testing Finalized',
                'results' => $res,
                'message' => 'Silakan cek Dashboard ARIYAS untuk melihat notifikasi DICEKAL dan data lainnya.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
