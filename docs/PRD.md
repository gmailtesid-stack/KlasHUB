# Product Requirement Document (PRD): KelasHUB — All-in-One Class Operations Suite

**Versi**: 2.2 (Industrial Release)  
**Status**: Live / Production  
**Author**: Ariyas Pratama Ramadhan By WaveProject.ID  
**Bahasa**: Indonesia  
**Tanggal**: 27 Mei 2026

---

## 1. Ringkasan Eksekutif (Executive Summary)
**KelasHUB** adalah solusi *enterprise-grade* untuk manajemen operasional kelas perkuliahan yang mengintegrasikan sistem kehadiran (attendance), buku kas (finance), repositori modul (LMS), dan manajemen jadwal dalam satu platform terpadu. Proyek ini didesain khusus untuk berjalan secara efisien di infrastruktur *Serverless* dengan arsitektur *Multi-Tenant Isolation*, memungkinkan skalabilitas lintas kelas dengan biaya operasional minimal (*Low-Cost, High-Impact*).

## 2. Latar Belakang & Analisis Masalah
### 2.1 Konteks Masalah
Manajemen kelas di perguruan tinggi seringkali menghadapi kendala fragmentasi data:
- **Presensi Manual**: Risiko kecurangan (titip absen) dan sulitnya rekapitulasi real-time.
- **Transparansi Dana**: Pengelolaan uang kas yang tidak terdokumentasi dengan baik sering menimbulkan konflik.
- **Aksesibilitas Modul**: Modul kuliah yang tersebar di aplikasi chat seringkali sulit dicari kembali.
- **Infrastruktur**: Kebutuhan sistem yang handal namun terkendala budget server konvensional.

### 2.2 Sasaran Produk
1.  **Sentralisasi**: Satu pintu untuk semua kebutuhan administrasi kelas.
2.  **Akuntabilitas**: Sistem "Sisa Nyawa" untuk mendisiplinkan kehadiran secara otomatis.
3.  **Efisiensi Biaya**: Optimasi penuh pada *Vercel Free Tier* dan *TiDB Cloud*.
4.  **Mobilitas**: Desain *Mobile-First* dengan implementasi *Android WebView*.

### 2.3 Timeline Perkembangan (Product Evolution)
- **Fase 1 (v1.0 - Jan 2024)**: MVP (Minimum Viable Product). Rilis dasar Attendance, Cash Ledger, dan RBAC dasar.
- **Fase 2 (v1.4 - May 2026)**: Hardening & Feature Expansion. Penambahan Learning Modules, Master Subjects, dan optimasi Base64 storage.
- **Fase 3 (v2.0 - Current)**: Scale to SaaS. Implementasi sistem Multi-Tenant, Super Admin Panel, dan atomisitas registrasi kelas.

## 3. Matriks Pengguna & Persona (User Personas)
Sistem menggunakan *Role-Based Access Control* (RBAC) yang ketat pada tabel `students`:

| Role | Deskripsi Persona | Tanggung Jawab Utama |
|:---|:---|:---|
| **Super Admin** | Pengelola Platform | Registrasi kelas baru secara atomik, audit sistem, manajemen lintas-tenant. |
| **Ketua Kelas** | Administrator Kelas | Validasi data presensi/keuangan, manajemen anggota, kontrol mode hari kuliah (Online/Offline). |
| **Sekretaris** | Operasional Akademik | Input data kehadiran, upload modul pembelajaran, manajemen jadwal harian. |
| **Bendahara** | Operasional Finansial | Manajemen buku kas kelas (pemasukan/pengeluaran), pelaporan keuangan berkala. |
| **Mahasiswa** | Pengguna Akhir | Dashboard personal, download modul, submit rekap mandiri (Izin/Sakit), monitoring "Sisa Nyawa". |

## 4. Spesifikasi Fungsional (Functional Specifications)

### 4.1 Modul Smart Attendance & Engine "Sisa Nyawa"
Sistem kehadiran berbasis pinalti untuk otomatisasi sanksi akademis.
- **Aturan Bisnis (Logic)**:
    - Default: 3 Nyawa per mata kuliah.
    - Status `Alfa` (Validated) → Deduct 1 Life.
    - Status `Sakit/Izin` (Validated) → No life deduction.
    - Life = 0 → Status otomatis `DICEKAL` (Daftar Isi Cekal), sistem memberikan peringatan visual merah pekat.
- **Fitur Validation**: Rekap mandiri oleh Mahasiswa tidak akan mengubah data hingga disetujui (validated) oleh Sekretaris/Ketua Kelas.

### 4.2 Modul Financial Ledger (Buku Kas)
Sistem pencatatan transaksi kelas yang auditabel.
- **Fitur Utama**: Input debet/kredit, rincian deskripsi, dan tanggal transaksi.
- **Real-time Balance**: Saldo berjalan otomatis dihitung berdasarkan agregasi SQL `SUM(type='income') - SUM(type='expense')`.
- **Exporting**: Dukungan ekspor ke PDF (Formal) dan CSV Streaming untuk efisiensi RAM.

