# Product Requirement Document (PRD): KelasHUB — All-in-One Class Operations Suite

**Versi**: 2.3 (Push Notification Release)  
**Status**: Live / Production  
**Author**: Ariyas Pratama Ramadhan By WaveProject.ID  
**Bahasa**: Indonesia  
**Tanggal**: 30 Mei 2026

---

## 1. Ringkasan Eksekutif (Executive Summary)
**KelasHUB** adalah solusi *enterprise-grade* untuk manajemen operasional kelas perkuliahan yang mengintegrasikan sistem kehadiran (attendance), buku kas (finance), repositori modul (LMS), manajemen jadwal, dan kini **sistem notifikasi push real-time** dalam satu platform terpadu. Proyek ini didesain khusus untuk berjalan secara efisien di infrastruktur *Serverless* dengan arsitektur *Multi-Tenant Isolation*, memungkinkan skalabilitas lintas kelas dengan biaya operasional minimal (*Low-Cost, High-Impact*).

## 2. Latar Belakang & Analisis Masalah
### 2.1 Konteks Masalah
Manajemen kelas di perguruan tinggi seringkali menghadapi kendala fragmentasi data:
- **Presensi Manual**: Risiko kecurangan (titip absen) dan sulitnya rekapitulasi real-time.
- **Transparansi Dana**: Pengelolaan uang kas yang tidak terdokumentasi dengan baik sering menimbulkan konflik.
- **Aksesibilitas Modul**: Modul kuliah yang tersebar di aplikasi chat seringkali sulit dicari kembali.
- **Keterlambatan Informasi**: Mahasiswa tidak mendapatkan notifikasi real-time saat ada tugas baru, pengumuman, atau perubahan jadwal.
- **Infrastruktur**: Kebutuhan sistem yang handal namun terkendala budget server konvensional.

### 2.2 Sasaran Produk
1. **Sentralisasi**: Satu pintu untuk semua kebutuhan administrasi kelas.
2. **Akuntabilitas**: Sistem "Sisa Nyawa" untuk mendisiplinkan kehadiran secara otomatis.
3. **Efisiensi Biaya**: Optimasi penuh pada *Vercel Free Tier* dan *TiDB Cloud*.
4. **Mobilitas**: Desain *Mobile-First* dengan implementasi *Android Native (Kotlin)*.
5. **Notifikasi Real-Time**: Push notification pop-up ke perangkat mahasiswa via OneSignal.

### 2.3 Timeline Perkembangan (Product Evolution)
- **Fase 1 (v1.0 - Jan 2024)**: MVP. Rilis dasar Attendance, Cash Ledger, dan RBAC dasar.
- **Fase 2 (v1.4 - May 2026)**: Hardening & Feature Expansion. Penambahan Learning Modules, Master Subjects, dan optimasi Base64 storage.
- **Fase 3 (v2.0 - May 2026)**: Scale to SaaS. Implementasi Multi-Tenant, Super Admin Panel.
- **Fase 4 (v2.3 - May 2026)**: Real-Time Notifications. Integrasi OneSignal push notification ke Android native.

## 3. Matriks Pengguna & Persona (User Personas)
Sistem menggunakan *Role-Based Access Control* (RBAC) yang ketat pada tabel `students`:

| Role | Deskripsi Persona | Tanggung Jawab Utama |
|:---|:---|:---|
| **Super Admin** | Pengelola Platform | Registrasi kelas baru secara atomik, audit sistem. |
| **Ketua Kelas** | Administrator Kelas | Validasi data, manajemen anggota, kontrol mode hari kuliah. Menerima notifikasi saat mahasiswa submit izin. |
| **Sekretaris** | Operasional Akademik | Input kehadiran, upload modul, manajemen jadwal. |
| **Bendahara** | Operasional Finansial | Manajemen buku kas, pelaporan keuangan. |
| **Mahasiswa** | Pengguna Akhir | Dashboard personal, download modul, submit rekap mandiri, **menerima push notification** tugas/pengumuman/kas baru. |

## 4. Spesifikasi Fungsional (Functional Specifications)

### 4.1 Modul Smart Attendance & Engine "Sisa Nyawa"
Sistem kehadiran berbasis pinalti untuk otomatisasi sanksi akademis.
- **Aturan Bisnis**: Default 3 Nyawa per mata kuliah. `Alfa` (Validated) → kurangi 1 Nyawa. Life = 0 → Status `DICEKAL`.
- **Fitur Validation**: Rekap mandiri mahasiswa tidak valid hingga disetujui Sekretaris/Ketua Kelas.
- **Integrasi Notifikasi**: Pengajuan rekap mandiri memicu notifikasi push ke Ketua Kelas.

### 4.2 Modul Financial Ledger (Buku Kas)
Sistem pencatatan transaksi kelas yang auditabel.
- Input debet/kredit dengan deskripsi dan tanggal transaksi.
- Saldo berjalan dihitung otomatis via `SUM(income) - SUM(expense)`.
- Ekspor ke PDF (Formal) dan CSV Streaming.
- **Integrasi Notifikasi**: Transaksi kas baru memicu notifikasi push ke anggota kelas.

### 4.3 Modul Academic Hub (LMS Repository)
Pusat penyimpanan materi perkuliahan dengan strategi `Base64` storage di database (menghindari keterbatasan Vercel stateless filesystem).
- Support file (PDF, DOCX, TXT) dan link eksternal.
- **Integrasi Notifikasi**: Upload modul baru memicu notifikasi push ke seluruh kelas.

