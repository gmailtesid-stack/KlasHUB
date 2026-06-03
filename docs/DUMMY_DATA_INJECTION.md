# Dokumentasi Injeksi Data Dummy KelasHUB (V2.3.0)

Dokumen ini menjelaskan secara teknis bagaimana data dummy dalam skala besar (1.500+ record) berhasil disuntikkan ke dalam sistem **KelasHUB** yang beroperasi di arsitektur Serverless Vercel dan Remote Database TiDB Cloud.

## 1. Tantangan Arsitektur Serverless
Pada arsitektur Vercel (khususnya paket *Hobby/Free*), semua *Serverless Functions* memiliki batasan waktu eksekusi maksimal **10 detik**.
Proses penyuntikkan data (Dummy Seeding) membutuhkan waktu eksekusi yang lama (minimal 3-5 detik hanya untuk proses *hashing password* 30 user menggunakan `Hash::make`), dan juga latensi bolak-balik (I/O HTTP) untuk mengirim sebanyak 1.500 baris ke database TiDB yang berada di Region AWS Singapore.

Jika memaksakan eksekusi Seeder melalui *endpoint* Vercel (misal menggunakan koneksi HTTP REST `Invoke-RestMethod`), Vercel API Gateway akan memutus paksa koneksi secara sepihak dan melempar *Error 504 (Gateway Timeout)*. 

## 2. Metodologi Eksekusi (Bypass Vercel Timeout)
Untuk menghindari limitasi 10 detik Vercel, kita melakukan **Remote Direct Execution**.
Karena database `TiDB` bisa diakses dari mana saja menggunakan URI MySQL, eksekusi dilakukan lokal (*on-disk execution*) menggunakan Terminal PowerShell yang menjalankan mesin PHP-Artisan lokal, yang kemudian melakukan kontak I/O statis namun berantai ke *Remote Database TiDB*.

**Perintah yang dieksekusi melalui PowerShell (Lokal):**
```bash
php artisan db:seed --class="Database\Seeders\DummyClassSeeder"
```
Proses ini memakan waktu kurang lebih **12-15 Detik** hingga selesai (Sukses melewati batas 10 Detik milik Vercel).

---

## 3. Struktur Detail Data Dummy Yang Disuntikkan

Eksekusi ini menghasilkan lingkungan testing *"Production-Ready"* dalam 1 Kelas saja (mengamankan kelas lain dari bentrok data).

- **Informasi Kelas:** Teknik Informatika - TPLE-013 (Semester 1, Tahun Ajaran 2025/2026).
- **Entitas Mahasiswa (30 Akun):**
  - **1 Ketua Kelas** (NIM: `231011400001` | Password: `231011400001KK`)
  - **1 Sekretaris** (NIM: `231011400002` | Password: `231011400002SK`)
  - **1 Bendahara** (NIM: `231011400003` | Password: `231011400003BD`)
  - **27 Mahasiswa Biasa** (NIM berurut: `0004` hingga `0030`, Password identik dengan NIM).
- **Konfigurasi Akademi:**
  - 8 Mata Kuliah Master (SKS & Dosen Pengampu lengkap).
  - 8 Blok Jadwal Kuliah (Offline dan Online via Zoom).
  - 10 Tugas (Tugas Individu, Kelompok, dan Kuis dengan Status Validasi bervariasi).
  - 11 Modul Materi Pembelajaran Lengkap.
  - 960+ Histori Kehadiran Absensi yang dialokasikan di 4 pertemuan.
- **Konfigurasi Finansial:**
  - 32 Histori Pemasukan (Status Valid/Menunggu Validasi) dan Pengeluaran Biaya Kelas.
- **Notifikasi Push (153 Record):** Logika Broadcast Simulasi tugas kelompok dari sekretaris dan Validasi kas.

---

## 4. Source Code Utama: `DummyClassSeeder.php`

Berikut adalah inti logika Injeksi Data (Seeder) yang dipakai:

```php
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

        // 1. Handling Class ID Existing/Pembuatan Baru
        $existingClass = DB::table('academic_classes')->where('code', 'TPLE-013')->first();
        if ($existingClass) {
            $classId = $existingClass->id;
            // Purge semua data usang milik class ini saja
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
                // ...
            ]);
        }

        // 2. Insert Mahasiswa + Hashing Passwords (Heavy Load)
        // [Data mahasiswa disiapkan dalam bentuk Array $studentRows]
        // ... (30 Mahasiswa) ...

        // 3. Insert Master Subjects dengan logika Avoid Duplicate Constraint
        foreach ($subjects as $sub) {
            DB::table('master_subjects')->updateOrInsert(
                ['name' => $sub['name']], // Unique Identifier Constraint
                [
                    'sks' => $sub['sks'],
                    // ... Parameter lainnya
                    'class_id' => $classId
                ]
            );
        }

        // 4. Batch Processing Data Kehadiran (960 Absensi)
        // Vercel sering melempar timeout error apabila query MySQL dilakukan 1-by-1 di loop 1000 iterasi.
        // Dipecah dan disuntikkan secara bulk chunk:
        foreach (array_chunk($attendances, 200) as $chunk) {
            DB::table('class_attendances')->insert($chunk);
        }
    }
}
```

## Kesimpulan
Pendekatan ini memisahkan batas kewenangan antara Vercel sebagai Delivery Edge Node (untuk User HTTP Request) dan Mesin Lokal Administratif sebagai Ingestion Node (Database Seeder), memastikan bahwa basis data bisa terus beroperasi dengan beban tinggi di TiDB tanpa dihukum (penalty timeout) oleh batas 10-detik Vercel.
