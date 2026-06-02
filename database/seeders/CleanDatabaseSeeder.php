<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\AcademicSchedule;
use App\Models\MasterSubject;
use App\Models\Assignment;
use App\Models\ClassAttendance;
use App\Models\CashLedger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CleanDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to allow truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Truncate all tables
        DB::table('notifications')->truncate();
        DB::table('learning_modules')->truncate();
        \App\Models\AcademicClass::truncate();
        \App\Models\Student::truncate();
        \App\Models\AcademicSchedule::truncate();
        \App\Models\MasterSubject::truncate();
        \App\Models\Assignment::truncate();
        \App\Models\ClassAttendance::truncate();
        \App\Models\CashLedger::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Seed Academic Class
        $class = \App\Models\AcademicClass::create([
            'name' => 'TPLE013 (Reguler C)',
            'code' => 'TPLE-013',
            'academic_year' => '2023/2024',
            'department' => 'Teknik Informatika'
        ]);

        // 3. Seed 35 Saturday Class Students
        $students = [
            ['nim' => '231011402802', 'name' => 'ARFIANNISA KAYLA', 'role' => 'mahasiswa'],
            ['nim' => '231011403268', 'name' => 'ARIYAS PRATAMA RAMADHAN', 'role' => 'ketua_kelas'],
            ['nim' => '231011402460', 'name' => 'ARYA HAIDAR SARIFUDIN', 'role' => 'mahasiswa'],
            ['nim' => '231011402845', 'name' => 'AZAY AGUSTIAN', 'role' => 'mahasiswa'],
            ['nim' => '231011403640', 'name' => 'DEDE SANDI', 'role' => 'mahasiswa'],
            ['nim' => '231011402314', 'name' => 'DORRA LADY AFISHE', 'role' => 'bendahara'],
            ['nim' => '231011402466', 'name' => 'DWI FITRIA ALFIANI', 'role' => 'mahasiswa'],
            ['nim' => '231011402430', 'name' => 'FACHRY RAMADHAN', 'role' => 'mahasiswa'],
            ['nim' => '231011402157', 'name' => 'FARIQ ITSBAT ALFARIDZI', 'role' => 'mahasiswa'],
            ['nim' => '231011401383', 'name' => 'HANA RIFDAH RIANRA', 'role' => 'mahasiswa'],
            ['nim' => '231011401982', 'name' => 'IHSAN MAULANA', 'role' => 'mahasiswa'],
            ['nim' => '231011403196', 'name' => 'JUAN MONTOYA DARMAWAN', 'role' => 'mahasiswa'],
            ['nim' => '231011400980', 'name' => 'MOCHAMAD FICKRY SATRIA', 'role' => 'sekretaris'],
            ['nim' => '231011401386', 'name' => 'MOH FADEL FARISTA', 'role' => 'mahasiswa'],
            ['nim' => '231011401602', 'name' => 'MUHAMAD ARIO ISNANDAR', 'role' => 'mahasiswa'],
            ['nim' => '231011401601', 'name' => 'MUHAMAD KHAERULLAH', 'role' => 'mahasiswa'],
            ['nim' => '231011403096', 'name' => 'MUHAMAD LUTFI AZIZAN', 'role' => 'mahasiswa'],
            ['nim' => '211011400343', 'name' => 'MUHAMMAD ABDUL ROZAQ', 'role' => 'mahasiswa'],
            ['nim' => '231011402859', 'name' => 'MUHAMMAD BIMO TRI NUGROHO', 'role' => 'mahasiswa'],
            ['nim' => '231011400772', 'name' => 'MUHAMMAD FAHMI', 'role' => 'mahasiswa'],
            ['nim' => '231011400815', 'name' => 'MUHAMMAD ILHAM', 'role' => 'mahasiswa'],
            ['nim' => '231011403288', 'name' => 'MUHAMMAD NAJHAN TSAANI', 'role' => 'mahasiswa'],
            ['nim' => '211011450342', 'name' => 'MUHAMMAD RIZKY ALBARAS', 'role' => 'mahasiswa'],
            ['nim' => '231011400775', 'name' => 'NAJWA MASAYU AZZAHRA', 'role' => 'mahasiswa'],
            ['nim' => '231011402280', 'name' => 'OLIVIA RAMADHANI', 'role' => 'mahasiswa'],
            ['nim' => '231011400795', 'name' => 'PASCAL ISMAIL', 'role' => 'mahasiswa'],
            ['nim' => '231011401598', 'name' => 'PUTRI AMALIA', 'role' => 'mahasiswa'],
            ['nim' => '231011401384', 'name' => 'RAFI MUHAMMAD LUTHFI', 'role' => 'mahasiswa'],
            ['nim' => '231011400785', 'name' => 'RAGIL FADHILAH AKHDAN', 'role' => 'mahasiswa'],
            ['nim' => '231011403294', 'name' => 'EVRICO RAMADHINO IRZAN', 'role' => 'mahasiswa'],
            ['nim' => '231011401590', 'name' => 'RIZKI ARTINIO PERMANA PUTRA', 'role' => 'mahasiswa'],
            ['nim' => '231011400813', 'name' => 'SEPTIANO ALVIAN ISMAU', 'role' => 'mahasiswa'],
            ['nim' => '221011402323', 'name' => 'SYAID AGIL AL MUNAWAR', 'role' => 'mahasiswa'],
            ['nim' => '231011400761', 'name' => 'WISNU SAPUTRA', 'role' => 'mahasiswa'],
            ['nim' => '231011402258', 'name' => 'YASIN KAMIL', 'role' => 'mahasiswa'],
        ];

        foreach ($students as $student) {
            $passwordVal = $student['nim'];
            if ($student['role'] === 'ketua_kelas') {
                $passwordVal .= 'KK';
            } elseif ($student['role'] === 'sekretaris') {
                $passwordVal .= 'SK';
            } elseif ($student['role'] === 'bendahara') {
                $passwordVal .= 'BD';
            }

            Student::create([
                'class_id' => $class->id,
                'nim' => $student['nim'],
                'name' => $student['name'],
                'password' => Hash::make($passwordVal),
                'role' => $student['role'],
                'device_id' => 'DEV-' . rand(1000, 9999),
            ]);
        }

        // 4. Seed Academic Schedules
        AcademicSchedule::create([
            'class_id' => $class->id,
            'subject_name' => 'Mobile Programming',
            'subject_code' => 'MP-101',
            'lecturer_name' => 'TIO ANDRIAN S.T., M.KOM.',
            'day' => 'Sabtu',
            'time_start' => '07:30:00',
            'time_end' => '10:00:00',
            'room' => 'Lab R.304',
            'class_name' => 'TPLE013'
        ]);

        AcademicSchedule::create([
            'class_id' => $class->id,
            'subject_name' => 'Rekayasa Perangkat Lunak',
            'subject_code' => 'RPL-202',
            'lecturer_name' => 'ULIYATUNISA S.Kom., M.Kom.',
            'day' => 'Sabtu',
            'time_start' => '10:00:00',
            'time_end' => '12:30:00',
            'room' => 'Ruang L.401',
            'class_name' => 'TPLE013'
        ]);

        // 5. Seed 8 Master Subjects
        $matkuls = [
            ['name' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'lecturer' => 'ULIYATUNISA S.Kom., M.Kom.'],
            ['name' => 'Kerja Praktek', 'sks' => 2, 'lecturer' => 'SUTRIYONO S.KOM., M.KOM.'],
            ['name' => 'Teknologi Internet of Things', 'sks' => 2, 'lecturer' => 'JULI GUNAWAN S.T., M.Kom.'],
            ['name' => 'Pemrograman II', 'sks' => 3, 'lecturer' => 'DAWAM AGUNG PRIBADI'],
            ['name' => 'Basis Data II', 'sks' => 3, 'lecturer' => 'ACHMAD LUTFI FUADI S.Kom'],
            ['name' => 'Mobile Programming', 'sks' => 3, 'lecturer' => 'TIO ANDRIAN S.T., M.KOM.'],
            ['name' => 'Sistem Pendukung Keputusan', 'sks' => 2, 'lecturer' => 'ACHMAD SEHAN S.Kom'],
            ['name' => 'Teknik Kompilasi', 'sks' => 2, 'lecturer' => 'ANIS MIRZA S.Kom, M.Kom']
        ];

        foreach ($matkuls as $m) {
            MasterSubject::create([
                'name' => $m['name'],
                'sks' => $m['sks'],
                'default_lecturer' => $m['lecturer']
            ]);
        }
    }
}
