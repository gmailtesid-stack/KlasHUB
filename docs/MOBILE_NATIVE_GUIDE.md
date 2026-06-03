# Kitab Fabrikasi Klien Eksternal Android Kotlin (*The Native Mobile Integration Guide*)

Target Infrastruktur: Android SDK API Level 26+ (Minimum Android 8.0 Oreo).
Perangkat Perkakas Mutlak: *Android Studio Koala / LadyBug 2024.1+* dengan JDK 17 Compiler.

Di dalam ekosistem Hybrid Injeksi *KelasHUB*, modul sisi seluler `android-webview/` **TIDAK LAGI MENGADOPSI WEBVIEW.HTML** Pasif layaknya rilis jadul! Ini adalah proyek 100% *Murni Android Kotlin Native Architecture*. Panduan ini membahas alur injeksi kode yang diperbaiki. 

---

## 1. Tulang Punggung Model-View-ViewModel (Jetpack MVVM)
Setiap Layar Aplikasi (Aktivitas Login, NavDash) menjauhi panggilan data kotor *(Fat Activity Constraint)*.

1. **Retrofit API Interfaces (Client Model)**:
   Akar tarikan ditaruh di `ApiClient.kt`. Karena koneksi server kita di Vercel menggunakan *State Cookie (Sessions PHP)* (Bukan Bearer Auth Statis!), Klien Android **HARUS** dipasangi penangkap Cookie. Lihat fungsi ekstensi Singleton CookieJar Kustom di File `ApiClient.kt` milik WaveProject:
   ```kotlin
   // Menyeret cookie login ke SharedPreferences agar
   // seluruh Endpoint GET dashboard tervalidasi. (Wajib Ada)
   ```

2. **Fragments vs Activites (Render Material UI)**
   Kami mengarahkan pemakaian antarmuka RecyclerView `Fragment` pada bilah Bawah Navigasi (*Bottom Navigation Tabs*). Seluruh pewarnaan (XML Styles/Colors) dipaksa mematuhi skema Hitam Siluman Zinc (`#09090b` hingga `#18181b`) demi homogenitas rupa Front-end Tailwind Vercel Web.

---

## 2. Inisiasi Peledak Radar Gawai (OneSignal Push Sync)
Modul ini merupakan *Killer Feature* dari arsitektur *Decoupled Push* milik KelasHUB.

**A. Injeksi ID Manifest App**  
Dalam berkas `AndroidManifest.xml` hingga ke tingkat Gradle Build App Module (`build.gradle.kts` tingkat `android-webview/app/`), dependensi Google Cloud Service OneSignal dimasukkan ke blok Dependencies:
> `implementation("com.onesignal:OneSignal:[LATEST_VERSION]")`

**B. Pengkhianatan Hening (The Silent Upload)**  
Saat `MainActivity.kt` bangkit paska login (Cek Skema Coroutines SDK OneSignal), Kode Kotlin secara otomatis mengorek parameter Alamat Gawai Kriptografis Unik (UUID).  
UUID Tanda Pengenal OneSignal ini ditembak dan disimpan tanpa suara via rute Retrofit Android ini: `POST https://klas-hub.vercel.app/kh/device-token`. Sinkronisasi rampung! Ponsel Mahasiswa ini pun telah tertaut ke awan KelasHUB tanpa opsi mencabutnya!

---

## 3. Kompilasi Persenjataan Mesin Awan (Local Release Blueprint)

Kompilasi (Generate APK) Android dilarang dieksekusi secara awam (Debugging-mode default).

1. Tentukan Ujung Tombak Server: Buka `/app/src/main/java/com/waveproject/kelashub/ApiClient.kt`. Ubah Constanta `BASE_URL` ke Produksi Vercel Asli (Bila rilis Publik), Hindari alamat `10.0.2.2:8000` emulator lokal!
2. Buka terminal Root CMD mengambang, Tembak injersi skrip pembersihan dan Kompilator Rilis Final:
   ```powershell
   ./gradlew clean
   ./gradlew assembleRelease
   ```
3. Menunggu (Kalkulator Keadilan). Karena dependensi besar Jetpack Material3 UI dan OkHttp Retrofit. Komputasinya butuh CPU konstan RAM PC Development. Skrip mungkin memakan durasi absolut **~3 Menit** sampai Artefak `app-release.apk` siap dilempar ke Publik / Play-Store / Group Aplikasi Mahasiswa!
