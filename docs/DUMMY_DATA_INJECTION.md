# Dokumen Injeksi Ledakan Data Mentah (The Dummy Bomb Seeder Guide)

Karena perisai isolat Data Multi-Tenant KelasHUB *(BelongsToClass Global Scopes)* begitu agresif, melakukan audit arsitektur desain (Quality Assurance Front-end UI) tanpa injeksi Ribuan Set Data secara massif sangatlah memberatkan pihak penguji. 

Dokumentasi ini membongkar panduan pengoperasian *DummyClassSeeder.php* tanpa memicu insiden Time-Out pada Arsitektur Jaringan Vercel Web Server.

---

## 1. Peringatan Keamanan Kritis Vercel (Timeout Fatalities)

JANGAN menyalurkan (Execute) skrip ini melewati panggilan rute API `POST` standar yang dipancing lewat Browser Anda (Contoh: `/api/start-seeder`). Memompa 1000 Mahasiswa dengan Hash Sandi *Bcrypt PHP* yang berat, akan mencekik Komputasi Server Vercel yang dipacu hidup melayani di dalam 10 detik. Jika dilakukan lewat Browser / Postman URL: Anda bakal berhadapan di Error Merah Bisu **`504 Gateway Deployment Timeout`**.

### 1a. Manuver Pintu Darurat (Terminal Direct TCP Tunnel)
Penggelembungan Ribuan Profil Bohongan **WAJIB** dieksekusi secara lokal dari LapTop/Komputer Insinyur *(Development Machine)* via Protokol Baris Komando murni CLI yang menembak TCP Tembok Peladen MySQL secara absolut *(Bebas Bypassed dari Batas Jembatan Vercel)*:

```bash
php artisan db:class --class="Database\Seeders\DummyClassSeeder"
```

*CLI (Command-Line PHP)* akan berjalan merangkul konektivitas jaringan, kadang mengendap waktu eksekusi sampai 18 Detik komputasi hashing massal berjalan, tapi tak akan pernah di-interupsi server!

---

## 2. Struktur Bom Biologis Populasi Mahasiswa Pemandu

Perintah Diatas melahirkan Populasi Semu Terisolasi pada 1 Tenant: **Kelas Universitas "Teknik Informatika - TPLE-013"** (Semester 2). Kelas palsu ini merajut seluruh jaringannya dan menghindari bentrokan *(Crash FK Reference Constraint)* dengan tabel kampus lain. Barisan yang dilahirkan meliputi:

* **Injeksi Kepengurusan Tingkat Atas:** 
  1. Sang Diktator Otoritas Ketua: Nomor Induk `231011400001` (Katakunci Darurat: `231011400001KK`).
  2. Menteri Keuangan (Bendahara): Nomor Induk `231011400003` (Katakunci Darurat: `231011400003BD`).
  3. Menteri Administrasi (Sekretaris): Nomor Induk `231011400002` (Sandi: `231011400002SK`).
* **Konsumer Jelata Android Kotlin:**
  Akan lahir **27 Mahasiswa Biasa**. Mengadopsi Identitas (NIM) melesat beranak-pinak dari NIM: `0004` melaju ke angka `0030`. (Sandi disatukan serentak `password123`).
* **Serangan Kehidupan Asrama (Data Sampah Realistis):**
  Mengimpor struktur Jadwal Vektor di UI Android dengan menyinggung Modul Matriks, lalu melemparkan **960 Rangkaian Status Kehadiran (Attendances)** secara rapi direntangkan membuntuti 8 hari Kalender Kuliah dengan metode DB `array_chunk($attendancesData, 200)` agar baris Data tak memprovokasi Penolakan Koneksi (*MySQL Package Too Long Error*). 

Menjalankan perintah skrip *Seeding Dummy* tersebut me-reset total (*Clean Slate Purging* `DB:table(students)->delete`) populasi siluman pada kelompok TPLE-013 yang dulu-dulu *(Garbage Pre-Clean Validation)* tanpa mencederai Anggota Tenant Prodi lain! Cukup pencet satu tombol CLI.
