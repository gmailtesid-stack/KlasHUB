# 🌌 KelasHUB — Ekosistem Operasional Kelas Modern (Enterprise-Grade)

<div align="center">

[![Deployed on Vercel Edge](https://img.shields.io/badge/Production-Vercel-black?logo=vercel)](https://klas-hub.vercel.app)
[![Laravel Serverless](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel)](https://laravel.com)
[![TiDB Cloud SQL](https://img.shields.io/badge/Database-TiDB%20Cloud-orange?logo=mysql)](https://tidbcloud.com)
[![OneSignal Push Notifications](https://img.shields.io/badge/Notifications-OneSignal-E54B4D?logo=onesignal)](https://onesignal.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Kotlin Native App](https://img.shields.io/badge/Mobile-Kotlin_Native-7F52FF?logo=kotlin)](android-webview/)
[![UI Stealth Theme](https://img.shields.io/badge/UI-Zinc_900_Stealth-0f172a?logo=tailwindcss)](Resources/css/app.css)

**Sistem Administrasi Sentral Kelas Perkuliahan (SaaS) Berperforma Ekstrim, Dirancang untuk Agility, Transparansi Dana, dan Konektivitas Mahasiswa Real-Time.**  
Berjalan sempurna tanpa henti di atas Vercel Serverless (via `vercel-php`), didayagunakan TiDB Cloud MySQL, diselimuti estetika Stealth Hitam Pekat (Tailwind CSS v4 + Alpine.js), dan dipersenjatai peringatan harian via Push Notification (OneSignal SDK).

[🚀 Buka Versi Live Demo](https://klas-hub.vercel.app) · [📋 Baca Log Perubahan](CHANGELOG.md) · [🤝 Ingin Berkontribusi?](CONTRIBUTING.md) · [📖 Dokumentasi Terbuka API](docs/API.md)

</div>

---

## 📖 Navigasi Indeks (Daftar Isi)

- [Tentang Proyek Ekosistem](#-tentang-proyek-ekosistem)
- [⏳ Evolusi Sejarah KelasHUB (Dari Web ke Super-App)](#-evolusi-sejarah-kelashub-dari-web-ke-super-app)
- [Modul Fitur Papan Atas](#-modul-fitur-papan-atas)
- [Detail Arsitektur & Teknologi Server](#-detail-arsitektur--teknologi-server)
- [Topologi & Skema Basis Data Relasional](#-topologi--skema-basis-data-relasional)
- [Cara Instalasi Mesin Lingkungan Lokal (Dev)](#-cara-instalasi-mesin-lingkungan-lokal-dev)
- [Deployment Lanjutan Vercel Serverless](#-deployment-lanjutan-vercel-serverless)
- [Integrasi Web Dashboard & Mobile Android](#-integrasi-web-dashboard--mobile-android)
- [Hierarki Kontrol Akses Super (RBAC)](#-hierarki-kontrol-akses-super-rbac)
- [Rambu Keamanan & Celah Sistem](#-rambu-keamanan--celah-sistem)

---

## 🎯 Tentang Proyek Ekosistem

**KelasHUB** bermula dari sebuah purwarupa (prototype) website manajemen presensi sederhana, dan kini telah berevolusi menjadi sebuah *Super-App* ekosistem perkuliahan (MaaS / Management as a Service). Proyek ini mengeliminasi ratusan lembar kertas presensi fisik, riwayat chat tugas yang tertimbun di WhatsApp, hingga hilangnya transparansi pencatatan dana kelas.

Dikemas ke dalam tajuk "**Stealth Tactical Operations**", antarmuka Web Dashboard dibalut latar gelap kontras tinggi (Zinc-900) dengan Reaktivitas *Alpine.js* agar memancarkan aura futuristik profesional. Perjalanan panjang dari *Web Monolithic* menuju *API-Driven Mobile Native* membuktikan skalabilitas arsitektur sistem ini.

**Arsitektur Kreator**: Wave Project.ID  
**Edisi Saat Ini**: Rilis Web & Mobile Terpadu **v2.3.0**

---

## ⏳ Evolusi Sejarah KelasHUB (Dari Web ke Super-App)

KelasHUB dibangun bukan dalam semalam. Berikut adalah rekam jejak historis bagaimana sistem ini membengkak secara fungsional tanpa mengorbankan performa:

### 🌟 Era 1: Sang Pionir Web Sederhana (MVP Web-Only)
Pada awal penciptaannya (v1.0.0), KelasHUB hanyalah sebuah sistem website administrasi murni berbasis `Laravel Blade MVC`. 
- Ketergantungan penuh pada *Form Submission* HTML tradisional tanpa AJAX.
- Fitur terpusat hanya pada 2 elemen: **Buku Kas** dan **Absensi "Sisa Nyawa"**.
- Belum ada konsep multi-kelas. Satu database `students` berdiri mati membina satu kelas tunggal.
- Hosting menggunakan panel cPanel standar, yang sering tumbang saat seluruh kelas login bersamaan (I/O Bottleneck).

### 🚀 Era 2: Kenaikan Kasta Arsitektur & Ekspansi Fitur (The Multi-Tenant Web)
Kebutuhan mahasiswa melonjak (v1.5.0 - v2.0.0). Kami menulis ulang fondasinya:
- Website ini tidak lagi memuat 1 kelas, melainkan mengaktifkan sistem perancah SaaS (Software as a Service) sejati. Lahirnya tabel `academic_classes` menciptakan *Tenant Isolation*, memungkinkan ratusan kelas kampus didaftarkan independen dalam satu aplikasi.
- Penambahan fungsi Gudang File (Academic Repositories). Mengatasi masalah storage murah, website ini direkayasa menelan file PDF dan menyimpannya menjadi Teks Panjang Terenkripsi (*Base64 String*) di Database TiDB Cloud.
- UI *Stealth Zinc-900* dikodifikasi menggunakan **Tailwind CSS v4** plus sayatan reaktivitas **Alpine.js**, mengubah pengalaman web lawas menjadi lincah serasa *Single Page Application (SPA)*.

### 📱 Era 3: Revolusi Mobile & Serverless Vercel (The Super-App Era)
Era puncak (v2.1.0 - v2.3.0). Kecepatan adalah segalanya:
- Website diangkat paksa dari server VPS konvensional menuju lingkungan **Vercel Serverless (Edge Runtime)**. Kode menyesuaikan diri untuk hidup tanpa menyimpan _Session File_.
- Website Monolitik bermutasi menjadi *Hybrid API Gateway*. Selain memuntahkan grafis HTML untuk pengguna Laptop/PC, KelasHUB membuka gerbang JSON murni untuk klien Mobile.
- Diciptakannya **Aplikasi Mobile Native Android (Kotlin)** (`android-webview/`). Bukan lagi sekadar web yang dibungkus, melainkan menggunakan `Retrofit` mutakhir untuk menarik data server, dan mengaitkan ID perangkat (*Player_ID*) ke OneSignal API penyiaran notifikasi seketika (Zero-Delay Push Broadcast). Mahasiswa kini dapat pop-up peringatan mematikan (Tugas Baru, Alfa) menembus ponsel mereka bahkan saat layar terkunci.

---

## ✨ Modul Fitur Papan Atas

### 1. 🌐 Web Dashboard "Stealth-Ops" (Alpine.js + Tailwind)
Pengalaman *Desktop/Web* paling memukau bagi pengurus kelas (Ketua/Sekretaris/Bendahara). Manajemen entri data besar-besaran, analisis keuangan detail, dan penjemputan berkas laporan (Export PDF/Excel) dapat dilakukan bebas-loading (_Client-side Transitions_) dengan estika gelap pekat memanjakan mata yang tidak akan ditemui pada sistem Siakad kuno kampus.

### 2. 🚷 Gamefication Kehadiran & Sanksi "Sisa Nyawa"
Mengadopsi psikologi disiplin otomatis melalui mode permainan *Life-Tokens*:
- Default setiap peluru mata kuliah diberikan **3 Sisa Nyawa**.
- Ketidakhadiran tanpa keterangan tervalidasi akan memotong **1 Nyawa**.
- Saat nyawa membentur angka nol = Panel dashboard meledak merah menyala, status mahasiswa terkunci ke indikator `DICEKAL (Risiko Drop-Out)`.

### 3. 💼 Laporan Saldo Moneter Real-Time Kas Kelas
- Transaksi terhubung sinkron antara penginputan web dasbor dan pembacaan di HP mahasiswa. Kalkulasi saldo akhir dikeroyok mesin lewat rumus `SUM(income) - SUM(expense)`.
- *Streaming Export Engine*: Mampu melontarkan log dokumen CSV berkapasitas ratusan baris langsung ke *browser output stream* (`php://output`), tanpa membebani limit memori Vercel. 

### 4. 🗄️ Repositori Storage Bebas Hambatan (Base64 Injection)
Menghancurkan limitasi *Filesystem Stateless!* Seluruh PDF silabus dan Makalah Tugas Mahasiswa tidak di-upload ke *Storage Local* (yang mana akan terhapus saat Vercel server _sleep_). Dokumen dikompres menjadi bentuk string karakter (Base64 LONGTEXT). Keabadian data terjamin di basis data SQL Cloud. 

### 5. 🔔 Radar Real-Time Push (Native OneSignal Integration)
Sistem Notifikasi Hibrida Handoff:
- Notifikasi bertumpuk di Lonceng Web Aplikasi bagi pengguna laptop.
- Menyetrumkan sinyal API ke Server OneSignal, yang berlanjut meledakkan Pop-Up Latar Belakang (Background Notification) langsung pada OS Android Mahasiswa ketika sistem menyuntikkan informasi Ujian atau Tagihan Kas yang tertunda.

---

## 🛠️ Detail Arsitektur & Teknologi Server (Hybrid Monolith)

Karena sejatinya KelasHUB adalah *Website Monolith* canggih yang berevolusi menjadi API provider, arsitekturnya berwujud Hibrida:

```text
       [ LAPISAN PENGGUNA UJUNG ]
              │                            │
   📱 NATIVE KOTLIN APP            🌐 MODERN WEB BROWSER
   (JSON Consumer & Push Target)   (Blade HTML & Alpine.js Renderer)
              │                            │
              ▼                            ▼
  ┌─────────────────────────────────────────────────────────┐
  │ ⚡ VERCEL EDGE RUNTIME (Stateless & Serverless)          │
  │    (vercel-php bridge handler)                          │
  │    → Router Web (Kembalian DOM) / API (Kembalian JSON)  │
  │    → Middleware Filter & Session Cookie Encryption      │
  └───────────────────────────┬─────────────────────────────┘
                              │
  ┌───────────────────────────▼─────────────────────────────┐
  │ ☁️ TiDB SQL CLOUD (Multi-Tenant Persistence)             │
  │    Menjalankan Global scope isolasi Data Antar-Kelas    │
  └─────────────────────────────────────────────────────────┘
```

**Esensi Stack Teknologi Inti:**
| Pilar | Versi | Sejarah Operasional |
|:---|:---:|:---|
| **Laravel Framework** | `13.x` | Pondasi MVC Web yang kini bertransformasi menjadi Hub API. |
| **PHP Runtime** | `8.3+` | Eksekutor kencang dengan memori terbatas Vercel. |
| **TiDB Cloud** | `MySQL 8` | Basis data nir-server menelan entri string dokumen base64. |
| **TailwindCSS** | `v4` | Pelukis antarmuka *Stealth Dark Mode* pada web dashboard. |
| **Alpine.js** | `3.x` | Memanipulasi DOM Web HTML seakan hidup tanpa memuat ulang (SPA-feel). |
| **Kotlin Native** | `v1.9` | Aplikasi Performa Cepat Sistem Operasi Android (Pendamping Web). |

---

## 🔐 Rambu Keamanan & Celah Sistem

Mendapatkan keamanan maksimal sangat ditekankan:
1. **Anti Identifikasi Palsu (IDOR)**:
   Aplikasi kampus rentan manipulasi parameter ID `/user/1` ke `/user/2`. KelasHUB menggunakan benteng dinding partisi `Auth::user()->class_id` menggunakan *Eloquent Global Scope* yang dengan telak menolak query lintas kelas instansi.
2. **Perisai Ganda XSS (Cross-Site Scripting)**:
   Seluruh masukan di Dashboard Web (pengumuman, nama tugas) wajib digempur lolos tag filter `htmlspecialchars` bawaan Blade Template Laravel dan dipantau kaku melalui *SecurityHeaders Middleware*, mengusir ClickJacking pada panel *Super Admin*.

---

<div align="center">
    <h3>Direkayasa Oleh Seniman Kode Taktis dari:</h3>
    <h2>Wave Project.ID (Ariyas Pratama Ramadhan)</h2>
    <p>Semoga teknologi open-source kita memfasilitasi masa depan gemilang anak bangsa 🇮🇩</p>
    <code>"Evolusi Operasional Kelas - Bermula Dari Lembar Kertas Menuju Penyiaran Lintas Ekosistem"</code>
</div>
