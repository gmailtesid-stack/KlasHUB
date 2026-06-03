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
            // Pengurus Inti & Super Admin
            ['nim' => '231011403268', 'name' => 'ARIYAS PRATAMA RAMADHAN', 'role' => 'super_admin', 'pw' => '231011403268KK'],
            ['nim' => '231011402314', 'name' => 'DORRA LADY AFISHE', 'role' => 'bendahara', 'pw' => '231011402314BD'],
            ['nim' => '231011401386', 'name' => 'MOH FADEL FARISTA', 'role' => 'sekretaris', 'pw' => '231011401386SK'],

            // Mahasiswa Reguler
            ['nim' => '231011402802', 'name' => 'ARFIANNISA KAYLA', 'role' => 'mahasiswa', 'pw' => '231011402802'],
            ['nim' => '231011402460', 'name' => 'ARYA HAIDAR SARIFUDIN', 'role' => 'mahasiswa', 'pw' => '231011402460'],
            ['nim' => '231011402845', 'name' => 'AZAY AGUSTIAN', 'role' => 'mahasiswa', 'pw' => '231011402845'],
            ['nim' => '231011400535', 'name' => 'BIMO MUSTHAFA ABDILLAH', 'role' => 'mahasiswa', 'pw' => '231011400535'],
            ['nim' => '231011403640', 'name' => 'DEDE SANDI', 'role' => 'mahasiswa', 'pw' => '231011403640'],
            ['nim' => '231011402466', 'name' => 'DWI FITRIA ALFIANI', 'role' => 'mahasiswa', 'pw' => '231011402466'],
            ['nim' => '231011402430', 'name' => 'FACHRY RAMADHAN', 'role' => 'mahasiswa', 'pw' => '231011402430'],
            ['nim' => '231011402157', 'name' => 'FARIQ ITSBAT ALFARIDZI', 'role' => 'mahasiswa', 'pw' => '231011402157'],
            ['nim' => '231011401383', 'name' => 'HANA RIFDAH RIANRA', 'role' => 'mahasiswa', 'pw' => '231011401383'],
            ['nim' => '231011401982', 'name' => 'IHSAN MAULANA', 'role' => 'mahasiswa', 'pw' => '231011401982'],
            ['nim' => '231011403196', 'name' => 'JUAN MONTOYA DARMAWAN', 'role' => 'mahasiswa', 'pw' => '231011403196'],
            ['nim' => '231011400980', 'name' => 'MOCHAMAD FICKRY SATRIA', 'role' => 'mahasiswa', 'pw' => '231011400980'],
            ['nim' => '231011401602', 'name' => 'MUHAMAD ARIO ISNANDAR', 'role' => 'mahasiswa', 'pw' => '231011401602'],
            ['nim' => '231011401601', 'name' => 'MUHAMAD KHAERULLAH', 'role' => 'mahasiswa', 'pw' => '231011401601'],
            ['nim' => '231011403096', 'name' => 'MUHAMAD LUTFI AZIZAN', 'role' => 'mahasiswa', 'pw' => '231011403096'],
            ['nim' => '231011402859', 'name' => 'MUHAMMAD BIMO TRI NUGROHO', 'role' => 'mahasiswa', 'pw' => '231011402859'],
            ['nim' => '231011400772', 'name' => 'MUHAMMAD FAHMI', 'role' => 'mahasiswa', 'pw' => '231011400772'],
            ['nim' => '231011400815', 'name' => 'MUHAMMAD ILHAM', 'role' => 'mahasiswa', 'pw' => '231011400815'],
            ['nim' => '231011403288', 'name' => 'MUHAMMAD NAJHAN TSAANI', 'role' => 'mahasiswa', 'pw' => '231011403288'],
            ['nim' => '231011400775', 'name' => 'NAJWA MASAYU AZZAHRA', 'role' => 'mahasiswa', 'pw' => '231011400775'],
            ['nim' => '231011402280', 'name' => 'OLIVIA RAMADHANI', 'role' => 'mahasiswa', 'pw' => '231011402280'],
            ['nim' => '231011400795', 'name' => 'PASCAL ISMAIL', 'role' => 'mahasiswa', 'pw' => '231011400795'],
            ['nim' => '231011401598', 'name' => 'PUTRI AMALIA', 'role' => 'mahasiswa', 'pw' => '231011401598'],
            ['nim' => '231011401384', 'name' => 'RAFI MUHAMMAD LUTHFI', 'role' => 'mahasiswa', 'pw' => '231011401384'],
            ['nim' => '231011400785', 'name' => 'RAGIL FADHILAH AKHDAN', 'role' => 'mahasiswa', 'pw' => '231011400785'],
            ['nim' => '231011403294', 'name' => 'REVRICO RAMADHINO IRZAN', 'role' => 'mahasiswa', 'pw' => '231011403294'],
            ['nim' => '231011401590', 'name' => 'RIZKI ARTINIO PERMANA PUTRA', 'role' => 'mahasiswa', 'pw' => '231011401590'],
            ['nim' => '231011400813', 'name' => 'SEPTIANO ALVIAN ISMAU', 'role' => 'mahasiswa', 'pw' => '231011400813'],
            ['nim' => '221011402323', 'name' => 'SYAID AGIL AL MUNAWAR', 'role' => 'mahasiswa', 'pw' => '221011402323'],
            ['nim' => '231011400761', 'name' => 'WISNU SAPUTRA', 'role' => 'mahasiswa', 'pw' => '231011400761'],
            ['nim' => '231011402258', 'name' => 'YASIN KAMIL', 'role' => 'mahasiswa', 'pw' => '231011402258'],
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
