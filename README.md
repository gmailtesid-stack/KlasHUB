# 🌌 KelasHub: Stealth-Theme All-in-One Class Operations Suite

KelasHub adalah platform operasional kelas kelas-hybrid (*All-in-One Class Operations*) yang dirancang khusus untuk manajemen perkuliahan secara modern, responsif, dan terpusat. Dikembangkan di bawah payung **Wave Project.ID**, platform ini mengintegrasikan **Laravel backend (serverless di Vercel)** dengan database transaksional performa tinggi (**TiDB Cloud / MySQL / SQLite**) dan dibungkus menggunakan **Native Android WebView Wrapper** untuk menyajikan pengalaman aplikasi mobile yang premium, cepat, dan *seamless*.

Sistem ini didesain menggunakan **Stealth Zinc-900 Aesthetic Theme** dengan sentuhan *glassmorphism*, gradasi warna yang halus (*smooth gradients*), serta mikro-animasi dinamis yang memanjakan mata pengguna sekaligus menjamin keterbacaan data yang optimal pada layar HP.

---

## 📸 Tampilan Utama KelasHub

```
+-------------------------------------------------------------------+
|  [🌌 KELASHUB]                      (Akun) Ariyas Pratama  [->]   |
+-------------------------------------------------------------------+
|  Tracker Kehadiran | Sisa Nyawa Bolos | Repositori | Keuangan     |
|                                                                   |
|  +-------------------------------------------------------------+  |
|  |  MATA KULIAH: Pemrograman Web (3 SKS)                       |  |
|  |  Dosen: Ir. Budi Santoso                                    |  |
|  |  Status: [ONLINE]  <-- Toggleable by Ketua Kelas            |  |
|  +-------------------------------------------------------------+  |
|                                                                   |
|  MANAJEMEN ANGGOTA KELAS (Tabel Responsif 3-Kolom Mobile)         |  |
|  +-------------------------------------------------------------+  |
|  |  MAHASISWA             | JABATAN           | AKSI           |  |
|  +-------------------------------------------------------------+  |
|  |  Hana Rifdah Rianra    | [ MAHASISWA ]     |  [ Hapus ]     |  |
|  |  NIM: 231011401383     |                   |                |  |
|  |  --------------------+-------------------+----------------  |
|  |  Juan Montoya D.       | [ SEKRETARIS ]    |  [ Hapus ]     |  |
|  |  NIM: 231011402105     |                   |                |  |
|  +-------------------------------------------------------------+  |
+-------------------------------------------------------------------+
```

---

## 🛠️ Arsitektur Sistem & Teknologi Stack

KelasHub menggunakan kombinasi teknologi modern berkinerja tinggi untuk memastikan performa yang cepat dan bebas dari hambatan serverless (seperti Vercel Gateway Timeouts):

### **1. Backend & Logika Core**
*   **Framework:** Laravel 13.9.0 (PHP 8.5.2 / 8.3)
*   **Routing & Templating:** Blade Views + Alpine.js (untuk reaktivitas frontend tanpa beban SPA yang berat).
*   **Aplikasi Mobile:** Native Android Webview (Kotlin) dengan engine Chromium yang dioptimalkan.

### **2. Database & Data Storage**
*   **Sistem Database:** TiDB Cloud (MySQL-Compatible distributed database) untuk produksi, dan SQLite untuk pengembangan lokal.
*   **Ceklis & Caching:** Redis/Database Caching untuk menyimpan data sesi secara stateless guna mencegah redundansi query.

### **3. Desain & Antarmuka (Aesthetics)**
*   **Tema Utama:** Dark Stealth (Zinc-900 / HSL Tailored Gradients).
*   **Framework CSS:** Tailwind CSS v4 dengan optimisasi build menggunakan Vite.
*   **Responsive Engine:** Unified Mobile-First layout yang otomatis menyesuaikan elemen tabel, tombol bertumpuk (*stacked buttons*), dan kartu interaktif di layar HP.

---

## ✨ Fitur-Fitur Utama KelasHub

### **1. Smart Attendance & "3-Alfa" Penalty Engine**
*   **Sistem Sisa Nyawa:** Setiap mahasiswa dibekali dengan **3 Nyawa** per mata kuliah di awal semester.
*   **Auto-Penalty:** Setiap kali sekretaris atau ketua kelas menandai mahasiswa sebagai **"Alfa"**, sistem otomatis mengurangi 1 nyawa mahasiswa tersebut pada mata kuliah terkait.
*   **Sanksi Dicekal:** Jika nyawa mahasiswa menyentuh angka **0**, sistem otomatis mengubah status mahasiswa menjadi **"DICEKAL"**. Di dashboard mahasiswa akan muncul peringatan keras berkedip merah: *"Anda Terindikasi Nilai E / Tidak Bisa Mengikuti UTS/UAS"*.

