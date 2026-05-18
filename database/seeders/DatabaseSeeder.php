<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\AcademicSchedule;
use App\Models\Assignment;
use App\Models\ClassAttendance;
use App\Models\CashLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Master Subjects
        $masterSubjects = [
            ['name' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'code' => '06TPLE013', 'default_lecturer' => 'ULIYATUNISA S.Kom., M.Kom.'],
            ['name' => 'Kerja Praktek', 'sks' => 2, 'code' => '06TPLE013', 'default_lecturer' => 'SUTRIYONO S.KOM., M.KOM.'],
            ['name' => 'Teknologi Internet of Things', 'sks' => 2, 'code' => '06TPLE013', 'default_lecturer' => 'JULI GUNAWAN S.T., M.Kom.'],
            ['name' => 'Pemrograman II', 'sks' => 3, 'code' => '06TPLE013', 'default_lecturer' => 'DAWAM AGUNG PRIBADI'],
            ['name' => 'Basis Data II', 'sks' => 3, 'code' => '06TPLE013', 'default_lecturer' => 'ACHMAD LUTFI FUADI S.Kom'],
            ['name' => 'Mobile Programming', 'sks' => 3, 'code' => '06TPLE013', 'default_lecturer' => 'TIO ANDRIAN S.T., M.KOM.'],
            ['name' => 'Sistem Pendukung Keputusan', 'sks' => 2, 'code' => '06TPLE013', 'default_lecturer' => 'ACHMAD SEHAN S.Kom'],
            ['name' => 'Teknik Kompilasi', 'sks' => 2, 'code' => '06TPLE013', 'default_lecturer' => 'ANIS MIRZA S.Kom, M.Kom'],
        ];

        foreach ($masterSubjects as $ms) {
            \App\Models\MasterSubject::create($ms);
        }

        // 2. Create Students (Kelas Karyawan Sabtu)
        $students = [
            ['nim' => '231011402802', 'name' => 'ARFIANNISA KAYLA', 'role' => 'mahasiswa'],
            ['nim' => '231011403268', 'name' => 'ARIYAS PRATAMA RAMADHAN', 'role' => 'ketua_kelas', 'password' => '231011403268KK'],
            ['nim' => '231011402460', 'name' => 'ARYA HAIDAR SARIFUDIN', 'role' => 'mahasiswa'],
            ['nim' => '231011402845', 'name' => 'AZAY AGUSTIAN', 'role' => 'mahasiswa'],
            ['nim' => '231011403640', 'name' => 'DEDE SANDI', 'role' => 'mahasiswa'],
            ['nim' => '231011402314', 'name' => 'DORRA LADY AFISHE', 'role' => 'bendahara', 'password' => '231011402314BD'],
            ['nim' => '231011402466', 'name' => 'DWI FITRIA ALFIANI', 'role' => 'mahasiswa'],
            ['nim' => '231011402430', 'name' => 'FACHRY RAMADHAN', 'role' => 'mahasiswa'],
            ['nim' => '231011402157', 'name' => 'FARIQ ITSBAT ALFARIDZI', 'role' => 'mahasiswa'],
            ['nim' => '231011401383', 'name' => 'HANA RIFDAH RIANRA', 'role' => 'mahasiswa'],
            ['nim' => '231011401982', 'name' => 'IHSAN MAULANA', 'role' => 'mahasiswa'],
            ['nim' => '231011403196', 'name' => 'JUAN MONTOYA DARMAWAN', 'role' => 'mahasiswa'],
            ['nim' => '231011400980', 'name' => 'MOCHAMAD FICKRY SATRIA', 'role' => 'sekretaris', 'password' => '231011400980SK'],
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
            Student::create([
                'nim' => $student['nim'],
                'name' => $student['name'],
                'password' => Hash::make($student['password'] ?? $student['nim']),
                'role' => $student['role'],
                'device_id' => 'DEV-' . rand(1000, 9999),
            ]);
        }
    }
}