### 4.3 Modul Academic Hub (LMS Repository)
Pusat penyimpanan materi perkuliahan.
- **Optimization Strategy**: Menggunakan penyimpanan `Base64` di kolom `LONGTEXT` untuk modul file guna menghindari keterbatasan tidak adanya penyimpanan persisten di Vercel Functions.
- **Support**: File (PDF, DOCX, TXT) dan Link eksternal (YouTube, GDrive).

### 4.4 Modul Multi-Tenant Registration
Alur pembuatan kelas baru yang terintegrasi (Unified Form).
- **Proses Atomik**: Sekali klik membuat record di `academic_classes` sekaligus akun `super_admin` atau `ketua_kelas` yang berelasi ke kelas tersebut menggunakan `class_id`.

## 5. User Journey & User Stories

### 5.1 User Story: Manajemen Kehadiran
> **Sebagai Ketua Kelas**, saya ingin memvalidasi pengajuan izin mahasiswa agar "Sisa Nyawa" mahasiswa tersebut tidak berkurang secara otomatis.

**Kriteria Penerimaan (Acceptance Criteria)**:
1. Sistem menampilkan daftar mahasiswa yang berstatus `Alfa/Izin/Sakit` yang belum tervalidasi.
2. Terdapat tombol "Validasi" yang memicu perubahan `is_validated = 1`.
3. Setelah divalidasi, jika statusnya `Alfa`, sistem otomatis memotong kolom nyawa terkait.

### 5.2 User Journey: Akses Modul
1. Mahasiswa login ke aplikasi.
2. Membuka tab "Modul Pembelajaran".
3. Memilih mata kuliah.
4. Menekan tombol "Download" → Sistem melakukan dekripsi Base64 dan melayani file ke browser.

## 6. Spesifikasi Non-Fungsional (Non-Functional Requirements)

### 6.1 Performance & Scalability
- **Serverless Limits**: Semua endpoint harus merespon dalam < 10 detik (Vercel Free Timeout).
- **RAM Footprint**: Penggunaan Query Builder (`DB::table`) alih-alih Eloquent untuk proses batch (Simulasi & Export) guna membatasi penggunaan RAM < 256MB.
- **Latency**: Penggunaan database distributed (TiDB Cloud) di region yang sama dengan Vercel Edge.

### 6.2 Security & Data Integrity
- **Tenant Isolation**: Setiap query WAJIB menyertakan scope `class_id` untuk memastikan data antar kelas tidak bocor.
- **Session**: Menggunakan `SESSION_DRIVER=cookie` untuk mendukung sifat *stateless* pada Serverless Functions.
- **Password Policy**: Default hashing menggunakan Bcrypt (NIM + salt).

## 7. Arsitektur Teknis
### 7.1 Backend Stack
- **Core**: Laravel 13.x
- **Runtime**: PHP 8.3 (Vercel PHP Bridge)
- **Database**: TiDB Cloud (Distributed MySQL)

### 7.2 Frontend Stack
- **Framework**: Tailwind CSS v4
- **Reactivity**: Alpine.js (Lightweight DOM Manipulation)
- **Reporting**: Barryvdh/Laravel-DomPDF

### 7.3 Android Integration
- **Hybrid Approach**: Kotlin-based WebView.
- **Config**: `MainActivity.kt` dengan `JavaScriptEnabled`, `DomStorageEnabled`, dan penanganan `DownloadListener` untuk file modul.

## 8. Skema Database (Kunci Utama)
- **`academic_classes`**: Entitas tertinggi (ID, Name, Code, Department).
- **`students`**: Relasi (class_id, nim, role, device_id).
- **`class_attendances`**: Relasi (student_id, class_id, status, is_validated).
- **`cash_ledgers`**: Relasi (student_id, class_id, amount, type).
- **`learning_modules`**: Relasi (class_id, file_content, mime_type).

## 9. KPI & Metrik Kesuksesan
1.  **Akurasi Data**: 100% kesesuaian antara saldo fisik buku kas dengan saldo di aplikasi.
2.  **User Adoption**: Minimal 90% mahasiswa dalam satu kelas aktif menggunakan fitur "Smart Attendance".
3.  **Reliability**: Zero downtime pada event "War Modul" (saat dosen upload file secara massal).

## 10. Roadmap Masa Depan
- **Phase 4**: Integrasi notifikasi WhatsApp via API luar.
- **Phase 5**: AI-Powered Chatbot untuk rekap otomatis materi perkuliahan dari modul PDF.
- **Phase 6**: Dashboard visual analytics untuk Ketua Kelas dalam memantau tren kehadiran per semester.

---
**Standard Industri Compliance**: Dokumen ini disusun mengikuti struktur standar *Product Management* industri teknologi tingkat lanjut, mencakup aspek strategis, taktis, dan teknis.
