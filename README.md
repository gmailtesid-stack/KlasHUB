# 🌌 KelasHUB — Stealth-Theme All-in-One Class Operations Suite

<div align="center">

[![Deployed on Vercel](https://img.shields.io/badge/Deployed-Vercel-black?logo=vercel)](https://klas-hub.vercel.app)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://php.net)
[![TiDB Cloud](https://img.shields.io/badge/Database-TiDB%20Cloud-orange)](https://tidbcloud.com)
[![OneSignal](https://img.shields.io/badge/Notifications-OneSignal-E54B4D?logo=onesignal)](https://onesignal.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

**Platform manajemen kelas kuliah modern, cepat, terpusat, dan kini dengan push notification real-time.**  
Dibangun di atas Laravel Serverless (Vercel) · TiDB Cloud · Tailwind CSS v4 · Alpine.js · OneSignal

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
- [Sistem Push Notification](#-sistem-push-notification-onesignal)
- [Engine Simulasi & Laporan](#-engine-simulasi--laporan)
- [Optimasi Vercel Serverless](#-optimasi-vercel-serverless)
- [Aplikasi Android Native](#-aplikasi-android-native-kotlin)
- [Filosofi Desain UI](#-filosofi-desain-ui)
- [Kontribusi](#-kontribusi)

---

## 🎯 Tentang Proyek

**KelasHUB** adalah platform operasional kelas perkuliahan *All-in-One* yang dirancang untuk menggantikan proses administrasi manual — mulai dari presensi, manajemen keuangan kas, repositori modul pembelajaran, manajemen jadwal, hingga **notifikasi push real-time** — ke dalam satu aplikasi terpadu yang bisa diakses dari ponsel.

Platform ini dikembangkan dengan filosofi **"Stealth Operations"**: tampilannya ramping dan gelap (dark-mode), tapi di baliknya menyimpan kekuatan penuh untuk mengelola seluruh operasional kelas secara real-time.

**Dikembangkan oleh**: Wave Project.ID  
**Stack**: Laravel 13 (Serverless) + TiDB Cloud + Tailwind CSS v4 + Alpine.js + OneSignal  
**Target Platform**: Vercel Free Tier (Serverless) + Android Native (Kotlin)  
**Versi Saat Ini**: v2.3.0

---

## ✨ Fitur Utama

### 1. 🔔 Push Notification Real-Time (OneSignal) ← **Baru di v2.3**
Notifikasi pop-up langsung ke HP mahasiswa seperti WhatsApp:
- Notifikasi otomatis saat ada **tugas baru**, **modul baru**, **transaksi kas**, atau **pengumuman**.
- Terintegrasi dengan aplikasi Android native via **OneSignal SDK v5**.
- Backend menggunakan **OneSignal REST API v2** (`include_subscription_ids`) untuk pengiriman yang ditargetkan.
- Token perangkat mahasiswa didaftarkan otomatis saat login via endpoint `POST /kh/device-token`.

### 2. 🎯 Smart Attendance & "Sisa Nyawa" Engine
Sistem kehadiran cerdas berbasis **penalty point**:
- Setiap mahasiswa mendapatkan **3 Nyawa** per mata kuliah per semester.
- Setiap absen (**Alfa**) yang tervalidasi mengurangi 1 nyawa.
- Jika nyawa = **0**, status otomatis `DICEKAL` (Nilai E) dengan peringatan berkedip merah di dashboard.
- Mahasiswa bisa submit **Rekap Mandiri** (Sakit/Izin) yang memerlukan validasi Ketua/Sekretaris.

### 3. 🔐 Role-Based Access Control (RBAC)
| Role | Hak Akses |
|:---|:---|
| `super_admin` | Registrasi kelas baru & Ketua Kelas, kelola semua data |
| `ketua_kelas` | Validasi semua data, tambah/hapus anggota, kelola jadwal, laporan |
| `sekretaris` | Input absensi, tampilkan daftar hadir, kelola jadwal, upload modul |
| `bendahara` | Input & kelola kas kelas, laporan keuangan |
| `mahasiswa` | Lihat data pribadi, unduh modul, rekap mandiri, terima notifikasi push |

### 4. 📚 Academic Hub & Repositori File
- Upload modul pembelajaran (PDF, DOC, TXT) langsung disimpan ke database sebagai `base64` — tanpa filesystem Vercel.
- Download modul bisa dilakukan langsung dari dashboard.
- Manajemen tugas (individual & kelompok) dengan deadline otomatis.

### 5. 💰 Financial Ledger (Buku Kas)
- Catat pemasukan dan pengeluaran kelas dengan deskripsi lengkap.
- Saldo berjalan otomatis dihitung dan ditampilkan di dashboard.
- Export laporan keuangan ke **PDF** dan **Excel/CSV** (streaming, RAM ~0 MB).

### 6. 📅 Jadwal & Mode Pembelajaran
- Jadwal perkuliahan harian per mata kuliah.
- Toggle mode **Online ↔ Offline** secara real-time.
- Cron job otomatis reset jadwal setiap hari pukul 23:59 WIB.

### 7. 🏛️ Super Admin Panel
- Panel khusus untuk mendaftarkan kelas baru **sekaligus** akunnya dalam satu form terpadu.
- Password Ketua Kelas dibuat otomatis: `NIM + "KK"`.
- Isolasi penuh antar kelas berbasis `class_id`.

### 8. 🤖 Simulasi Engine (Vercel-Optimized)
- Endpoint `/simulasi` mensimulasikan aktivitas nyata 5 kelas.
- Berjalan dalam **< 5 detik** — aman dari timeout Vercel Free.

---

## 🛠️ Arsitektur & Stack Teknologi

```
┌─────────────────────────────────────────────────────────┐
│                   CLIENT LAYER                          │
│  Android App (Kotlin Native)  /  Mobile Browser         │
└───────────┬───────────────────────────┬─────────────────┘
            │ HTTPS (Retrofit2)         │ OneSignal SDK
            │                           ▼
┌───────────▼──────────┐   ┌───────────────────────────────┐
│   VERCEL EDGE        │   │  OneSignal Platform            │
│  vercel-php → api/   │   │  Push Notification Delivery    │
│  Laravel 13 Backend  │   └───────────────────────────────┘
└───────────┬──────────┘
            │ MySQL Protocol (SSL)
┌───────────▼──────────────────────────────────────────────┐
│                    TiDB CLOUD                            │
│  Distributed MySQL-Compatible · 8 Tables + relations     │
└──────────────────────────────────────────────────────────┘
```

### Dependency Utama

| Komponen | Versi | Fungsi |
|:---|:---:|:---|
| Laravel | 13.x | Backend framework utama |
| PHP | 8.3+ | Runtime |
| TiDB Cloud | — | Database produksi (MySQL-compat) |
| Tailwind CSS | v4 | Styling framework |
| Alpine.js | 3.x | Reaktivitas frontend ringan |
| OneSignal | REST API v2 | Push notification eksternal |
| OneSignal Android SDK | 5.x | Push notification mobile |
| Retrofit2 | 2.9.0 | HTTP client Android |
| barryvdh/laravel-dompdf | ^3.1 | Ekspor laporan PDF |
| vercel-php | 0.9.0 | PHP runtime di Vercel Serverless |

---

## 🗄️ Struktur Database

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
students.onesignal_id ←───── OneSignal Subscription ID
```

### Detail Tabel Kunci

#### `students`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
nim             VARCHAR(20) UNIQUE
name            VARCHAR(255)
password        VARCHAR(255)         -- bcrypt
role            ENUM('ketua_kelas','sekretaris','bendahara','mahasiswa','super_admin')
onesignal_id    VARCHAR(255) NULL    -- ← BARU v2.3: OneSignal Subscription ID
device_id       VARCHAR(255) NULL
created_at, updated_at
```

#### `notifications`
```sql
id              BIGINT PK
class_id        FK → academic_classes.id
student_id      FK → students.id  (nullable = untuk semua anggota kelas)
message         VARCHAR(500)
is_read         BOOLEAN DEFAULT FALSE
created_at, updated_at
```

---

## ⚙️ Instalasi Lokal

### Prasyarat
- PHP >= 8.3 dengan ekstensi: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer >= 2.x
- Node.js >= 18.x & npm

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

# 5. Konfigurasi database & OneSignal di .env
# DB_CONNECTION=sqlite
# ONESIGNAL_APP_ID=04a9cff3-874a-4e84-96c0-f79cfa86d255
# ONESIGNAL_REST_API_KEY=os_v2_app_...

# 6. Jalankan migrasi
php artisan migrate

# 7. Jalankan server lokal
php artisan serve
```

### Konfigurasi .env Penting

```env
APP_NAME=KelasHUB
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

SESSION_DRIVER=cookie
CACHE_STORE=database

# OneSignal (untuk push notification)
ONESIGNAL_APP_ID=04a9cff3-874a-4e84-96c0-f79cfa86d255
ONESIGNAL_REST_API_KEY=os_v2_app_...
```

---

## 🚀 Deployment ke Vercel

### Langkah Deploy

```bash
# 1. Install Vercel CLI
npm install -g vercel

# 2. Login ke akun Vercel
vercel login

# 3. Deploy
vercel --prod
```

Atau cukup **push ke GitHub** — Vercel otomatis trigger deploy baru.

### Environment Variables di Vercel Dashboard

| Key | Keterangan |
|:---|:---|
| `APP_KEY` | Generate: `php artisan key:generate --show` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | TiDB Cloud |
| `MYSQL_ATTR_SSL_CA` | `/var/task/cacert.pem` |
| `ONESIGNAL_APP_ID` | `04a9cff3-874a-4e84-96c0-f79cfa86d255` |
| `ONESIGNAL_REST_API_KEY` | REST API Key dari OneSignal Dashboard |

---

## 🗺️ Referensi Route & API

### Public Routes
| Method | URL | Deskripsi |
|:---:|:---|:---|
| `GET` | `/` | Redirect ke `/login` |
| `GET/POST` | `/login` | Halaman & proses login |
| `POST` | `/logout` | Logout & invalidasi sesi |

### Mobile API Routes
| Method | URL | Deskripsi |
|:---:|:---|:---|
| `POST` | `/kh/device-token` | Registrasi OneSignal token perangkat |
| `GET` | `/kh/api/dashboard-data` | Data dashboard untuk Android native |

### Protected Routes (Middleware: `auth`)
| Method | URL | Role |
|:---:|:---|:---:|
| `GET` | `/dashboard` | Semua |
| `POST` | `/kh/password` | Semua |
| `POST` | `/kh/attendance` | Semua |
| `GET` | `/report/pdf/{id}` | Semua |

### Protected Routes (Admin)
| Method | URL | Middleware |
|:---:|:---|:---|
| `POST` | `/kh/schedule` | `role:ketua_kelas,sekretaris,bendahara` |
| `POST` | `/kh/module` | `role:ketua_kelas,sekretaris,bendahara` |
| `POST` | `/kh/cash` | `role:ketua_kelas,sekretaris,bendahara` |
| `POST` | `/kh/validate` | `role:ketua_kelas` |
| `POST` | `/kh/class` | `role:ketua_kelas` (Super Admin) |

Lihat [docs/API.md](docs/API.md) untuk dokumentasi lengkap.

---

## 🔐 Role-Based Access Control (RBAC)

| Fitur | super_admin | ketua_kelas | sekretaris | bendahara | mahasiswa |
|:---|:---:|:---:|:---:|:---:|:---:|
| Registrasi Kelas | ✅ | ❌ | ❌ | ❌ | ❌ |
| Kirim Notifikasi Push | ✅ | ✅ auto | ✅ auto | ✅ auto | ❌ |
| Input Absensi | ✅ | ✅ | ✅ | ✅ | 🔘 Mandiri |
| Validasi Data | ✅ | ✅ | ❌ | ❌ | ❌ |
| Upload Modul | ✅ | ✅ | ✅ | ✅ | ❌ |
| Download Modul | ✅ | ✅ | ✅ | ✅ | ✅ |
| Input Kas | ✅ | ✅ | ✅ | ✅ | ❌ |
| Export Laporan | ✅ | ✅ | ✅ | ✅ | ❌ |

---

## 🔔 Sistem Push Notification (OneSignal)

KelasHUB v2.3 menggunakan sistem notifikasi **dua lapis**:

| Jenis | Mekanisme | Platform |
|:---|:---|:---|
| **Internal** | Disimpan di tabel `notifications`, tampil di dashboard | Web & Mobile |
| **Eksternal** | Pop-up via OneSignal REST API v2 | Android Native |

### Trigger Otomatis Notifikasi
| Aksi | Target Penerima |
|:---|:---|
| Tambah Tugas Baru | Semua anggota kelas |
| Upload Modul | Semua anggota kelas |
| Input Transaksi Kas | Semua anggota kelas |
| Submit Rekap Mandiri | Ketua Kelas & Sekretaris |
| Validasi Rekap | Mahasiswa yang bersangkutan |

### Arsitektur Notifikasi
```
Action (Controller)
    → NotificationService::notifyClass($classId, $msg)
        → Insert ke tabel notifications (internal)
        → HTTP POST ke OneSignal API v2 (eksternal)
            → OneSignal Platform
                → Push ke perangkat mahasiswa (onesignal_id)
```

---

## ⚡ Optimasi Vercel Serverless

| Masalah | Solusi |
|:---|:---|
| Session tidak persistent | `SESSION_DRIVER=cookie` (encrypted, stateless) |
| File cache tidak bisa ditulis | `VIEW_COMPILED_PATH=/tmp` |
| Eloquent terlalu berat di loop | Gunakan `DB::table()` murni |
| Export data besar | CSV Streaming via `fputcsv` ke `php://output` |
| File storage tidak tersedia | Simpan file modul sebagai `base64` di database |
| Cron job tidak ada di Vercel Free | Vercel Cron (`vercel.json`) + route khusus |

---

## 📱 Aplikasi Android Native (Kotlin)

Proyek ini menyertakan aplikasi Android native (`android-webview/`) dengan koneksi langsung ke API Laravel.

### Struktur Folder
```
android-webview/
├── app/
│   ├── build.gradle           (OneSignal dependency)
│   └── src/main/
│       ├── java/com/waveproject/kelashub/
│       │   ├── MainApplication.kt  ← Inisialisasi OneSignal
│       │   ├── MainActivity.kt     ← Dashboard + token sync
│       │   ├── LoginActivity.kt    ← Login form
│       │   ├── DashboardActivity.kt← Launcher activity
│       │   ├── ApiClient.kt        ← Retrofit singleton
│       │   └── ApiInterface.kt     ← Endpoint definitions
│       ├── res/
│       └── AndroidManifest.xml    ← MainApplication registered
├── build.gradle
└── gradle/wrapper/gradle-wrapper.properties
```

### Cara Build APK
```bash
# Di Android Studio:
# Build > Build Bundle(s) / APK(s) > Build APK(s)

# Output:
# android-webview/app/build/outputs/apk/debug/app-debug.apk
```

### Alur Push Notification di Android
```
Buka Aplikasi
  → MainApplication.onCreate()
      → OneSignal.initWithContext(appId)
  → DashboardActivity / LoginActivity
  → Login Berhasil → MainActivity
      → syncOneSignalToken()
          → POST /kh/device-token {player_id: uuid}
              → students.onesignal_id = uuid  ✅
```

---

## 🎨 Filosofi Desain UI

### "Stealth Dark Operations" Theme
KelasHUB menggunakan estetika **Zinc-900 Stealth** — hitam pekat dengan aksen abu-abu dingin dan highlight biru elektrik. Terinspirasi dari UI terminal hacker dan dashboard sistem keamanan.

| Prinsip | Implementasi |
|:---|:---|
| **Mobile-First** | Bottom Navigation Bar, satu jempol operasional |
| **Data Clarity** | Label menonjol + efek glow untuk info kritis |
| **Micro-Animation** | Alpine.js transitions pada semua modal & tab |
| **Glassmorphism** | `backdrop-blur-md` + `bg-white/[0.02]` pada card |
| **Premium Feel** | Custom font tracking, letter-spacing, shadow-xl |

---

## 🤝 Kontribusi

Silakan baca [CONTRIBUTING.md](CONTRIBUTING.md) untuk panduan kontribusi.

```bash
# Fork repo → Clone → Buat branch fitur
git checkout -b feature/nama-fitur

# Commit dengan format konvensional
git commit -m "feat(notification): tambah trigger saat input kas"

# Push & buat Pull Request
git push origin feature/nama-fitur
```

---

## 📜 Lisensi

Proyek ini dilisensikan di bawah **MIT License** — bebas digunakan, dimodifikasi, dan didistribusikan.

---

<div align="center">

Dikembangkan dengan oleh **Wave Project.ID**  
Untuk memajukan efisiensi administrasi akademis mahasiswa Indonesia 🇮🇩

**v2.3.0** — Push Notification Release

</div>
