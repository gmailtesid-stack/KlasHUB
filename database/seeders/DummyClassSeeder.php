<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyClassSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $semester = 1;

        // ========================================================
        // 1. BUAT KELAS AKADEMIK
        // ========================================================
        $existingClass = DB::table('academic_classes')->where('code', 'TPLE-013')->first();
        if ($existingClass) {
            $classId = $existingClass->id;
            // Bersihkan data lama untuk kelas ini agar tidak nyangkut/duplicate NIM
            DB::table('students')->where('class_id', $classId)->delete();
            DB::table('academic_schedules')->where('class_id', $classId)->delete();
            DB::table('assignments')->where('class_id', $classId)->delete();
            DB::table('master_subjects')->where('class_id', $classId)->delete();
            DB::table('cash_ledgers')->where('class_id', $classId)->delete();
            DB::table('class_attendances')->where('class_id', $classId)->delete();
            DB::table('notifications')->where('class_id', $classId)->delete();
        } else {
            $classId = DB::table('academic_classes')->insertGetId([
                'name' => 'Teknik Informatika - TPLE-013',
                'code' => 'TPLE-013',
                'academic_year' => '2025/2026',
                'semester_ke' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========================================================
        // 2. BUAT 30 MAHASISWA (3 Pengurus + 27 Biasa)
        // ========================================================
        $ketuaNim = '231011400001';
        $sekretNim = '231011400002';
        $bendNim = '231011400003';

        $studentRows = [
            // — PENGURUS KELAS —
            ['nim' => $ketuaNim, 'name' => 'RAFI ADITYA PRATAMA', 'role' => 'ketua_kelas', 'pw' => $ketuaNim . 'KK'],
            ['nim' => $sekretNim, 'name' => 'NABILA SARI RAHMAWATI', 'role' => 'sekretaris', 'pw' => $sekretNim . 'SK'],
            ['nim' => $bendNim, 'name' => 'DIMAS ARYO WICAKSONO', 'role' => 'bendahara', 'pw' => $bendNim . 'BD'],
            // — MAHASISWA BIASA (27 orang) —
            ['nim' => '231011400004', 'name' => 'AISYAH NUR FADILAH', 'role' => 'mahasiswa', 'pw' => '231011400004'],
            ['nim' => '231011400005', 'name' => 'BAYU SETYA NUGRAHA', 'role' => 'mahasiswa', 'pw' => '231011400005'],
            ['nim' => '231011400006', 'name' => 'CITRA DEWI LESTARI', 'role' => 'mahasiswa', 'pw' => '231011400006'],
            ['nim' => '231011400007', 'name' => 'DEVA ANANDA PUTRA', 'role' => 'mahasiswa', 'pw' => '231011400007'],
            ['nim' => '231011400008', 'name' => 'EKA PUTRI MAHARANI', 'role' => 'mahasiswa', 'pw' => '231011400008'],
            ['nim' => '231011400009', 'name' => 'FARHAN RIZKY MAULANA', 'role' => 'mahasiswa', 'pw' => '231011400009'],
            ['nim' => '231011400010', 'name' => 'GITA AMELIA SAFITRI', 'role' => 'mahasiswa', 'pw' => '231011400010'],
            ['nim' => '231011400011', 'name' => 'HABIB KURNIAWAN', 'role' => 'mahasiswa', 'pw' => '231011400011'],
            ['nim' => '231011400012', 'name' => 'INTAN PERMATA SARI', 'role' => 'mahasiswa', 'pw' => '231011400012'],
            ['nim' => '231011400013', 'name' => 'JOKO SUSILO PRASETYO', 'role' => 'mahasiswa', 'pw' => '231011400013'],
            ['nim' => '231011400014', 'name' => 'KARTIKA DEWI ANGGRAENI', 'role' => 'mahasiswa', 'pw' => '231011400014'],
            ['nim' => '231011400015', 'name' => 'LUKMAN HAKIM SAPUTRA', 'role' => 'mahasiswa', 'pw' => '231011400015'],
            ['nim' => '231011400016', 'name' => 'MEGA FITRIA HANDAYANI', 'role' => 'mahasiswa', 'pw' => '231011400016'],
            ['nim' => '231011400017', 'name' => 'NAUFAL ADHI NUGROHO', 'role' => 'mahasiswa', 'pw' => '231011400017'],
            ['nim' => '231011400018', 'name' => 'OKTAVIA RAMADHANI', 'role' => 'mahasiswa', 'pw' => '231011400018'],
            ['nim' => '231011400019', 'name' => 'PANDU WAHYU SEJATI', 'role' => 'mahasiswa', 'pw' => '231011400019'],
            ['nim' => '231011400020', 'name' => 'QURROTA AYUN NISA', 'role' => 'mahasiswa', 'pw' => '231011400020'],
            ['nim' => '231011400021', 'name' => 'RIZKI FAJAR RAMADHAN', 'role' => 'mahasiswa', 'pw' => '231011400021'],
            ['nim' => '231011400022', 'name' => 'SINTA BELLA OKTAVIANA', 'role' => 'mahasiswa', 'pw' => '231011400022'],
            ['nim' => '231011400023', 'name' => 'TAUFIK HIDAYAT', 'role' => 'mahasiswa', 'pw' => '231011400023'],
            ['nim' => '231011400024', 'name' => 'ULFA NUR AZIZAH', 'role' => 'mahasiswa', 'pw' => '231011400024'],
            ['nim' => '231011400025', 'name' => 'VINO ALDI PRATAMA', 'role' => 'mahasiswa', 'pw' => '231011400025'],
            ['nim' => '231011400026', 'name' => 'WULAN DARI ANJANI', 'role' => 'mahasiswa', 'pw' => '231011400026'],
            ['nim' => '231011400027', 'name' => 'XAVIER PUTRA MAHENDRA', 'role' => 'mahasiswa', 'pw' => '231011400027'],
            ['nim' => '231011400028', 'name' => 'YUNI KARTIKA SARI', 'role' => 'mahasiswa', 'pw' => '231011400028'],
            ['nim' => '231011400029', 'name' => 'ZAKI MUBAROK FIRDAUS', 'role' => 'mahasiswa', 'pw' => '231011400029'],
            ['nim' => '231011400030', 'name' => 'ADINDA PUTRI CAHYANI', 'role' => 'mahasiswa', 'pw' => '231011400030'],
        ];

        $studentIds = [];
        foreach ($studentRows as $s) {
            $id = DB::table('students')->insertGetId([
                'nim' => $s['nim'],
                'name' => $s['name'],
                'role' => $s['role'],
                'password' => Hash::make($s['pw']),
                'class_id' => $classId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $studentIds[$s['nim']] = $id;
        }

        $ketuaId = $studentIds[$ketuaNim];
        $sekretId = $studentIds[$sekretNim];
        $bendId = $studentIds[$bendNim];

        // ========================================================
        // 3. MATA KULIAH MASTER (8 Matkul Semester 5 TI)
        // ========================================================
        $subjects = [
            ['name' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'code' => 'TI501', 'lecturer' => 'ULIYATUNISA S.Kom., M.Kom.'],
            ['name' => 'Kerja Praktek', 'sks' => 2, 'code' => 'TI502', 'lecturer' => 'SUTRIYONO S.KOM., M.KOM.'],
            ['name' => 'Teknologi Internet of Things', 'sks' => 2, 'code' => 'TI503', 'lecturer' => 'DR. AHMAD FUADI S.T., M.T.'],
            ['name' => 'Pemrograman II', 'sks' => 3, 'code' => 'TI504', 'lecturer' => 'BUDI PRASETYO S.Kom., M.Cs.'],
            ['name' => 'Basis Data II', 'sks' => 3, 'code' => 'TI505', 'lecturer' => 'SITI NURHALIZA S.T., M.T.'],
            ['name' => 'Mobile Programming', 'sks' => 3, 'code' => 'TI506', 'lecturer' => 'HENDRO WICAKSONO M.Kom.'],
            ['name' => 'Sistem Pendukung Keputusan', 'sks' => 2, 'code' => 'TI507', 'lecturer' => 'DR. EKO PRASETYO M.Sc.'],
            ['name' => 'Teknik Kompilasi', 'sks' => 2, 'code' => 'TI508', 'lecturer' => 'AGUS SANTOSO S.Kom., M.T.'],
        ];

        foreach ($subjects as $sub) {
            DB::table('master_subjects')->insert([
                'name' => $sub['name'],
                'sks' => $sub['sks'],
                'code' => $sub['code'],
                'default_lecturer' => $sub['lecturer'],
                'class_id' => $classId,
                'semester' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========================================================
        // 4. JADWAL KULIAH (Ketua & Sekretaris share jadwal)
        // ========================================================
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $rooms = ['V.401', 'V.402', 'V.706', 'L.201', 'LAB-A', 'LAB-B', 'ZOOM-01', 'V.503'];
        $schedules = [];

        foreach ($subjects as $i => $sub) {
            $day = $days[$i % count($days)];
            $start = sprintf('%02d:00', 7 + ($i % 4) * 3);
            $end = date('H:i', strtotime($start . ' + 150 minutes'));

            $schedules[] = [
                'subject_name' => $sub['name'],
                'lecturer_name' => $sub['lecturer'],
                'day' => $day,
                'time_start' => $start,
                'time_end' => $end,
                'room' => $rooms[$i],
                'delivery_type' => $i % 3 === 0 ? 'online' : 'offline',
                'is_validated' => true,
                'class_id' => $classId,
                'semester' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('academic_schedules')->insert($schedules);

        // ========================================================
        // 5. TUGAS INDIVIDU & KELOMPOK (Sekretaris share tugas)
        // ========================================================
        $assignments = [
            ['subj' => 'Rekayasa Perangkat Lunak', 'title' => 'Makalah Analisis Kebutuhan Sistem', 'desc' => 'Buat dokumen SRS sesuai IEEE 830 minimal 15 halaman.', 'type' => 'individual', 'days' => 7, 'validated' => true],
            ['subj' => 'Pemrograman II', 'title' => 'Project CRUD Laravel + Tailwind', 'desc' => 'Buat aplikasi CRUD sederhana dengan autentikasi. Deploy ke Vercel.', 'type' => 'individual', 'days' => 14, 'validated' => true],
            ['subj' => 'Basis Data II', 'title' => 'Tugas Kelompok Normalisasi Database', 'desc' => 'Normalisasi studi kasus Perpustakaan ke 3NF. Kelompok max 5 orang.', 'type' => 'group', 'days' => 10, 'validated' => true],
            ['subj' => 'Mobile Programming', 'title' => 'Mini Project Aplikasi To-Do List Kotlin', 'desc' => 'Buat aplikasi native Android dengan RecyclerView dan Room Database.', 'type' => 'individual', 'days' => 21, 'validated' => true],
            ['subj' => 'Teknologi Internet of Things', 'title' => 'Laporan Praktikum Sensor Arduino', 'desc' => 'Dokumentasikan penggunaan sensor DHT11 dan tampilkan di LCD.', 'type' => 'group', 'days' => 5, 'validated' => true],
            ['subj' => 'Sistem Pendukung Keputusan', 'title' => 'Implementasi Metode AHP', 'desc' => 'Implementasi Analytic Hierarchy Process untuk studi kasus pemilihan laptop.', 'type' => 'individual', 'days' => 12, 'validated' => false],
            ['subj' => 'Teknik Kompilasi', 'title' => 'Tugas Pembuatan Lexical Analyzer', 'desc' => 'Buat scanner sederhana yang bisa tokenisasi kode sumber C.', 'type' => 'individual', 'days' => 8, 'validated' => false],
            ['subj' => 'Kerja Praktek', 'title' => 'Laporan Akhir KP Perusahaan', 'desc' => 'Laporan KP lengkap format APA 7th Edition. Min 40 halaman.', 'type' => 'individual', 'days' => 30, 'validated' => true],
            ['subj' => 'Rekayasa Perangkat Lunak', 'title' => 'Presentasi Use Case Diagram', 'desc' => 'Siapkan slide PPT dan presentasi UML Use Case 15 menit per kelompok.', 'type' => 'group', 'days' => 3, 'validated' => true],
            ['subj' => 'Pemrograman II', 'title' => 'Quiz Coding: API RESTful', 'desc' => 'Open book quiz. Buat REST API dengan 4 endpoint dalam 90 menit.', 'type' => 'individual', 'days' => 1, 'validated' => true],
        ];

        foreach ($assignments as $a) {
            DB::table('assignments')->insert([
                'subject_name' => $a['subj'],
                'title' => $a['title'],
                'description' => $a['desc'],
                'deadline' => $now->copy()->addDays($a['days'])->setTime(23, 59, 0),
                'type' => $a['type'],
                'is_validated' => $a['validated'],
                'class_id' => $classId,
                'semester' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========================================================
        // 6. MODUL KULIAH (Sekretaris upload modul, link & file)
        // ========================================================
        $modules = [
            ['subj' => 'Rekayasa Perangkat Lunak', 'title' => 'Slide Pertemuan 1-4: Pengantar RPL', 'type' => 'link', 'url' => 'https://drive.google.com/file/d/RPL_slide_01'],
            ['subj' => 'Rekayasa Perangkat Lunak', 'title' => 'Template Dokumen SRS IEEE 830', 'type' => 'link', 'url' => 'https://drive.google.com/file/d/SRS_template'],
            ['subj' => 'Pemrograman II', 'title' => 'Modul Laravel 11: Routing & Controller (PDF)', 'type' => 'file', 'url' => null],
            ['subj' => 'Pemrograman II', 'title' => 'Cheat Sheet Artisan Commands', 'type' => 'link', 'url' => 'https://laravel.com/docs/artisan'],
            ['subj' => 'Basis Data II', 'title' => 'Ebook: Database System Concepts Ed.7 (PDF)', 'type' => 'file', 'url' => null],
            ['subj' => 'Mobile Programming', 'title' => 'Tutorial Kotlin Coroutines - Android Dev (Link)', 'type' => 'link', 'url' => 'https://developer.android.com/kotlin/coroutines'],
            ['subj' => 'Mobile Programming', 'title' => 'Modul Praktikum RecyclerView (PDF)', 'type' => 'file', 'url' => null],
            ['subj' => 'Teknologi Internet of Things', 'title' => 'Datasheet Sensor DHT11 (PDF)', 'type' => 'file', 'url' => null],
            ['subj' => 'Sistem Pendukung Keputusan', 'title' => 'Jurnal Referensi AHP Saaty (Link)', 'type' => 'link', 'url' => 'https://scholar.google.com/ahp-saaty'],
            ['subj' => 'Teknik Kompilasi', 'title' => 'Slide Teori Automata & Grammar (PDF)', 'type' => 'file', 'url' => null],
            ['subj' => 'Kerja Praktek', 'title' => 'Panduan Penulisan Laporan KP 2026 (PDF)', 'type' => 'file', 'url' => null],
        ];

        foreach ($modules as $m) {
            DB::table('learning_modules')->insert([
                'subject_name' => $m['subj'],
                'title' => $m['title'],
                'type' => $m['type'],
                'link_url' => $m['url'],
                'file_path' => $m['type'] === 'file' ? null : null,
                'is_validated' => true,
                'class_id' => $classId,
                'semester' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========================================================
        // 7. BUKU KAS KELAS (Bendahara input income/expense)
        // ========================================================
        $allStudentIds = array_values($studentIds);
        $cashEntries = [];

        // Iuran Kas Masuk (income) dari 25 mahasiswa @ Rp20.000
        for ($i = 3; $i < 28; $i++) {
            $cashEntries[] = [
                'student_id' => $allStudentIds[$i],
                'type' => 'income',
                'amount' => 20000,
                'description' => 'Iuran Kas Bulanan Juni 2026',
                'transaction_date' => $now->copy()->subDays(rand(1, 10))->toDateString(),
                'is_validated' => true,
                'class_id' => $classId,
                'semester' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Beberapa pengeluaran (expense)
        $expenses = [
            ['amount' => 50000, 'desc' => 'Print makalah kelompok RPL (50 lembar)'],
            ['amount' => 75000, 'desc' => 'Beli kertas A4 + spidol presentasi'],
            ['amount' => 120000, 'desc' => 'Sewa proyektor cadangan Lab IoT'],
            ['amount' => 35000, 'desc' => 'Cetak banner kegiatan seminar kelas'],
            ['amount' => 80000, 'desc' => 'Konsumsi rapat evaluasi tengah semester'],
        ];

        foreach ($expenses as $exp) {
            $cashEntries[] = [
                'student_id' => null,
                'type' => 'expense',
                'amount' => $exp['amount'],
                'description' => $exp['desc'],
                'transaction_date' => $now->copy()->subDays(rand(1, 14))->toDateString(),
                'is_validated' => true,
                'class_id' => $classId,
                'semester' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Kas pending (belum divalidasi bendahara — bayar via transfer)
        for ($i = 28; $i < 30; $i++) {
            $cashEntries[] = [
                'student_id' => $allStudentIds[$i],
                'type' => 'income',
                'amount' => 20000,
                'description' => 'Iuran Kas (Transfer) — Menunggu Validasi',
                'transaction_date' => $now->toDateString(),
                'is_validated' => false,
                'class_id' => $classId,
                'semester' => $semester,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('cash_ledgers')->insert($cashEntries);

        // ========================================================
        // 8. ABSENSI KEHADIRAN (variasi: Hadir, Alfa, Izin, Sakit)
        // ========================================================
        $attendances = [];
        $statuses = ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Hadir', 'Alfa', 'Izin', 'Sakit'];

        foreach ($subjects as $subIdx => $sub) {
            // Simulasi 4 pertemuan per matkul
            for ($pertemuan = 1; $pertemuan <= 4; $pertemuan++) {
                $attDate = $now->copy()->subWeeks(4 - $pertemuan)->toDateString();

                foreach ($allStudentIds as $sid) {
                    $status = $statuses[array_rand($statuses)];
                    $attendances[] = [
                        'student_id' => $sid,
                        'subject_name' => $sub['name'],
                        'attendance_date' => $attDate,
                        'status' => $status,
                        'is_validated' => ($status === 'Hadir' || $status === 'Alfa') ? true : (rand(0, 1) ? true : false),
                        'class_id' => $classId,
                        'semester' => $semester,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // Chunk insert agar tidak overload
        foreach (array_chunk($attendances, 200) as $chunk) {
            DB::table('class_attendances')->insert($chunk);
        }

        // ========================================================
        // 9. NOTIFIKASI (simulasi aliran notif realistis)
        // ========================================================
        $notifications = [];

        // — Notif broadcast ke semua mahasiswa dari Sekretaris (tugas baru) —
        foreach ($allStudentIds as $sid) {
            $notifications[] = [
                'student_id' => $sid,
                'class_id' => $classId,
                'message' => '📝 Tugas Baru: Makalah Analisis Kebutuhan Sistem (RPL) — Deadline 7 hari lagi!',
                'is_read' => $sid === $ketuaId ? true : (rand(0, 1) ? true : false),
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ];
        }

        // — Notif broadcast tugas kelompok —
        foreach ($allStudentIds as $sid) {
            $notifications[] = [
                'student_id' => $sid,
                'class_id' => $classId,
                'message' => '👥 Tugas Kelompok Baru: Normalisasi Database (Basis Data II) — Kelompok max 5 orang.',
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ];
        }

        // — Notif modul baru —
        foreach ($allStudentIds as $sid) {
            $notifications[] = [
                'student_id' => $sid,
                'class_id' => $classId,
                'message' => '📚 Modul Baru Diupload: Slide Pertemuan 1-4 Pengantar RPL. Silakan unduh!',
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(4),
            ];
        }

        // — Notif kas ke bendahara —
        $notifications[] = [
            'student_id' => $bendId,
            'class_id' => $classId,
            'message' => '💰 Ada 2 pembayaran kas via transfer yang menunggu validasi Anda.',
            'is_read' => false,
            'created_at' => $now->copy()->subHours(2),
            'updated_at' => $now->copy()->subHours(2),
        ];

        // — Notif izin/sakit ke Ketua untuk validasi —
        $notifications[] = [
            'student_id' => $ketuaId,
            'class_id' => $classId,
            'message' => '🏥 AISYAH NUR FADILAH mengajukan izin sakit pada matkul Pemrograman II. Perlu validasi.',
            'is_read' => false,
            'created_at' => $now->copy()->subHours(6),
            'updated_at' => $now->copy()->subHours(6),
        ];

        $notifications[] = [
            'student_id' => $ketuaId,
            'class_id' => $classId,
            'message' => '📋 BAYU SETYA NUGRAHA mengajukan rekap absen (Izin Sidang KP). Perlu validasi!',
            'is_read' => false,
            'created_at' => $now->copy()->subHours(3),
            'updated_at' => $now->copy()->subHours(3),
        ];

        // — Notif jadwal update ke semua —
        foreach ($allStudentIds as $sid) {
            $notifications[] = [
                'student_id' => $sid,
                'class_id' => $classId,
                'message' => '📅 Jadwal Mobile Programming berubah: ONLINE via Zoom (Link di menu Akademi Hub).',
                'is_read' => rand(0, 1) ? true : false,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ];
        }

        // — Notif pengeluaran kas ke semua —
        foreach ($allStudentIds as $sid) {
            $notifications[] = [
                'student_id' => $sid,
                'class_id' => $classId,
                'message' => '💸 Pengeluaran Kas: Rp120.000 untuk sewa proyektor Lab IoT. Saldo dikurangi.',
                'is_read' => $sid === $bendId ? true : (rand(0, 1) ? true : false),
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ];
        }

        // Chunk insert notifications
        foreach (array_chunk($notifications, 100) as $chunk) {
            DB::table('notifications')->insert($chunk);
        }

        $this->command->info("✅ Dummy data berhasil disuntikkan!");
        $this->command->info("   Kelas      : TPLE-013 (ID: {$classId})");
        $this->command->info("   Mahasiswa  : 30 orang (3 pengurus + 27 biasa)");
        $this->command->info("   Mata Kuliah: 8 matkul");
        $this->command->info("   Jadwal     : 8 slot");
        $this->command->info("   Tugas      : 10 (individu + kelompok)");
        $this->command->info("   Modul      : 11 (file + link)");
        $this->command->info("   Kas        : 32 transaksi (income + expense + pending)");
        $this->command->info("   Absensi    : " . count($attendances) . " records");
        $this->command->info("   Notifikasi : " . count($notifications) . " records");
        $this->command->info("");
        $this->command->info("🔑 Login Credentials:");
        $this->command->info("   Ketua    : {$ketuaNim} / {$ketuaNim}KK");
        $this->command->info("   Sekretaris: {$sekretNim} / {$sekretNim}SK");
        $this->command->info("   Bendahara : {$bendNim} / {$bendNim}BD");
        $this->command->info("   Mahasiswa : 231011400004 / 231011400004 (dst.)");
    }
}
