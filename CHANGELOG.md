# Changelog — KelasHUB

Semua perubahan penting pada proyek ini akan didokumentasikan di sini.  
Format mengacu pada [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased]

---

## [2.1.0] — 2026-05-21

### Added
- **Simulasi Engine** (`SimulasiController`): Loop 4 detik yang mensimulasikan aktivitas nyata 5 kelas secara otomatis menggunakan `DB::table()` murni untuk hemat RAM Vercel.
- **Export Engine** (`LaporanController`):
  - `exportPdf($class_id)`: Menghasilkan laporan keuangan kas formal monokrom via `barryvdh/laravel-dompdf`.
  - `exportExcel($class_id)`: CSV Streaming murni menggunakan `fputcsv` ke `php://output` — RAM ~0 MB.
- **Tabel `notifications`**: Migrasi baru untuk menyimpan log aktivitas dan pesan simulasi.
- **UjiKomprehensifController**: Engine pengujian satu-klik (endpoint `/test-full`) yang memverifikasi Tugas, Modul, Absensi, dan Kas dalam satu request.
- Dokumentasi lengkap: `README.md`, `CHANGELOG.md`, `CONTRIBUTING.md`, `docs/API.md`.

### Changed
- **Unified Class Registration**: Panel Super Admin kini hanya memiliki satu form modal terpadu (mendaftarkan Kelas + Ketua Kelas sekaligus), menggantikan dua form terpisah yang redundant.
- `SimulasiController` dioptimasi dari loop 60 detik menjadi 4 detik agar aman dari timeout 10 detik Vercel Free Tier.

### Removed
- Hapus panel Super Admin lama (2 form terpisah + tabel data duplikat) — total ~200 baris kode dibuang dari `main_mobile.blade.php`.

---

## [2.0.0] — 2026-05-21

### Added
- **Sistem Multi-Kelas** (`academic_classes`): Tabel baru sebagai "rumah" data terisolasi per kelas.
- **Kolom `class_id`**: Ditambahkan ke semua tabel operasional (`students`, `assignments`, `cash_ledgers`, `class_attendances`, `learning_modules`, `academic_schedules`, `master_subjects`) via migrasi batch.
- **Unified Class Registration** (`storeUnifiedClass`): Endpoint baru yang secara atomik membuat record kelas dan akun Ketua Kelas dalam satu transaksi database.
- **Kolom `department` dan `contact`** pada tabel `academic_classes` untuk data operasional yang lebih kaya.
- **Role `super_admin`**: Ditambahkan ke enum `students.role`.
- **Super Admin Panel** di dashboard: Tab khusus untuk manajemen lintas kelas.
- **Navigasi mobile khusus Super Admin**: Bottom navigation dengan tab "S. Admin".
- Laporan presensi & keuangan: `ReportController` dengan 4 endpoint (PDF & Excel untuk absensi + kas).

### Fixed
- **Crash 500 di Vercel** akibat infinite recursion pada trait `BelongsToClass` — trait dihapus seluruhnya dan relasi diimplementasikan secara langsung di controller.
- Relasi `ketuaKelas` pada `AcademicClass` yang menyebabkan eager loading berulang.

### Changed
- Tombol laporan (PDF/Excel) dipindahkan ke tab **Presensi Tracker** untuk UX yang lebih intuitif.
- `getStudentDashboard` dioptimasi: query berat dipisahkan ke endpoint AJAX (`/kh/api/dashboard-data`).

---

## [1.5.0] — 2026-05-18

### Added
- **File content storage** langsung di database: Modul file disimpan sebagai `base64` LONGTEXT agar tidak memerlukan filesystem (tidak tersedia di Vercel).
- Kolom `file_content` dan `mime_type` pada tabel `learning_modules`.

---

## [1.4.0] — 2026-05-16

### Added
- Tabel `learning_modules` dengan dukungan tipe `file` dan `link`.
- Tabel `master_subjects` sebagai referensi mata kuliah resmi.
- Kolom `code` dan `class` pada tabel `academic_schedules`.
- Toggle `delivery_type` (Online/Offline) pada jadwal kuliah secara real-time.
- Validasi data (`is_validated`) pada semua tabel operasional untuk alur persetujuan multi-role.
- Kolom `notes` pada `class_attendances` untuk keterangan surat.
- Route laporan presensi & keuangan.

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