### **2. Role-Based Access Control (RBAC) Interaktif**
Sistem KelasHub membagi hak akses secara ketat berdasarkan status jabatan di kelas:
*   **Ketua Kelas:** Memiliki kendali penuh untuk menambah/menghapus mahasiswa, mengatur jenis pembelajaran (Online/Offline) per mata kuliah secara *real-time*, dan memvalidasi persetujuan dispensasi.
*   **Sekretaris:** Memiliki otorisasi penuh untuk mengelola lembar presensi harian kelas dan menginput data absensi.
*   **Bendahara:** Memiliki hak akses khusus untuk mengelola buku kas kelas, mencatat uang kas masuk/keluar, dan memantau status iuran wajib anggota.
*   **Mahasiswa:** Hak akses terbatas (baca-saja) untuk memantau sisa nyawa pribadi, mengunduh modul pembelajaran, dan mengirimkan rekap mandiri jika berhalangan hadir (Sakit/Izin).

### **3. Academic Hub & File Repository**
*   **Database Integrated Storage:** Modul pembelajaran dan tugas kuliah disimpan langsung di dalam database dalam bentuk terkompresi.
*   **Upload & Download:** Ketua Kelas/Sekretaris dapat mengunggah file materi secara instan, dan mahasiswa dapat mengunduhnya langsung melalui aplikasi WebView.

### **4. Financial Ledger Tracking**
*   Bendahara kelas dapat memasukkan nominal iuran mingguan.
*   Tersedia diagram sirkulasi keuangan kas yang transparan untuk menghindari kecurangan atau salah hitung keuangan kelas.

---

## 🗄️ Struktur Database & Migrasi Core

KelasHub memiliki skema relasional database yang bersih dan optimal:

### **1. Tabel Master Subjek (`master_subjects`)**
Menyimpan daftar mata kuliah resmi di jurusan.
```sql
CREATE TABLE master_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    sks INT NOT NULL,
    created_at TIMESTAMP NULL
);
```

### **2. Tabel Jadwal Kelas (`schedules`)**
Menyimpan hari perkuliahan beserta nama dosen, kode kelas, dan metode penyampaian kelas (*Online/Offline*).
```sql
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    dosen VARCHAR(255) NOT NULL,
    day VARCHAR(50) NOT NULL,
    time VARCHAR(50) NOT NULL,
    code VARCHAR(50) DEFAULT 'REG-A',
    delivery_type VARCHAR(50) DEFAULT 'offline', -- 'online' / 'offline'
    created_at TIMESTAMP NULL
);
```

### **3. Tabel Kehadiran Mahasiswa (`class_attendances`)**
Mengelola status absensi mahasiswa per mata kuliah untuk melacak sisa nyawa bolos.
```sql
CREATE TABLE class_attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'Hadir', -- 'Hadir', 'Sakit', 'Izin', 'Alfa'
    notes VARCHAR(255) NULL,            -- Keterangan surat sakit atau izin
    is_validated BOOLEAN DEFAULT TRUE,  -- Menunggu validasi Ketua Kelas jika diajukan mandiri
    created_at TIMESTAMP NULL
);
```

### **4. Tabel Modul Pembelajaran (`learning_modules`)**
Menyimpan materi kuliah dan tugas dalam bentuk data biner langsung di cloud database.
```sql
CREATE TABLE learning_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    file_content LONGBLOB NOT NULL,     -- File modul disimpan langsung di TiDB
    file_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);
```

---

## 📱 Dokumentasi Aplikasi Android WebView Wrapper

Aplikasi Android KelasHub dikembangkan menggunakan Kotlin native sebagai wrapper dari URL Vercel. Menggunakan engine Google Chromium terbaru dengan konfigurasi performa maksimal agar tidak terasa seperti mengakses website biasa.

### **1. Konfigurasi Manifest (`AndroidManifest.xml`)**
Mengaktifkan izin koneksi internet dan lalu lintas data yang aman:
```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    package="com.waveproject.kelashub">

    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />

    <application
        android:allowBackup="true"
        android:icon="@mipmap/ic_launcher"
        android:label="@string/app_name"
        android:roundIcon="@mipmap/ic_launcher_round"
        android:supportsRtl="true"
        android:theme="@style/Theme.KelasHUB">
        
        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:theme="@style/Theme.KelasHUB.NoActionBar">
            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>
        </activity>
    </application>
</manifest>
```

