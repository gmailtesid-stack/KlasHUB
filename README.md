# 🌌 KelasHUB — Stealth-Theme All-in-One Class Operations Suite

<div align="center">

[![Deployed on Vercel](https://img.shields.io/badge/Deployed-Vercel-black?logo=vercel)](https://klas-hub.vercel.app)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://php.net)
[![TiDB Cloud](https://img.shields.io/badge/Database-TiDB%20Cloud-orange)](https://tidbcloud.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

**Platform manajemen kelas kuliah modern, cepat, dan terpusat.**
Dibangun di atas Laravel Serverless (Vercel) · TiDB Cloud · Tailwind CSS v4 · Alpine.js

[🚀 Live Demo](https://klas-hub.vercel.app) · [📋 Changelog](CHANGELOG.md) · [🤝 Contributing](CONTRIBUTING.md) · [📖 API Docs](docs/API.md)

</div>

---

## 📖 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Arsitektur & Stack Teknologi](#-arsitektur--stack-teknologi)
- [Struktur Database](#-struktur-database)
- [Instalasi Lokal](#-instalasi-lokal)
- [Deployment ke Vercel](#-deployment-ke-vercel)
- [Referensi Route & API](#-referensi-route--api)
- [Role-Based Access Control](#-role-based-access-control-rbac)
- [Engine Simulasi & Laporan](#-engine-simulasi--laporan)
- [Optimasi Vercel Serverless](#-optimasi-vercel-serverless)
- [Aplikasi Android WebView](#-aplikasi-android-webview)
- [Filosofi Desain UI](#-filosofi-desain-ui)
- [Kontribusi](#-kontribusi)

---

## 🎯 Tentang Proyek

**KelasHUB** adalah platform operasional kelas perkuliahan *All-in-One* yang dirancang untuk menggantikan proses administrasi manual yang rumit — mulai dari presensi, manajemen keuangan kas, repositori modul pembelajaran, hingga pengumuman kelas — ke dalam satu aplikasi terpadu yang bisa diakses dari ponsel.

Platform ini dikembangkan dengan filosofi **"Stealth Operations"**: tampilannya ramping dan gelap (dark-mode), tapi di baliknya menyimpan kekuatan penuh untuk mengelola seluruh operasional kelas secara real-time.

**Dikembangkan oleh**: Wave Project.ID  
**Stack**: Laravel 13 (Serverless) + TiDB Cloud + Tailwind CSS v4 + Alpine.js  
**Target Platform**: Vercel Free Tier (Serverless) + Android WebView

---

## ✨ Fitur Utama

### 1. 🎯 Smart Attendance & "Sisa Nyawa" Engine
Sistem kehadiran cerdas berbasis **penalty point**:
- Setiap mahasiswa mendapatkan **3 Nyawa** per mata kuliah per semester.
- Setiap absen (**Alfa**) yang tervalidasi mengurangi 1 nyawa.
- Jika nyawa = **0**, status otomatis berubah menjadi `DICEKAL` (Nilai E) dengan peringatan berkedip merah di dashboard.
- Mahasiswa juga bisa submit **Rekap Mandiri** (Sakit/Izin) yang memerlukan validasi Sekretaris.

### 2. 🔐 Role-Based Access Control (RBAC)
| Role | Hak Akses |
|:---|:---|
| `super_admin` | Registrasi kelas baru & Ketua Kelas, kelola semua data |
| `ketua_kelas` | Validasi semua data, tambah/hapus anggota, kelola jadwal, laporan |
| `sekretaris` | Input absensi, tampilkan daftar hadir, kelola jadwal |
| `bendahara` | Input & kelola kas kelas, laporan keuangan |
| `mahasiswa` | Lihat data pribadi, unduh modul, rekap mandiri |

### 3. 📚 Academic Hub & Repositori File
- Upload modul pembelajaran (PDF, DOC, TXT) langsung disimpan ke database sebagai `base64` — tanpa butuh storage filesystem Vercel.
- Download modul bisa dilakukan langsung dari dashboard.
- Manajemen tugas (individual & kelompok) dengan deadline otomatis.

### 4. 💰 Financial Ledger (Buku Kas)
- Catat pemasukan dan pengeluaran kelas dengan deskripsi lengkap.
- Saldo berjalan otomatis dihitung dan ditampilkan di dashboard.
- Export laporan keuangan ke **PDF** (format formal monokrom) dan **Excel/CSV** (streaming, RAM ~0 MB).

### 5. 📅 Jadwal & Mode Pembelajaran
- Jadwal perkuliahan harian per mata kuliah.
- Toggle mode **Online ↔ Offline** secara real-time oleh Ketua Kelas/Sekretaris.
- Cron job otomatis reset jadwal setiap hari pukul 23:59 WIB.

### 6. 🏛️ Super Admin Panel
- Panel khusus untuk mendaftarkan kelas baru **sekaligus** akunnya dalam satu form terpadu (Unified Class Registration).
- Password Ketua Kelas dibuat otomatis: `NIM + "KK"`.
- Isolasi penuh antar kelas berbasis `class_id`.

### 7. 🤖 Simulasi Engine (Vercel-Optimized)
- Endpoint `/simulasi` yang mensimulasikan aktivitas nyata 5 kelas secara otomatis.
- Berjalan dalam **< 5 detik** — aman dari timeout 10 detik Vercel Free.
- Menggunakan `DB::table()` murni (tanpa Eloquent) untuk efisiensi RAM.

---

## 🛠️ Arsitektur & Stack Teknologi

```
┌─────────────────────────────────────────────────────────┐
│                   CLIENT LAYER                          │
│  Android App (Kotlin WebView)  /  Mobile Browser        │
└───────────────────────┬─────────────────────────────────┘
                        │ HTTPS
┌───────────────────────▼─────────────────────────────────┐
│                   VERCEL EDGE                           │
│  Static Assets → @vercel/static                         │
│  PHP Requests  → vercel-php@0.9.0 → api/index.php       │
└───────────────────────┬─────────────────────────────────┘
                        │
┌───────────────────────▼─────────────────────────────────┐
│              LARAVEL 13 APPLICATION CORE                │
│  ┌──────────┐  ┌───────────┐  ┌───────────────────────┐ │
│  │  Routes  │→│Controllers │→│  Models (Eloquent)      │ │
│  │ web.php  │  │ Engine    │  │  Student, Assignment,  │ │
│  └──────────┘  │ Laporan   │  │  CashLedger, Module.. │ │
│                │ Simulasi  │  └──────────┬────────────┘ │
│                └───────────┘             │               │
└──────────────────────────────────────────┼───────────────┘
                                           │ MySQL Protocol
┌──────────────────────────────────────────▼───────────────┐
│                    TiDB CLOUD                            │
│  Distributed MySQL-Compatible · 5 Tables + relations     │
│  academic_classes, students, assignments,                 │
│  cash_ledgers, class_attendances, learning_modules, ...  │
└──────────────────────────────────────────────────────────┘
```

### Dependency Utama

| Komponen | Versi | Fungsi |
|:---|:---:|:---|
| Laravel | 13.x | Backend framework utama |
| PHP | 8.3+ | Runtime |
| TiDB Cloud | - | Database produksi (MySQL-compat) |
| Tailwind CSS | v4 | Styling framework |
| Alpine.js | 3.x | Reaktivitas frontend ringan |
| barryvdh/laravel-dompdf | ^3.1 | Ekspor laporan PDF |
| vercel-php | 0.9.0 | PHP runtime di Vercel Serverless |

---

## 🗄️ Struktur Database

### Skema Relasi Antar Tabel

```
academic_classes (1) ──────── (N) students
academic_classes (1) ──────── (N) assignments
academic_classes (1) ──────── (N) cash_ledgers
academic_classes (1) ──────── (N) class_attendances
academic_classes (1) ──────── (N) learning_modules
academic_classes (1) ──────── (N) academic_schedules
academic_classes (1) ──────── (N) notifications

students (1) ─────────────── (N) class_attendances
students (1) ─────────────── (N) cash_ledgers
students (1) ─────────────── (N) notifications
```

### Detail Tabel

#### `academic_classes`
```sql
id              BIGINT PK AUTO_INCREMENT
name            VARCHAR(255)    -- "Teknik Informatika - 06TPLE013"
code            VARCHAR(50)     -- "06TPLE013" (unique key isolasi data)
academic_year   VARCHAR(20)     -- "2023/2024"
department      VARCHAR(255)    -- "Teknik Informatika" (added v2)
contact         VARCHAR(255)    -- No. HP / Email Ketua (added v2)
created_at, updated_at
```

#### `students`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
nim             VARCHAR(20) UNIQUE  -- Nomor Induk Mahasiswa
name            VARCHAR(255)        -- Digunakan sebagai username login
password        VARCHAR(255)        -- bcrypt(NIM + "KK") untuk Ketua
role            ENUM('ketua_kelas','sekretaris','bendahara','mahasiswa','super_admin')
device_id       VARCHAR(255) NULL   -- Untuk keamanan perangkat
created_at, updated_at
```

#### `class_attendances`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
student_id      FK → students.id
subject_name    VARCHAR(255)  -- Harus sesuai dengan master_subjects.name
attendance_date DATE
status          ENUM('Hadir','Izin','Sakit','Alfa')
notes           TEXT NULL
is_validated    BOOLEAN DEFAULT TRUE
created_at, updated_at
```

#### `cash_ledgers`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
student_id      FK → students.id  (nullable)
type            ENUM('income','expense')
amount          INTEGER       -- Dalam Rupiah
description     VARCHAR(255)
transaction_date DATE
is_validated    BOOLEAN
created_at, updated_at
```

#### `learning_modules`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
subject_name    VARCHAR(255)
title           VARCHAR(255)
type            ENUM('file','link')
file_content    LONGTEXT NULL -- Base64 encoded (disimpan langsung di DB)
mime_type       VARCHAR(100) NULL
file_path       VARCHAR(255) NULL
link_url        VARCHAR(500) NULL
is_validated    BOOLEAN
created_at, updated_at
```

#### `assignments`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
subject_name    VARCHAR(255)
title           VARCHAR(255)
description     TEXT NULL
deadline        DATETIME
material_link   VARCHAR(500) NULL
type            ENUM('individual','group')
members         TEXT NULL     -- Anggota kelompok (opsional)
is_validated    BOOLEAN
created_at, updated_at
```

#### `notifications`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
student_id      FK → students.id  (nullable)
message         VARCHAR(500)
is_read         BOOLEAN DEFAULT FALSE
created_at, updated_at
```

#### `master_subjects`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id  (nullable)
name            VARCHAR(255) UNIQUE
sks             INT DEFAULT 2
code            VARCHAR(50)
default_lecturer VARCHAR(255) NULL
created_at, updated_at
```

---

## ⚙️ Instalasi Lokal

### Prasyarat
- PHP >= 8.3 dengan ekstensi: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer >= 2.x
- Node.js >= 18.x & npm
- Database: SQLite (lokal) atau MySQL / TiDB Cloud (staging/prod)

### Langkah-langkah

```bash
# 1. Clone repositori
git clone https://github.com/gmailtesid-stack/KlasHUB.git
cd KlasHUB

# 2. Install dependency PHP
composer install

# 3. Install dependency Node.js & build asset
npm install
npm run build

# 4. Siapkan environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env (SQLite paling cepat untuk lokal)
# DB_CONNECTION=sqlite
# (file database.sqlite akan dibuat otomatis)

# 6. Jalankan migrasi
php artisan migrate

# 7. Jalankan server lokal
php artisan serve
```

Buka `http://localhost:8000` di browser Anda.

### Konfigurasi .env Penting

```env
APP_NAME=KelasHUB
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Untuk TiDB Cloud / MySQL:
# DB_CONNECTION=mysql
# DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
# DB_PORT=4000
# DB_DATABASE=kelashub
# DB_USERNAME=your_user
# DB_PASSWORD=your_password
# MYSQL_ATTR_SSL_CA=/path/to/cacert.pem

SESSION_DRIVER=cookie
CACHE_STORE=database
```

---

## 🚀 Deployment ke Vercel

KelasHUB sudah dikonfigurasi secara penuh untuk berjalan di Vercel Free Tier.

### Langkah Deploy

```bash
# 1. Install Vercel CLI
npm install -g vercel

# 2. Login ke akun Vercel
vercel login

# 3. Deploy (dari root proyek)
vercel --prod
```

Atau cukup dengan **push ke GitHub** — Vercel akan otomatis trigger deploy baru.

### Konfigurasi `vercel.json`

File `vercel.json` di root proyek sudah menyertakan:
- **Build**: PHP via `vercel-php@0.9.0`, static files via `@vercel/static`
- **Routes**: Semua request diarahkan ke `api/index.php` kecuali asset publik
- **Env**: Variabel produksi seperti `SESSION_DRIVER=cookie`, `VIEW_COMPILED_PATH=/tmp`
- **Crons**: Reset jadwal otomatis setiap `23:59 WIB` (`59 16 * * *` UTC)

### Environment Variables di Vercel Dashboard

Tambahkan via Vercel Dashboard > Project Settings > Environment Variables:

| Key | Value | Keterangan |
|:---|:---|:---|
| `APP_KEY` | `base64:xxx...` | Generate: `php artisan key:generate --show` |
| `DB_HOST` | `gateway01.ap-...` | TiDB Cloud endpoint |
| `DB_PORT` | `4000` | TiDB Cloud port |
| `DB_DATABASE` | `kelashub` | Nama database |
| `DB_USERNAME` | `xxx.root` | Username TiDB |
| `DB_PASSWORD` | `your_password` | Password TiDB |
| `MYSQL_ATTR_SSL_CA` | `/var/task/cacert.pem` | SSL cert path di Vercel |

---

## 🗺️ Referensi Route & API

### Public Routes
| Method | URL | Deskripsi |
|:---:|:---|:---|
| `GET` | `/` | Redirect ke `/login` |
| `GET` | `/login` | Halaman login |
| `POST` | `/login` | Proses autentikasi |
| `POST` | `/logout` | Logout & invalidasi sesi |

### Protected Routes (Middleware: `auth`)
| Method | URL | Controller@Method | Role |
|:---:|:---|:---|:---:|
| `GET` | `/dashboard` | `KelasHubEngineController@getStudentDashboard` | Semua |
| `POST` | `/kh/password` | `KelasHubEngineController@updatePassword` | Semua |
| `POST` | `/kh/attendance` | `KelasHubEngineController@storeAttendance` | Semua |
| `GET` | `/simulasi` | `SimulasiController@jalankanSimulasi` | Semua |
| `GET` | `/report/pdf/{id}` | `LaporanController@exportPdf` | Semua |
| `GET` | `/report/excel/{id}` | `LaporanController@exportExcel` | Semua |

### Protected Routes (Middleware: `role:ketua_kelas,sekretaris,bendahara`)
| Method | URL | Controller@Method |
|:---:|:---|:---|
| `POST` | `/kh/schedule` | `storeSchedule` |
| `POST` | `/kh/schedule/toggle-delivery` | `toggleDeliveryType` |
| `POST` | `/kh/assignment` | `storeAssignment` |
| `POST` | `/kh/module` | `storeModule` |
| `GET` | `/kh/module/{id}/download` | `downloadModule` |
| `POST` | `/kh/cash` | `storeCashLedger` |
| `POST` | `/kh/student` | `storeStudent` |
| `DELETE` | `/kh/subject/{id}` | `deleteSubject` |
| `DELETE` | `/kh/student/{id}` | `deleteStudent` |
| `POST` | `/kh/master-subject` | `storeMasterSubject` |

### Protected Routes (Middleware: `role:ketua_kelas`)
| Method | URL | Controller@Method |
|:---:|:---|:---|
| `POST` | `/kh/validate` | `validateData` |
| `POST` | `/kh/class` | `storeUnifiedClass` |
| `POST` | `/kh/student/{id}/role` | `updateStudentRole` |
| `GET` | `/kh/api/dashboard-data` | `getDashboardData` |
| `GET` | `/kh/cron/reset-schedule` | Reset jadwal (cron) |

### Report Routes
| Method | URL | Laporan |
|:---:|:---|:---|
| `GET` | `/kh/reports/attendance/pdf` | PDF Presensi |
| `GET` | `/kh/reports/attendance/excel` | Excel Presensi (CSV Stream) |
| `GET` | `/kh/reports/cash/pdf` | PDF Keuangan |
| `GET` | `/kh/reports/cash/excel` | Excel Keuangan (CSV Stream) |

---

## 🔐 Role-Based Access Control (RBAC)

Sistem RBAC KelasHUB diimplementasikan via **custom middleware** `role` yang memeriksa field `role` pada tabel `students`.

```php
// Middleware: App\Http\Middleware\RoleMiddleware
// Contoh penggunaan di routes:
Route::middleware(['role:ketua_kelas,sekretaris'])->group(function () { ... });
```

### Matriks Hak Akses Fitur

| Fitur | super_admin | ketua_kelas | sekretaris | bendahara | mahasiswa |
|:---|:---:|:---:|:---:|:---:|:---:|
| Registrasi Kelas | ✅ | ❌ | ❌ | ❌ | ❌ |
| Tambah Mahasiswa | ✅ | ✅ | ✅ | ✅ | ❌ |
| Hapus Mahasiswa | ✅ | ✅ | ✅ | ✅ | ❌ |
| Input Absensi | ✅ | ✅ | ✅ | ✅ | 🔘 Mandiri |
| Validasi Data | ✅ | ✅ | ❌ | ❌ | ❌ |
| Upload Modul | ✅ | ✅ | ✅ | ✅ | ❌ |
| Download Modul | ✅ | ✅ | ✅ | ✅ | ✅ |
| Input Kas | ✅ | ✅ | ✅ | ✅ | ❌ |
| Export Laporan | ✅ | ✅ | ✅ | ✅ | ❌ |
| Kelola Jadwal | ✅ | ✅ | ✅ | ✅ | ❌ |
| Toggle Online/Offline | ✅ | ✅ | ✅ | ✅ | ❌ |
| Ubah Role Mahasiswa | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 🤖 Engine Simulasi & Laporan

### Simulasi Engine (`SimulasiController`)
Digunakan untuk mengisi data dummy secara otomatis untuk keperluan pengujian.

**Endpoint**: `GET /simulasi`

```
Loop 4 detik (aman dari timeout 10 detik Vercel):
  - Pilih acak 1 dari 5 kelas (class_id 1-5)
  - Pilih acak 1 dari 4 aksi:
    1. Insert ke assignments
    2. Insert ke cash_ledgers
    3. Insert ke learning_modules
    4. Insert ke notifications
```

**Response JSON:**
```json
{
  "success": true,
  "message": "Simulasi selesai dalam rentang < 5 detik",
  "total_inserted": 6,
  "environment": "Vercel Optimized"
}
```

### Export Laporan (`LaporanController`)

#### PDF (`/report/pdf/{class_id}`)
- Menggunakan `barryvdh/laravel-dompdf`
- Template `resources/views/reports/pdf.blade.php` (monokrom formal)
- Berisi tabel kas + **rekap saldo akhir** di bawah tabel

#### Excel/CSV (`/report/excel/{class_id}`)
- **Tanpa package berat** — CSV murni via `fputcsv`
- Stream langsung ke `php://output` → RAM Vercel tetap ~0 MB
- Menggunakan `chunk(200)` agar tidak overload memori

---

## ⚡ Optimasi Vercel Serverless

KelasHUB didesain khusus untuk berjalan efisien di Vercel Free Tier dengan batasan:
- ⏱ **Timeout**: 10 detik per request
- 💾 **RAM**: 256 MB per function invocation
- 📦 **Bundle Size**: Maksimal 250 MB (compressed)

### Teknik Optimasi yang Diterapkan

| Masalah | Solusi |
|:---|:---|
| Session tidak persistent | `SESSION_DRIVER=cookie` (encrypted, stateless) |
| File cache tidak bisa ditulis | `VIEW_COMPILED_PATH=/tmp`, semua cache → `/tmp` |
| Eloquent terlalu berat di loop | Gunakan `DB::table()` (Query Builder) murni |
| Recursive eager loading | Hapus `BelongsToClass` trait dari semua model |
| Export data besar | CSV Streaming → `fputcsv` ke `php://output` |
| Cold boot lambat | Bootstrap minimal, lazy-loading model |
| File storage tidak tersedia | Simpan file modul sebagai `base64` di database |
| Cron job tidak ada di Vercel Free | Gunakan Vercel Cron (`vercel.json`) + route khusus |

---

## 📱 Aplikasi Android WebView

Proyek ini menyertakan wrapper Android native (`android-webview/`) yang membungkus URL Vercel dalam tampilan aplikasi mobile penuh layar.

### Struktur Folder
```
android-webview/
├── app/
│   ├── src/main/
│   │   ├── java/com/waveproject/kelashub/
│   │   │   └── MainActivity.kt
│   │   ├── res/
│   │   │   ├── layout/activity_main.xml
│   │   │   └── values/strings.xml
│   │   └── AndroidManifest.xml
│   └── build.gradle
├── build.gradle
└── gradle/
```

### `MainActivity.kt` — Fitur WebView Premium
```kotlin
val settings = webView.settings.apply {
    javaScriptEnabled = true      // Alpine.js & form interaktif
    domStorageEnabled = true      // LocalStorage untuk session
    useWideViewPort = true        // Layout responsif penuh
    loadWithOverviewMode = true   // Fit ke layar HP
    databaseEnabled = true        // IndexedDB support
    allowFileAccess = true        // Download modul
}
// Back-button navigation: webView.canGoBack() → webView.goBack()
// Target URL: https://klas-hub.vercel.app
```

### Cara Build APK
```bash
# Di Android Studio:
# Build > Build Bundle(s) / APK(s) > Build APK(s)

# Output: android-webview/app/build/outputs/apk/debug/app-debug.apk
```

---

## 🎨 Filosofi Desain UI

### "Stealth Dark Operations" Theme
KelasHUB menggunakan estetika **Zinc-900 Stealth** — warna dasar hitam pekat dengan aksen abu-abu dingin dan highlight biru elektrik. Terinspirasi dari UI terminal hacker dan dashboard sistem keamanan.

### Prinsip Desain

| Prinsip | Implementasi |
|:---|:---|
| **Mobile-First** | Bottom Navigation Bar, satu jempol operasional |
| **Data Clarity** | Label menonjol + efek glow untuk info kritis |
| **Micro-Animation** | Alpine.js transitions pada semua modal & tab |
| **Glassmorphism** | `backdrop-blur-md` + `bg-white/[0.02]` pada card |
| **Premium Feel** | Custom font tracking, letter-spacing, shadow-xl |

### Responsive Breakpoints
- **Mobile** (`< md`): Bottom nav, stacked cards, minimized table
- **Desktop** (`>= md`): Sidebar nav, grid layout, expanded table

---

## 🤝 Kontribusi

Silakan baca [CONTRIBUTING.md](CONTRIBUTING.md) untuk panduan kontribusi.

```bash
# Fork repo → Clone → Buat branch fitur
git checkout -b feature/nama-fitur

# Commit dengan format konvensional
git commit -m "feat: tambah fitur xyz"

# Push & buat Pull Request
git push origin feature/nama-fitur
```

---

## 📜 Lisensi

Proyek ini dilisensikan di bawah **MIT License** — bebas digunakan, dimodifikasi, dan didistribusikan.

---

<div align="center">

Dikembangkan dengan ❤️ oleh **Wave Project.ID**  
Untuk memajukan efisiensi administrasi akademis mahasiswa Indonesia 🇮🇩

</div>
