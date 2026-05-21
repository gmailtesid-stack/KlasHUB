<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasHubAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Academic Classes
        $class13 = \App\Models\AcademicClass::updateOrCreate(
            ['code' => '06TPLE013'],
            ['name' => 'Teknik Informatika - 06TPLE013', 'academic_year' => '2023/2024']
        );

        $class14 = \App\Models\AcademicClass::updateOrCreate(
            ['code' => '06TPLE014'],
            ['name' => 'Teknik Informatika - 06TPLE014', 'academic_year' => '2023/2024']
        );

        // 2. Seed Students for Class 06TPLE013
        $students13 = [
            ['nim' => '231011403268', 'name' => 'ARIYAS PRATAMA RAMADHAN', 'role' => 'super_admin', 'pw' => '231011403268KK'],
            ['nim' => '231011403269', 'name' => 'SEKRETARIS KELAS', 'role' => 'sekretaris', 'pw' => '231011403269SK'],
            ['nim' => '231011403270', 'name' => 'BENDAHARA KELAS', 'role' => 'bendahara', 'pw' => '231011403270BD'],
            ['nim' => '231011403271', 'name' => 'MAHASISWA BIASA', 'role' => 'mahasiswa', 'pw' => '231011403271'],
        ];

        foreach ($students13 as $s) {
            \App\Models\Student::updateOrCreate(
                ['nim' => $s['nim']],
                [
                    'name' => $s['name'],
                    'role' => $s['role'],
                    'password' => bcrypt($s['pw']),
                    'class_id' => $class13->id
                ]
            );
        }

        // 3. Seed Students for Class 06TPLE014 (Testing Isolation)
        $students14 = [
            ['nim' => '241011400001', 'name' => 'KETUA KELAS 14', 'role' => 'ketua_kelas', 'pw' => '241011400001KK'],
            ['nim' => '241011400002', 'name' => 'SISWA KELAS 14', 'role' => 'mahasiswa', 'pw' => '241011400002'],
        ];

        foreach ($students14 as $s) {
            \App\Models\Student::updateOrCreate(
                ['nim' => $s['nim']],
                [
                    'name' => $s['name'],
                    'role' => $s['role'],
                    'password' => bcrypt($s['pw']),
                    'class_id' => $class14->id
                ]
            );
        }

        // 4. Seed Schedules for Class 06TPLE013
        $matkuls = [
            ['nama' => 'Rekayasa Perangkat Lunak', 'dosen' => 'ULIYATUNISA S.Kom., M.Kom.', 'jam' => '07:30', 'ruang' => 'V.401', 'type' => 'offline'],
            ['nama' => 'Kerja Praktek', 'dosen' => 'SUTRIYONO S.KOM., M.KOM.', 'jam' => '10:00', 'ruang' => 'V.402', 'type' => 'offline'],
            ['nama' => 'Teknologi Internet of Things', 'dosen' => 'DR. AHMAD FUADI', 'jam' => '13:00', 'ruang' => 'L.201', 'type' => 'offline'],
        ];

        foreach ($matkuls as $m) {
            \App\Models\AcademicSchedule::updateOrCreate(
                ['subject_name' => $m['nama'], 'class_id' => $class13->id],
                [
                    'lecturer_name' => $m['dosen'],
                    'day' => 'Sabtu',
                    'time_start' => $m['jam'],
                    'time_end' => date('H:i', strtotime($m['jam'] . ' + 150 minutes')),
                    'room' => $m['ruang'],
                    'delivery_type' => $m['type'],
                    'is_validated' => true
                ]
            );
        }
    }
}
