# 📱 Panduan Ekosistem Khusus: KelasHUB Mobile Native Kotlin

Dokumentasi terpisah ini didedikasikan sepenuhnya untuk mengupas tuntas arsitektur Aplikasi Seluler (APK Android) KelasHUB. Berfokus pada siklus hidup pengembangan (*Development Lifecycle*), mesin kompilasi, serta ikatan infrastruktur hibrida yang menyangga aplikasi *Native Kotlin* ini.

---

## 1. 🌐 Ikhtisar Infrastruktur Pendukung Aplikasi (The Web Backbone)
Aplikasi Android ini tidak berjalan di ruang hampa. Kecepatan dan keamanannya disokong oleh ekosistem awan raksasa di balik layar:

* **Github**: Platform kontrol versi (Version Control) utama yang mengamankan kolaborasi source code antara arsitektur web dan mobile.
* **Vercel (Edge Functions)**: Engine penyedia API (*Application Programming Interface*). Vercel menyuplai lalu lintas data JSON dengan sifat adaptif (Serverless) merespon *HTTP Requests* Retrofit dari HP Mahasiswa.
* **TiDB Cloud (Distribusi MySQL)**: Jantung persisten tanpa batas, menyimpan data base64 PDF, log buku kas, dan *Device Push Token* (UUID). Semua lalu lintas ditahan oleh perisai enkripsi SSL `cacert.pem`.

---

## 2. 🛠️ Persenjataan Developer (Lingkungan Pengembangan Editor)
Kode ini dibangun menggunakan dua senjata utama yang berjalan berdampingan (AI & Tradisional IDE):

### A. Antigravity AI Code Editor
* **Peran**: Asisten rekayasa algoritma dan perombakan arsitektur skala besar (Massive Refactoring). Digunakan untuk menyelesaikan integrasi logika OneSignal, mengatasi bug Sinkronisasi Gradle, serta mengotomasi perancangan jembatan API JSON Retrofit di Android. 
* **Keunggulan**: Memperbaiki ribuan lint error dan kebuntuan kompilasi Android dalam waktu singkat tanpa merusak struktur MVVM. Kode agen AI diatur pada panduan khusus (`mobile/AGENTS.md`).

### B. Android Studio (Flamingo / Giraffe / Terbaru)
* **Peran**: *Native Compiler* & *UI Profiler*. Lingkungan resmi Google untuk manufaktur paket instalator akhir.
* **Fungsi Kritis**: Menerjemahkan visual *Jetpack XML Layouts* (Material Design), mengeksekusi kompilator Kotlin v1.9, dan mem-buang bundel `.APK` / `.AAB` untuk distribusi massal.

---

## 3. 🎯 Arsitektur Utama Kotlin Android
Di dalam folder `/android-webview`, kode beroperasi mengacu pada prinsip arsitektur modern Android:
* **Networking (Retrofit2 + OkHttp)**: Bertanggung jawab mengirim tarikan sinkron asinkron JSON. Mengadopsi teknologi *Cookie Jar* untuk mencegah sesi otentikasi login Android mahasiswa terputus dari Cookie Stateless Vercel PHP.
* **Asynchrony (Kotlin Coroutines)**: Jauh lebih ringan dibandingkan Thread Java kuno, Coroutine bertugas menyinkronkan Token Android HP ke Backend dalam rutinitas tanpa membekukan layar (UI Freeze/ANR lags).
* **View Systems (XML Material)**: Kami membuang konsep WebView lawas yang membungkus web HTML! Seluruh tampilan (contoh: RecycleView Daftar Hadir) dicetak secara hakiki menggunakan elemen Native Android untuk *Smooth Scrolling* pada 60-120 fps.

---

## 4. 🔔 Mesin Latar Belakang (SDK Push Notification)
Dua pilar komunikasi nirkabel digabungkan untuk memastikan Mahasiswa dipaksa menerima peringatan Alfa / Ujian:

### Google Firebase Cloud Messaging (FCM Base)
Merupakan fondasi urat saraf OS Android yang memberikan jalan masuk bagi koneksi *Push Server-to-Client* tanpa menghabisi baterai gawai (Optimasi fitur *Doze Mode* CPU).

### OneSignal SDK (v5.x Push Platform)
SDK OneSignal berada di garda terdepan mengelola manajemen Firebase FCM (Menyuntikkan identifikasi User/Device yang elegan).
1. **Boot Initialization**: `MainApplication.kt` membangunkan SDK OneSignal seketika ikon aplikasi dihantam *(Cold Start)*.  
2. **Sinkronisasi Otomatis**: `MainActivity.kt` mencuri Kode ID Khusus HP (Player ID). Mengunggah token secara transparan lewat Endpoint khusus Backend Vercel `POST /kh/device-token`.
3. **Penerimaan Getar Realtime**: Pada detik yang sama Sang Ketua merilis Tugas Makalah di Web Vercel, OneSignal melepaskan sinyal ke Firebase, membuat ponsel menjerit < 1 detikan (Tembusan Notifikasi Lock-screen Otonom).

---

## 5. 🚀 Panduan Manual Produksi (Build Compilation Flow)

Untuk menggandakan *Source Code* ini menjadi file instalator (`APK`) resmi, jalankan prosedur kompilasi lokal berikut di Terminal (Gunakan peranti Android Studio):

```bash
# 1. Bersihkan sisa limbah kompilasi pabrik
./gradlew clean

# 2. Sinkronisasikan ketergantungan modul eksternal (Retrofit, OneSignal)
./gradlew dependencies

# 3. Kunci dan Kompilasi menjadi Rilis Android APK Siap-Pakai yang Optimal (Shrinked & Minified)
./gradlew assembleRelease
```
*Lokasi Panen (Output)*: File berformat `.apk` akan bersarang empuk *(Landing Zone)* di tapak direktori `app/build/outputs/apk/release/app-release.apk`. Bagikan file ini untuk di-install massal pada orientasi Android mahasiswa!

---
*Didedikasikan kepada penggiat Sistem Administrator Mobile KelasHUB. Hentikan meniru tampilan Web ke HP kosong (WebView); Rangkullah keagungan Native Performance.*
*— WaveProject.ID (v2.3 Kotlin Era).*
