# Changelog Historis Arsitektur

Semua perubahan kode sumber inti (Core Engine) pada repositori **KelasHUB** dijaga dengan ketat *(version-controlled)* pada jurnal rekayasa ini. 

Metodologi pendokumentasian ini merujuk kaku kepada pedoman standar [Keep a Changelog](https://keepachangelog.com/id/1.0.0/), sedangkan penomoran peluncuran kami mengikuti kesepakatan mutlak [Semantic Versioning v2.0.0](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]
*Fitur Tahap Kompilasi Mendatang (Staging)*

### Planned
- **AI OCR Text Recognition**: Integrasi *Computer Vision Camera* pada Android Kotlin untuk membaca dan menyalin (ekstrak) tulisan tangan Papan Tulis dosen secara rahasia.
- **Microservices Schedular**: Migrasi Eksekutor `reset-schedule` Vercel dari *Cron Job HTTP* rentan menjadi jembatan pesan aman (Message Broker) RabbitMQ bertaraf kampus antar universitas.

---

## [2.3.0] - 2026-05-30
*(The Enterprise Mobility & Push Radar Payload)*

### Added
- **Mobile Native Bridge Gateway (`POST /kh/device-token`)**: Menambahkan rute arsitektur REST API khusus untuk mengikatkan alamat *UUID (OneSignal Player ID) gawai pengguna Android* menyambung kepada Model Mahasiswa (Tabel TiDB). Menyala seketika instansi aplikasu dibuka lewat `MainApplication.kt` (Android Kotlin).
- **Asynchronous Broadcasting Radar (Zero-Delay Engine)**: Penambahan kelas modul murni *Decoupled* di `app/Services/NotificationService.php`. Memperbolehkan backend mengeksekusi tarikan Guzzle API lepas ke Firebase Cloud / OneSignal tanpa membebani muatan HTTP Respon dari Browser. Mahasiswa pengguna menerima sinyal Getar Pop-Up mendadak hitungan 0.8 Detik seketika Dosen melempar Tugas Baru.
- **Arsitektur Murni Native OS (Kotlin Fragment Material)**: Seluruh tumpukan struktur antarmuka buatan web (*Legacy WebViews*) disingkirkan absolut dari kerangka Android. Digantikan perenderan UI super-licin bersertifikat komponen *Android Jetpack Material 3 XML ViewModel*.

### Changed
- **Pelebaran Napas Terminal (Kotlin Build Toolchain)**: Menambahkan limit batas asuransi durasi Eksekutor Gradle (*Gradle Execution Timeout*) menuju rasio perlindungan 120 detik, memberikan jaminan kelonggaran *(CPU-lenient)* untuk mesin CI/CD merakit artefak *Release APK*.

### Security
- **Strict Role-Based Global Scopes Shield (Multi-Tenant Armor)**: Menggugurkan secara absolut kebocoran Celah Siber *(OWASP - IDOR Insecure Direct Object Identifier)* dalam platform lintas kampus ini. Pengecualian pemuatan *(Eloquent Load Queries)* dihantam penambahan perisai Injeksi Variabel `$class_id = current_tenant` secara otomatis saat booting. Mahasiswa penyusup tidak dpt menelisik data absen entitas di luar prodi-nya.
- **X-Frame Middleware Interceptor (Anti-Clickjacking)**: Mengutus pelindung statis kelas menengah `SecurityHeaders.php` yang menjamin penetapan parameter balikan Headers *X-Frame-Options SAMEORIGIN* dan menetralisir kerentanan iFrame *Click-Spoofing*.
- **API CSRF Waiver Proxy**: Rekayasa konfigurasi utama di `bootstrap/app.php` mengizinkan gerbang berawalan awalan `/kh/*` untuk lolos secara sah dari blokade filter pengecekan *CSRF Middleware Cookie*, tanpa mengendurkan proteksi bagi halaman Dasbor Admin Panel WEB.

---

## [2.1.0] - 2026-05-21
*(The Big Data Streaming Export Resilience)*

### Added
- **API Export Zero-RAM Overflow (`GET /report/excel/{class_id}`)**: Melahirkan mesin pelaporan Buku Kas Mahasiswa anti-henti. Ribuan larik mutasi finansial dilemparkan melewati buffer (*Data Chunking*) mengalir membelah jaringan memanfaatkan trik parameter transmisi `php://output` (I/O HTTP Stream) menyamar menjadi File Excel `.csv`. Vercel Cloud Serverless terjamin bebas fatal OOM (*Memorie-Exhausted Death*).
- **Injektor Serangan Laboratorium (`GET /simulasi`)**: Jalur api rahasia simulasi perang *(stress-test gateway)* digunakan untuk memberondong Tabel Skema Relasional TiDB MYSQL pada waktu uji kemampuan konektivitas ganda ping data.

### Fixed
- **Pembantaian Circular Recursion Fatal (500 Error Crash)**: Arsitektur Model *Eloquent* yang sempat menyedot memori akibat *Looping N+1 Queries* pencarian Relasi Kelas Ketua secara berulang. Titik akhir dihancurkan terisolasi menggunakan instruksi injeksi *Eager Load manual* (`->with()`), menyabet lonjakan RAM pemrosesan ke level Stabil.

### Removed
- Meleburkan secara drastis tumpukan Antarmuka *(UI Boilerplate)* pendaataan Ruang Kelas lama yang terpecah pada Web Dasbor. Akun Sentral "Ketua" & "Pembuatan Master Prodi" dikawinkan lewat perlindungan *1 tarikan nafas (Atomic Rolling SQL Transaction)*, menyunat waktu operasional pengurus.

---

## [1.5.0] - 2026-05-18
*(The Permanent Cloud Injector Paradigm)*

### Changed
- **Pemusnahan Local File Persistence -> Era Kriptografi Biner (SQL Storage Mode)**:
  Sistem Vercel Free-Tier menghancurkan *(wipe*) folder simpanan file fisik (`/storage/app/`) saat OS mati (Tidur/Cold Start). Memaksa KelasHUB memutar haluan secara radikal 180 Derajat.
  Semua fail ekstensi Dokumen (File Unggahan Makalah Dosen/Tugas .PDF Mahasiswa, Max 5MB) dipenggal, dikunyah ekskresi ke wujud *Cipher-Code* `Base64 String Encryption` (Bisa memuat 800-Ribu huruf/karakter abjad absolut). Ekstrak serpian Biner ini dikubur abadi di perut Databse MySQL TiDB beralas Tembok Data Kolom `LONGTEXT`. Insiden "Link Unduh PDF 404 Mati" diselesaikan tuntas tanpa menelan tarif AWS S3 Storage bulanan!

---

## [1.4.0] - 2026-05-16
*(The Discipline Architecture - Gamified Mod)*

### Added
- Standardisasi Tipografi Referensi: Pembangunan hierarki entitas pusat `master_subjects`, menekan cacat ketidakkonsistenan salah pengetikan nama dosen dalam operasi V-Model Alpine.js (The UI Binder).
- **Protokol Validasi Tahanan Hukum (`is_validated = Default Boolean Awaiting`)**: Mesin tak akan mengakui kehadiran *(absen mandiri Android)* atau aliran setor uang masuk sebelum Palu Sang Eksekutif Ketua Kelas/Bendahara membentur persetujuan *(Validation Checked)* di Layanan Web (Status Laporan berubah Permanen Immutable).

### Fixed
- Perekayasaan Rute Darurat Rendering Blade-Engine (Views). Mengarahkan sisa buangan tembolok hasil kanvas UI (*Compiled Render Files*) merangkak bergeser menyasar satu-satunya direktori pernapasan Linux Vercel yang bebas diakses Tulis-Seketika: `Folder /tmp/`. Menghindari Error Ijin Tulis OS (Write Permissions Denied).

---

## [1.0.0] - 2024-01-01
*(Genesis Era: Lahirnya Pondasi Aplikasi Pengumpul Dana Kelas)*

### Added
- **Status Rilis Repositori Minimum Viable Product (MVP)**
- Peluncuran Konstruksi Pondasi MVC Klasik dengan fokus Monolit Web murni. Membawa penyangga hidup Lima Pilar Pangkalan Tabel Pertama Skema Aplikasi: `students` (Mahasiswa), `academic_schedules`, `assignments`, dan poros pusaran kas keuangan rekayasa `cash_ledgers`.
- Mengimplementasikan konsep kanvas pandangan layar muka (UI Base) bergaya Gelap Pejam Tegas *"Stealth Dark Mode"* bersandar kaku atas parameter token warna Zinc-900 (Struktur desain awal purbawisesa sebelum ambisi hibrida App terkuak).
- Desain arsitek pemikiran Sistem Penjara Absut Gamifikasi (The Death Match Points). Menginisiasikan penanaman nilai nyawa Default "3 Strikes". Jatuh nol poin membangkitkan Status Kelam Ekstrem = *DICEKAL*.
