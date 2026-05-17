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
        // 1. Create Students
        $students = [
            ['nim' => '2341720001', 'name' => 'Budi Santoso', 'role' => 'ketua_kelas'],
            ['nim' => '2341720002', 'name' => 'Siti Aminah', 'role' => 'sekretaris'],
            ['nim' => '2341720003', 'name' => 'Andi Wijaya', 'role' => 'bendahara'],
            ['nim' => '2341720004', 'name' => 'Riko Dinata', 'role' => 'mahasiswa'],
            ['nim' => '2341720005', 'name' => 'Dewi Lestari', 'role' => 'mahasiswa'],
            
            // Kelas Karyawan Sabtu
            ['nim' => '231011402802', 'name' => 'ARFIANNISA KAYLA', 'role' => 'mahasiswa'],
            ['nim' => '231011403268', 'name' => 'ARIYAS PRATAMA RAMADHAN', 'role' => 'mahasiswa'],
            ['nim' => '231011402460', 'name' => 'ARYA HAIDAR SARIFUDIN', 'role' => 'mahasiswa'],
            ['nim' => '231011402845', 'name' => 'AZAY AGUSTIAN', 'role' => 'mahasiswa'],
            ['nim' => '231011403640', 'name' => 'DEDE SANDI', 'role' => 'mahasiswa'],
            ['nim' => '231011402314', 'name' => 'DORRA LADY AFISHE', 'role' => 'mahasiswa'],
            ['nim' => '231011402466', 'name' => 'DWI FITRIA ALFIANI', 'role' => 'mahasiswa'],
            ['nim' => '231011402430', 'name' => 'FACHRY RAMADHAN', 'role' => 'mahasiswa'],
            ['nim' => '231011402157', 'name' => 'FARIQ ITSBAT ALFARIDZI', 'role' => 'mahasiswa'],
            ['nim' => '231011401383', 'name' => 'HANA RIFDAH RIANRA', 'role' => 'mahasiswa'],
            ['nim' => '231011401982', 'name' => 'IHSAN MAULANA', 'role' => 'mahasiswa'],
            ['nim' => '231011403196', 'name' => 'JUAN MONTOYA DARMAWAN', 'role' => 'mahasiswa'],
            ['nim' => '231011400980', 'name' => 'MOCHAMAD FICKRY SATRIA', 'role' => 'mahasiswa'],
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
                'password' => Hash::make('password123'),
                'role' => $student['role'],
                'device_id' => 'DEV-' . rand(1000, 9999),
            ]);
        }

        // 2. Create Academic Schedules
        AcademicSchedule::create([
            'subject_name' => 'Mobile Programming',
            'lecturer_name' => 'Pak Dika',
            'day' => 'Senin',
            'time_start' => '08:00:00',
            'time_end' => '10:30:00',
            'room' => 'Lab R.304'
        ]);
        
        AcademicSchedule::create([
            'subject_name' => 'Rekayasa Perangkat Lunak',
            'lecturer_name' => 'Bu Rina',
            'day' => 'Senin',
            'time_start' => '10:30:00',
            'time_end' => '13:00:00',
            'room' => 'Ruang L.101'
        ]);

        // 3. Create Assignments
        Assignment::create([
            'title' => 'Project WebView App',
            'description' => 'Membuat aplikasi Hybrid berbasis Laravel dan Alpine.js.',
            'deadline' => Carbon::now()->addDays(2),
            'material_link' => 'https://drive.google.com/example',
            'type' => 'group'
        ]);

        // 4. Create Cash Ledger (Income and Expense)
        CashLedger::create([
            'student_id' => 1,
            'type' => 'income',
            'amount' => 50000,
            'description' => 'Iuran Kas Bulan Ini',
            'transaction_date' => Carbon::now()->subDays(2)
        ]);

        CashLedger::create([
            'student_id' => null,
            'type' => 'expense',
            'amount' => 15000,
            'description' => 'Beli Spidol dan Penghapus',
            'transaction_date' => Carbon::now()
        ]);

        // 5. Create Class Attendances for testing "Sisa Nyawa" and "Cekal"
        // Let's make Andi Wijaya (ID: 3) have 2 Alfa (Aman) in Mobile Programming
        ClassAttendance::create(['student_id' => 3, 'subject_name' => 'Mobile Programming', 'attendance_date' => Carbon::now()->subDays(14), 'status' => 'Alfa']);
        ClassAttendance::create(['student_id' => 3, 'subject_name' => 'Mobile Programming', 'attendance_date' => Carbon::now()->subDays(7), 'status' => 'Alfa']);
        
        // Let's make Riko Dinata (ID: 4) have 3 Alfa (Cekal) in RPL
        ClassAttendance::create(['student_id' => 4, 'subject_name' => 'Rekayasa Perangkat Lunak', 'attendance_date' => Carbon::now()->subDays(21), 'status' => 'Alfa']);
        ClassAttendance::create(['student_id' => 4, 'subject_name' => 'Rekayasa Perangkat Lunak', 'attendance_date' => Carbon::now()->subDays(14), 'status' => 'Alfa']);
        ClassAttendance::create(['student_id' => 4, 'subject_name' => 'Rekayasa Perangkat Lunak', 'attendance_date' => Carbon::now()->subDays(7), 'status' => 'Alfa']);
    }
}
