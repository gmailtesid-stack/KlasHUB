# Changelog — KelasHUB

Semua perubahan penting pada proyek ini akan didokumentasikan di sini.  
Format mengacu pada [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased]

---

## [2.3.0] — 2026-05-30

### Added
- **Push Notification via OneSignal**: Integrasi penuh OneSignal REST API v2 untuk pengiriman notifikasi pop-up eksternal ke perangkat Android mahasiswa secara real-time.
- **`NotificationService`** (`app/Services/NotificationService.php`): Service terpusat baru untuk mengirim notifikasi ke kelas (`notifyClass`), individu (`notifyStudent`), dan admin (`notifyAdmins`).
- **Kolom `onesignal_id`** pada tabel `students` (migrasi `2026_05_30_195000_add_onesignal_id_to_students`): Menyimpan Subscription ID perangkat untuk pengiriman push notification yang ditargetkan.
- **Endpoint `POST /kh/device-token`**: API baru untuk registrasi token perangkat dari aplikasi Android native saat mahasiswa login.
- **`MainApplication.kt`** (`android-webview/`): Kelas Application baru untuk inisialisasi OneSignal SDK saat aplikasi Android pertama kali dijalankan.
- **`syncOneSignalToken()`** di `MainActivity.kt`: Logika otomatis untuk mengirimkan OneSignal Subscription ID ke backend setelah dashboard dimuat.

### Changed
- `NotificationService` diperbarui dari Firebase (FCM) ke **OneSignal REST API v2** (`include_subscription_ids`) yang lebih modern dan akurat.
- `ApiInterface.kt` ditambahkan endpoint `updateDeviceToken` untuk mendukung registrasi perangkat dari Android.
- `AndroidManifest.xml` diperbarui untuk mendaftarkan `MainApplication` sebagai kelas Application utama.
- `build.gradle` (modul `app`) ditambahkan dependensi `com.onesignal:OneSignal`.
- `gradle/wrapper/gradle-wrapper.properties`: Batas waktu jaringan (`networkTimeout`) ditingkatkan dari 10 detik menjadi 120 detik untuk koneksi yang lebih stabil.

---

## [2.1.0] — 2026-05-21

### Added
- **Simulasi Engine** (`SimulasiController`): Loop 4 detik yang mensimulasikan aktivitas nyata 5 kelas secara otomatis menggunakan `DB::table()` murni untuk hemat RAM Vercel.
- **Export Engine** (`LaporanController`):
  - `exportPdf($class_id)`: Menghasilkan laporan keuangan kas formal monokrom via `barryvdh/laravel-dompdf`.
  - `exportExcel($class_id)`: CSV Streaming murni menggunakan `fputcsv` ke `php://output` — RAM ~0 MB.
- **Tabel `notifications`**: Migrasi baru untuk menyimpan log aktivitas internal dan pesan simulasi.
- **UjiKomprehensifController**: Engine pengujian satu-klik (endpoint `/test-full`) yang memverifikasi Tugas, Modul, Absensi, dan Kas dalam satu request.
- Dokumentasi lengkap: `README.md`, `CHANGELOG.md`, `CONTRIBUTING.md`, `docs/API.md`.

### Changed
- **Unified Class Registration**: Panel Super Admin kini hanya memiliki satu form modal terpadu.
- `SimulasiController` dioptimasi dari loop 60 detik menjadi 4 detik agar aman dari timeout Vercel Free Tier.

### Removed
- Hapus panel Super Admin lama (2 form terpisah) — total ~200 baris kode dibuang dari `main_mobile.blade.php`.

---

## [2.0.0] — 2026-05-21

### Added
- **Sistem Multi-Kelas** (`academic_classes`): Tabel baru sebagai "rumah" data terisolasi per kelas.
- **Kolom `class_id`**: Ditambahkan ke semua tabel operasional via migrasi batch.
- **Unified Class Registration** (`storeUnifiedClass`): Endpoint atomik membuat record kelas dan akun Ketua Kelas sekaligus.
- **Kolom `department` dan `contact`** pada tabel `academic_classes`.
- **Role `super_admin`**: Ditambahkan ke enum `students.role`.
- **Super Admin Panel** di dashboard: Tab khusus untuk manajemen lintas kelas.
- Laporan presensi & keuangan: `ReportController` dengan 4 endpoint (PDF & Excel).

### Fixed
- **Crash 500 di Vercel** akibat infinite recursion pada trait `BelongsToClass` — trait dihapus dan relasi diimplementasikan langsung di controller.
- Relasi `ketuaKelas` pada `AcademicClass` yang menyebabkan eager loading berulang.

### Changed
- Tombol laporan (PDF/Excel) dipindahkan ke tab **Presensi Tracker**.
- `getStudentDashboard` dioptimasi: query berat dipisahkan ke endpoint AJAX (`/kh/api/dashboard-data`).

---

## [1.5.0] — 2026-05-18

### Added
- **File content storage** langsung di database: Modul file disimpan sebagai `base64` LONGTEXT.
- Kolom `file_content` dan `mime_type` pada tabel `learning_modules`.

---

## [1.4.0] — 2026-05-16

### Added
- Tabel `learning_modules` dengan dukungan tipe `file` dan `link`.
- Tabel `master_subjects` sebagai referensi mata kuliah resmi.
- Toggle `delivery_type` (Online/Offline) pada jadwal kuliah secara real-time.
- Validasi data (`is_validated`) pada semua tabel operasional.

### Fixed
- Deployment Vercel: Session driver diubah ke `cookie` (stateless), view cache ke `/tmp/`.

---

## [1.0.0] — 2024-01-01

### Added
- Rilis awal KelasHUB.
- Tabel dasar: `students`, `academic_schedules`, `assignments`, `cash_ledgers`, `class_attendances`.
- Sistem kehadiran dengan "Sisa Nyawa" (3 nyawa per mata kuliah).
- RBAC: `ketua_kelas`, `sekretaris`, `bendahara`, `mahasiswa`.
- Dashboard mobile-first dengan Stealth Zinc-900 theme.
- Android WebView wrapper (Kotlin).
