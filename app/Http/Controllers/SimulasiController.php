<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulasiController extends Controller
{
    /**
     * Jalankan simulasi aktivitas kelas (Optimasi Vercel < 5 detik).
     */
    public function jalankanSimulasi()
    {
        // Set timeout sedikit di atas target
        set_time_limit(10);

        $endTime = time() + 4; // Target di bawah 5 detik
        $insertedCount = 0;
        $classIds = [1, 2, 3, 4, 5];

        while (time() < $endTime) {
            $classId = $classIds[array_rand($classIds)];
            $action = rand(1, 4);

            try {
                switch ($action) {
                    case 1: // Input Tugas
                        DB::table('assignments')->insert([
                            'class_id' => $classId,
                            'subject_name' => 'Simulasi Matkul ' . rand(1, 10),
                            'title' => 'Tugas Otomatis ' . rand(100, 999),
                            'description' => 'Deskripsi simulasi aktivitas kelas.',
                            'deadline' => now()->addDays(rand(1, 7)),
                            'type' => rand(0, 1) ? 'individual' : 'group',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        break;

                    case 2: // Input Kas
                        DB::table('cash_ledgers')->insert([
                            'class_id' => $classId,
                            'student_id' => null,
                            'type' => rand(0, 1) ? 'income' : 'expense',
                            'amount' => rand(1000, 50000),
                            'description' => 'Transaksi simulasi otomatis',
                            'transaction_date' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        break;

                    case 3: // Input/Update Modul/Matkul
                        DB::table('learning_modules')->insert([
                            'class_id' => $classId,
                            'subject_name' => 'Matkul ' . rand(1, 5),
                            'title' => 'Modul Simulasi ' . rand(1, 100),
                            'type' => 'link',
                            'link_url' => 'https://example.com/simulasi',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        break;

                    case 4: // Log Notifikasi
                        DB::table('notifications')->insert([
                            'class_id' => $classId,
                            'message' => 'Mahasiswa simulasi membaca notifikasi ' . rand(100, 999),
                            'is_read' => true,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        break;
                }
                $insertedCount++;
            } catch (\Exception $e) {
                // Silently ignore to keep the loop tight
            }

            // Bernapas sebentar tapi tetap di bawah 5 detik total
            usleep(500000); // 0.5 detik
        }

        return response()->json([
            'success' => true,
            'message' => 'Simulasi selesai dalam rentang < 5 detik',
            'total_inserted' => $insertedCount,
            'environment' => 'Vercel Optimized'
        ]);
    }
}
