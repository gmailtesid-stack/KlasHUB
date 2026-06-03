# Petunjuk Teknis & Agen (AGENTS.md) — KelasHUB Mobile (Native)

## Konteks Proyek & Parameter Fundamental
Repositori ini merujuk secara mendalam terhadap kode sumber sub-direktori `android-webview/` yang memuat integrasi migrasi final dari paradigma WebView lama kepada antarmuka Native murni Android berbahasa pemrograman Kotlin.

- **URL Soket Basis Backend**: `https://klas-hub.vercel.app/`
- **Model Manajemen Sesi**: Stateful pada klien via persistensi Cookie Jar terenkapsulasi di dalam OkHttp Network Tunnel. (Mengingat Backend API di Vercel menggunakan mekanisme perlindungan stateless via Cookie).
- **Protokol Saluran Komunikasi (HTTP Client)**: `Retrofit2` ditandemkan dengan parsing statis `Gson` JSON Deserializer, serta lapisan perantara Transport (Transport Layer) `OkHttp3`.
- **Target OS Mesin (Android Build)**: Minimal Android 7.0 (API 24). Direkomendasikan membumi untuk kompilasi hingga perangkaian keamanan termutakhir Android 14 (Tiramisu API 34).
- **Notifikasi Push**: `OneSignal Android SDK v5.x+`.
  - App Identifier ID Produksi: `04a9cff3-874a-4e84-96c0-f79cfa86d255`.

---

## Enklopedia Modul Kunci Kotlin

| Penamaan File Kernel | Definisi Arsitektur dan Tugas Operasional |
|:---|:---|
| `MainApplication.kt` | Akar fondasi. *Application Class Context* yang dipanggil pertama bahkan saat antarmuka belum digambar (Siklus boot). Menyuntik awal mesin sinkronisasi OneSignal. |
| `MainActivity.kt` | Tulang punggung layar. Dashboard sentral dengan kapabilitas latar (Thread) memanggil `syncOneSignalToken()`. |
| `LoginActivity.kt` | Gerbang awal pengait autentikasi (Input nama dan Sandi rahasia). |
| `DashboardActivity.kt` | Modul perantara *Launcher Activity*, mengatur loncatan inisialisasi transisional grafis pembukaan (Splash state). |
| `ApiClient.kt` | Jantung pembuluh data Internet. Obyek *Singleton* yang memaksa Retrofit merawat memori sesi identitas (Cookie Persistent Network Interceptor) atas tiap transmisi keluar dan masuk. |
| `ApiInterface.kt` | Memetakan semua Endpoint rute URL Vercel (seperti `POST /kh/device-token`) sejajar dengan anotasi Retrofit Kotlin (*@POST*, *@FormUrlEncoded*). |
| `app/build.gradle` | Dokumen konfigurasi mesin manufaktur (Manifaturer Config). Menautkan plugin perakitan OneSignal secara dinamis pada compiler versi SDK. |

---

## Protokol Wajib Rekayasa (Critical Coding Conventions)

1. **Jalur Eksekusi Internet Wajib Tunggal** 
   Setiap interaksi permintaan keluar dilarang menciptakan obyek koneksi Http yang liar/baru. Panggil asinkron secara eksklusif menggunakan jembatan statis terpadu `ApiClient.apiInterface.namametode()`.
2. **Ritual Sinkronisasi Identifier Mesin Notifikasi**
   Modul `MainActivity.onCreate()` memikul beban perulangan reinkarnasi panggilan balik (Callback) fungsi `syncOneSignalToken()` sesaat ketika fragmen DOM Dashboard Android selesai ditata. Hal ini memastikan token Player selalu identik, meski perangkat dicabut paksa (Clear Cache).
3. **Payload Parametrik Endpoint Token**
   Rute API spesifik identitas perangkat berada lurus di `POST /kh/device-token`. Wujud skema payload form URL menuntut kunci key bernomenklatur mutlak `player_id`. Payload tersebut wajib bervariabel UUID OneSignal Push Subscription.
4. **Penangkapan Erupsi Memori Otorisasi Teralienasi (401 Trapping)**
   Aktivitas klien wajib dipecahkan *(Hard Destroy)* ke `LoginActivity` jika terlempar kode `HTTP 401 Unauthorized`.

---

## Logika Penyiaran Intersepsi OneSignal (Under the Hood)
Sistem Notifikasi ini bertipe *Out-Of-Band* — Memecah kelembaman pergerakan API dengan mengalihkan pengikatan di level mesin (Kernel Level) OS Sistem Android.
- Jangan memulai instance OneSignal sembarangan melainkan via _Class Inherited Application Context_ guna pencegatan pesan sebelum User Interface dibangun (memunculkan Pop-up di Lockscreen/Background System).
- Sifat balasan Nilai _Token (Push Subscription ID)_ berupa **Tipe Nullable (`String?`)**; Pemutaran paksa tanpa *Null Check* memicu *NullPointerException Crash Fatal*.
- Jika mahasiswa merombak instalasi aplikasi dari bersih *(Fresh Install)* atau pindah smartphone, Token Identifier otomatis ter-generate sekuriti enkripsi *Unique UUID* baru. Ini merangsang trigger update kepada basis data TiDB KelasHUB di eksekusi _syncOneSignalToken_.