### 4.4 Modul Push Notification (OneSignal)
Sistem notifikasi dua lapis: Internal (riwayat di dashboard) dan Eksternal (pop-up di HP).
- **Backend**: `NotificationService` memanggil OneSignal REST API v2 dengan `include_subscription_ids`.
- **Mobile**: SDK OneSignal di aplikasi Android meregistrasi `onesignal_id` ke endpoint `/kh/device-token`.
- **Kondisi Trigger**: Tugas baru, modul baru, transaksi kas, dan rekap mandiri.

### 4.5 Modul Multi-Tenant Registration
Alur pembuatan kelas baru yang terintegrasi (Unified Form) — satu klik membuat record `academic_classes` dan akun `ketua_kelas` secara atomik.

## 5. User Journey & User Stories

### 5.1 User Story: Notifikasi Tugas Baru
> **Sebagai Mahasiswa**, saya ingin mendapatkan notifikasi pop-up di HP saya saat ada tugas baru agar saya tidak ketinggalan deadline.

**Kriteria Penerimaan:**
1. Sekretaris input tugas baru via form di dashboard.
2. Backend memanggil `NotificationService::notifyClass()`.
3. OneSignal API mengirim push notification ke semua perangkat anggota kelas yang terdaftar.
4. Pop-up muncul di HP mahasiswa meskipun aplikasi tidak sedang dibuka.

### 5.2 User Journey: Registrasi Token Perangkat
1. Mahasiswa login ke aplikasi Android.
2. `MainApplication` menginisialisasi OneSignal SDK.
3. `MainActivity` memanggil `syncOneSignalToken()` saat dashboard dimuat.
4. Token dikirim ke backend via `POST /kh/device-token`.
5. Backend menyimpan token di kolom `onesignal_id` tabel `students`.

### 5.3 User Journey: Akses Modul
1. Mahasiswa login ke aplikasi.
2. Membuka tab "Modul Pembelajaran".
3. Memilih mata kuliah.
4. Menekan tombol "Download" → Sistem melakukan dekripsi Base64 dan melayani file ke browser.

## 6. Spesifikasi Non-Fungsional

### 6.1 Performance & Scalability
- **Serverless Limits**: Semua endpoint harus merespon dalam < 10 detik (Vercel Free Timeout).
- **RAM Footprint**: Query Builder (`DB::table`) untuk proses batch, membatasi RAM < 256MB.
- **Notifikasi Async**: Pengiriman OneSignal dilakukan via HTTP request, tidak memblok response utama.

### 6.2 Security & Data Integrity
- **Tenant Isolation**: Setiap query wajib menyertakan scope `class_id`.
- **Session**: `SESSION_DRIVER=cookie` untuk mendukung sifat stateless Serverless.
- **API Key**: `ONESIGNAL_REST_API_KEY` disimpan aman di Vercel Environment Variables.

## 7. Arsitektur Teknis
### 7.1 Backend Stack
- **Core**: Laravel 13.x | **Runtime**: PHP 8.3 (Vercel PHP Bridge) | **Database**: TiDB Cloud

### 7.2 Frontend Stack
- **Framework**: Tailwind CSS v4 | **Reactivity**: Alpine.js | **Reporting**: Barryvdh/Laravel-DomPDF

### 7.3 Android Integration
- **Native Kotlin**: `MainActivity.kt`, `LoginActivity.kt`, `DashboardActivity.kt` dengan Retrofit2 HTTP client.
- **Push Notification**: OneSignal Android SDK v5.x.
- **Token Sync**: `syncOneSignalToken()` di `MainActivity` mengirim token ke `/kh/device-token`.

## 8. Skema Database (Kunci Utama)
- **`academic_classes`**: Entitas tertinggi (ID, Name, Code, Department).
- **`students`**: Relasi (class_id, nim, role, **onesignal_id** ← baru di v2.3).
- **`class_attendances`**: Relasi (student_id, class_id, status, is_validated).
- **`cash_ledgers`**: Relasi (student_id, class_id, amount, type).
- **`learning_modules`**: Relasi (class_id, file_content, mime_type).
- **`notifications`**: Log notifikasi internal (class_id, student_id, message, is_read).

## 9. Konfigurasi Environment (Wajib)

| Key | Deskripsi |
|:---|:---|
| `ONESIGNAL_APP_ID` | ID Aplikasi OneSignal untuk pengiriman push |
| `ONESIGNAL_REST_API_KEY` | REST API Key OneSignal (disimpan aman di Vercel) |
| `APP_KEY` | Laravel Encryption Key |
| `DB_HOST`, `DB_DATABASE`, dst. | Koneksi TiDB Cloud |

## 10. KPI & Metrik Kesuksesan
1. **Akurasi Data**: 100% kesesuaian saldo fisik dengan saldo di aplikasi.
2. **Notifikasi Delivery**: > 95% push notification berhasil terkirim ke perangkat terdaftar.
3. **Token Coverage**: > 80% mahasiswa aktif perangkatnya terdaftar dalam 1 minggu pertama penggunaan.
4. **Reliability**: Zero downtime pada event "War Modul".

## 11. Roadmap Masa Depan
- **Phase 5**: AI-Powered Chatbot untuk rekap otomatis materi perkuliahan dari modul PDF.
- **Phase 6**: Dashboard visual analytics untuk tren kehadiran per semester.
- **Phase 7**: Pindah penyimpanan file ke S3 Storage jika volume data meningkat drastis.

---
**Standard Industri Compliance**: Dokumen ini disusun mengikuti struktur standar *Product Management* industri teknologi tingkat lanjut.
