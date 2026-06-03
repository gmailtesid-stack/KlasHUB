# Changelog Perjalanan Waktu (Historical Log) — KelasHUB

Dokumentasi jejak modifikasi arsitektur proyek ini dipetakan secara akurat dari titik Nol (Prototipe Website Manajemen Sederhana) yang perlahan menetas menembus limitasi awan (*Cloud Serverless*) dan berkembang-biak menjadi Entitas Native Android.  
Format dokumen ini mengacu secara ketat pada [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased]

---

## [2.3.0] — 2026-05-30 (The Zenith: Web Meets Mobile Native & Security Push)

### Added (Penambahan Era Super-App)
- **Ekosistem Penyiaran Lintas Platform (OneSignal Push)**: Rangkaian penyiaran REST API v2 dipasang dalam paru-paru Backend Laravel. Sistem kini tak hanya mengirim rekam jejak Web Internal, namun melempar pop-up latar belakang (Background Notif) menuju perangkat Hardware Android secara murni *Real-time*.
- **Pintu Gerbang Endpoint Mobile (`POST /kh/device-token`)**: Penambahan Routing REST JSON tanpa UI. Web Monolith membenturkan fungsi Hibrida, memungkinkan kompilasi APK Android mengikatkan Sesi HTTP *cookie persisten* guna merelasikan Token UUID Android `onesignal_id` terhadap tabel Identitas Database TiDB.
- **Transmisi Notifikasi `NotificationService`** (`app/Services/NotificationService.php`): Otak pengirim (Controller) terpisah (Decoupled). Bertugas menetapkan apakah Siaran Notif tertuju pada satu orang Mahasiswa Validasi (*notifyStudent*), atau memborbardir Broadcast seluruh anggota Web kelas tersebut (*notifyClass*).
- File rahasia Modul Kotlin pada sub-direktori `/android-webview`: *MainApplication.kt* dan *MainActivity.kt* mendarat mulus mengoperasikan *Kotlin Coroutines* dalam menyinkron token OneSignal agar sejalan dengan siklus Boot perangkat Android.

### Fixed (Penambalan Pintu Darurat)
- **Eksekusi Penutupan Eksploit Web IDOR**: Pengetatan kerangka kueri. Upaya mahasiswa memasukkan ID kelas fiktif pada *URL Get Request* diputus aliran aksesnya melalui traksi *Global Scope*.
- **Penyuntikan Filter Header Ekstrem (SecurityHeaders.php)**: Menggusur ancaman eksploitasi pembajakan i-Frame UI *Clickjacking* di layar Browser pengguna, bersama mitigasi serangan skrip asing (XSS).

### Changed 
- Optimalisasi Timeout *Build Gradle* Android dinaikkan menjadi 120 detik, memberikan kelonggaran nafas pada kompilasi mesin lambat.  

---

## [2.1.0] — 2026-05-21 (The Vercel Stateless Endurance)

### Added
- **Mesin Validasi Beban Berlebih** (`SimulasiController`): Endpoint injeksi simulasi pertempuran data di Backend. Web secara ajaib memusnahkan waktu tunggu panjang (Dari eksekusi 60s menjadi < 4s), demi memenuhi titah batas umur fungsi *Free Tier Vercel Serverless*.
- **Alkimia Ekspor Pelaporan 0-RAM (Reporting Engine)** (`LaporanController`): 
  - `exportPdf()`: Ekspor File Laporan format Pemerintahan berbasis `DomPDF` Monokromatik.
  - `exportExcel()`: Evolusi manipulasi aliran unduh (Download Stream). Melontarkan seribu data Kas/Absen via *`php://output` CSV* secara telanjang demi menolak pengurasan Memory RAM Cloud VPS.
- Integrasi Tabel Penyimpan Jejak Sejarah `notifications`. Papan notifikas Web (Lonceng Dashboard) tak lagi bergantung pada _Session Flash_ sementara.

### Fixed
- **Pembantaian 500 Fatal Error (Limit Relasi Web)**: Arsitektur Model *Eloquent Trait BelongsToClass* pernah membuat siklus *Infinite Recursion Query* tatkala Web berusaha menggambar tabel Kelas beserta relasi Kepengurusan (`ketuaKelas`). Sistem terurai lewat pemisahan kueri manual *eager loading*.

### Changed
- **Peleburan UX Super Admin (Atomic Registry)**: Proses pembuatan Entitas Kelas dan Akun Super Ketua dijadikan satu nafas tarikan panjang *Database Transaction*. Interaksi layar Dashboard Web menyusut drastis, menghapus belasan form sampah peninggalan v1.0.

---

## [1.5.0] — 2026-05-18 (The Repository Storage Rebirth)

### Added
- **Revolusi Penyimpanan Nirvana Base64**: Penyimpanan *File Storage Local* (sistem konvensional) dikutuk karena lumpuh dan reset otomatis tatkala *mesin Vercel tertidur*. Kami memblokade Folder direktori uploads. Semua kiriman Tugas Mahasiswa maupun Makalah PDF Dosen (File Upload Interception) dikunyah dan disandingkan berwujud deretan String panjang eksotis (`Base64 Mime Code`) serta terkubur dalam rongga `LONGTEXT` di pusat Database MySQL. Cacat File-Hilang *(404 Not Found Broken Link)* tertumpas abadi.

---

## [1.4.0] — 2026-05-16 (The Hardened Rule of Engagement)

### Added
- Standardisasi Nama Mata Kuliah: Hadirnya master tabel khusus `master_subjects`, menjinakkan kesalahan tipografi pengisian nama dosen di antarmuka Front-End Alpine.js.
- Papan Transmisi Kuliah Real-Time Web (Toggle Online/Offline di Dashboard).
- **Kultivasi Hukum Pidana Akademis `is_validated`**: Cikal bakal kedisiplinan sistem! Sebuah modul Tugas, Absensi Mandiri, hingga Penerimaan Kas baru akan tampil ber-status (Suspended/Tertahan/Abu-Abu) sebelum Sang Eksekutor Web (Ketua/Sekretaris) melancarkan Klik Validasi sahnya.

### Fixed
- Pemisahan penyetoran Cache Blade (Compiled View Web) ke arah satu-satunya direktori hidup sistem Serverless `/tmp/`. Disusul *Session Cookie Strategy* murni (Gagalnya login Web di atasi total).

---

## [1.0.0] — 2024-01-01 (Genesis: Lahirnya Visi Website Manajemen Terpusat)

### Added
- Pencanangan Repositori *Minimum Viable Product (MVP)* Pertama KelasHUB. 
- Murni beroperasi layaknya aplikasi **Website Konvensional** di era arsitektur MVC Laravel konvensional. Diciptakan saat penat akan masalah mahasiswa mencatat uang buku kas kelas di selembar kertas lusuh lecek!
- Keutamaan Relasional Lima Pilar Pertama: `students`, `academic_schedules`, `assignments`, transaksi uang riil `cash_ledgers`, serta kejamnya palu absen `class_attendances`.
- **Modul Sang Jati Diri Awal (Nyawa Permainan Absen)**: Penguncian otomatis skor Kedisiplinan Kehadiran *(The 3 Life Tolerance Penalty Rule)*. Hadirnya *Alfa* memakan *Life Status*, merugikan status kemahasiswaan menuju jurang `DICEKAL`.
- Mengimplementasikan pewarnaan Antarmuka Pertama *(Zinc-900 Stealth Black)* dengan interaktivitas Blade Template Native untuk web browser. Merupakan rintisan (embrio awal) sebelum proyek nekat merengkuh ambisi *Mobile Native API App*.