### **2. Logika WebView Premium (`MainActivity.kt`)**
Memaksimalkan performa rendering, mengaktifkan penyimpanan lokal (*DOM Storage*), akselerasi perangkat keras (*Hardware Acceleration*), dan penanganan tombol kembali (*Back Button Navigation*):
```kotlin
package com.waveproject.kelashub

import android.os.Bundle
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {
    private lateinit var webView: WebView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        webView = findViewById(R.id.webView)
        
        // Konfigurasi WebSettings Premium
        val settings: WebSettings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.useWideViewPort = true
        settings.loadWithOverviewMode = true
        settings.databaseEnabled = true
        settings.allowFileAccess = true
        settings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW

        // Pasang Client agar link tidak membuka browser luar
        webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                if (url != null) {
                    view?.loadUrl(url)
                }
                return true
            }
        }

        // Muat URL Produksi Vercel
        webView.loadUrl("https://kelashub.vercel.app")
    }

    // Navigasi Tombol Back agar tidak langsung menutup aplikasi
    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }
}
```

---

## ⚡ Optimalisasi Kinerja Serverless (Vercel)

Karena KelasHub berjalan di atas platform Vercel Serverless Function, terdapat batasan ketat mengenai waktu eksekusi runtime (maksimal 10-15 detik per request). Beberapa teknik optimalisasi tingkat tinggi yang kami terapkan antara lain:

1. **Database Caching:** Data mata kuliah dan jadwal kelas di-cache sementara di memori aplikasi untuk meminimalkan beban koneksi database transaksional.
2. **Stateless PHP Handler:** `api/index.php` dikonfigurasi sebagai gerbang tunggal (*Single Entry Point*) yang langsung mengarahkan request ke kernel Laravel secara instan tanpa proses inisialisasi ganda.
3. **Stateless Sessions:** Mengalihkan sesi pelacakan pengguna dari penyimpanan file server lokal (yang tidak didukung oleh arsitektur stateless Vercel) ke cookie-based session terenkripsi tinggi.
4. **Vercel Build Target:** Mengabaikan folder vendor, cache lokal, dan modul Android (`.vercelignore`) saat melakukan deployment untuk memastikan waktu upload di bawah 5 detik.

---

## 🚀 Panduan Instalasi Lokal & Build

### **A. Menjalankan Website (Laravel Backend)**
1. Clone repositori ini ke komputer lokal Anda.
2. Salin berkas lingkungan:
   ```bash
   cp .env.example .env
   ```
3. Sesuaikan konfigurasi database Anda di dalam berkas `.env` (gunakan SQLite untuk pengembangan instan):
   ```env
   DB_CONNECTION=sqlite
   ```
4. Pasang dependensi PHP dan lakukan migrasi database:
   ```bash
   composer install
   php artisan migrate --seed
   ```
5. Jalankan server pengembangan lokal:
   ```bash
   php artisan serve
   ```
6. Buka `http://localhost:8000` di peramban Anda.

### **B. Melakukan Build Aplikasi Android APK**
1. Buka folder `android-webview` menggunakan **Android Studio**.
2. Pastikan file `local.properties` sudah mendeteksi lokasi SDK Android Anda.
3. Lakukan sinkronisasi Gradle (Gradle Sync).
4. Klik menu **Build > Build Bundle(s) / APK(s) > Build APK(s)**.
5. Berkas APK siap diinstal pada ponsel pintar Anda di folder:
   `android-webview/app/build/outputs/apk/debug/app-debug.apk`

---

## 💎 Filosofi Desain Antarmuka

KelasHub mengedepankan filosofi desain yang **intuitif, cepat, dan fungsional**:
* **Konsistensi UI:** Semua tombol aksi, input form, dan tabel menggunakan palet warna gelap terpadu dari sistem **Zinc-900** milik Tailwind CSS.
* **Aksesibilitas Satu Tangan:** Penempatan tab navigasi utama berada di bagian paling bawah layar (*Bottom Navigation Bar*), mempermudah pengoperasian aplikasi hanya dengan menggunakan satu jempol tangan.
* **Kejelasan Informasi:** Label krusial seperti status Online/Offline perkuliahan dan sisa nyawa bolos didesain menonjol dengan efek *glow* agar langsung menarik perhatian pengguna saat pertama kali membuka aplikasi.

---
*Dikembangkan dengan penuh dedikasi oleh **Wave Project.ID** untuk memajukan efisiensi administrasi akademis mahasiswa Indonesia.* 🇮🇩🚀
