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
        // 1. Ketua Kelas
        \App\Models\Student::updateOrCreate(
            ['nim' => '231011403268'],
            [
                'name' => 'ARIYAS PRATAMA RAMADHAN',
                'role' => 'ketua_kelas',
                'password' => bcrypt('231011403268KK')
            ]
        );

        // 2. Sekretaris
        \App\Models\Student::updateOrCreate(
            ['nim' => '231011403269'],
            [
                'name' => 'SEKRETARIS KELAS',
                'role' => 'sekretaris',
                'password' => bcrypt('231011403269SK')
            ]
        );

        // 3. Bendahara
        \App\Models\Student::updateOrCreate(
            ['nim' => '231011403270'],
            [
                'name' => 'BENDAHARA KELAS',
                'role' => 'bendahara',
                'password' => bcrypt('231011403270BD')
            ]
        );

        // 4. Regular Student
        \App\Models\Student::updateOrCreate(
            ['nim' => '231011403271'],
            [
                'name' => 'MAHASISWA BIASA',
                'role' => 'mahasiswa',
                'password' => bcrypt('231011403271')
            ]
        );

        // 5. Seed all 8 Matkuls into Schedules
        $matkuls = [
            ['nama' => 'Rekayasa Perangkat Lunak', 'dosen' => 'ULIYATUNISA S.Kom., M.Kom.', 'jam' => '07:30', 'ruang' => 'V.401', 'type' => 'offline'],
            ['nama' => 'Kerja Praktek', 'dosen' => 'SUTRIYONO S.KOM., M.KOM.', 'jam' => '10:00', 'ruang' => 'V.402', 'type' => 'offline'],
            ['nama' => 'Teknologi Internet of Things', 'dosen' => 'DR. AHMAD FUADI', 'jam' => '13:00', 'ruang' => 'L.201', 'type' => 'offline'],
            ['nama' => 'Pemrograman II', 'dosen' => 'HENDRA SURYA', 'jam' => '08:00', 'ruang' => 'V.305', 'type' => 'offline'],
            ['nama' => 'Basis Data II', 'dosen' => 'SITI NURJANAH', 'jam' => '10:30', 'ruang' => 'V.306', 'type' => 'online'],
            ['nama' => 'Mobile Programming', 'dosen' => 'PAK DIKA', 'jam' => '13:30', 'ruang' => 'LAB. KOMP', 'type' => 'online'],
            ['nama' => 'Sistem Pendukung Keputusan', 'dosen' => 'BU RINA', 'jam' => '15:30', 'ruang' => 'V.201', 'type' => 'online'],
            ['nama' => 'Teknik Kompilasi', 'dosen' => 'DR. IRWAN', 'jam' => '18:30', 'ruang' => 'V.202', 'type' => 'online'],
        ];

        foreach ($matkuls as $m) {
            \App\Models\AcademicSchedule::updateOrCreate(
                ['subject_name' => $m['nama']],
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
