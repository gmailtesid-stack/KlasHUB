# Dokumen Protokol Handover Aset Proyek (Buku Transisi) — KelasHUB Ekosistem

**Dokumen Serah-Terima Kekuasaan (Inrastructure Handover)** ini menuntun dan mendikte Petinggi Departemen Kampus (Divisi Pemeliharaan TI / DevOps / Maintainers Muda Mendatang) yang menanggung beban kelanjutan hidup operasional seluruh konstelasi KelasHUB paska turunnya *WaveProject.ID* dari kursi pengembangan utama platform.

---

## 📋 1. Inventarisasi Aset Mahakarya (Klaim Hibrida Dua Dunia)

Porsi penyerahan kekuasaan ini membawahi pengalihan **2 Repositori Alam Beda Bahasa Secara Integral (Hybrid)** yang mempekerjakan Vercel PHP sebagai Gateway Tunggalnya!
1. **Sektor Desktop (The Legacy But Still Beating Heart)**: Kode Utama Akar Folder Pangkalan Sistem PHP *Laravel v13*. Rute web pengemas *Template Engine Blade* dibungkus Alpine.js.
2. **Sektor Mobile (The Native Sibling)**: Area pertahanan `/android-webview`. Aplikasi OS Kepingan Android (Kotlin-murni) menggunakan Jetpack XML *Material Architecture.*
3. **Persistensi Ekosistem Database Serverless**: Cluster Awan penyimpanan tabel relasi MYSQL dari platform raksasa [TiDB SQL Engine].

### Jaminan Garansi Kondisi Penyerahan (The Warranty Seal):
* Proyek dilepas pada siklus akhir pembasuhan kutu versi: **Rilis Produksi (v2.3.0 Release Candidate)**.
* Ekosistem telah melahap dan membunuh cacat keterasingan lintas Tenant *(Isolasi Multi-Kelas lewat perlindungan Global Trait Eloquent Murni)*. 

---

## 🔑 2. Ruang Kunci Brankas Rahasia Siber (Environment Vault Guide)

Keamanan infrastruktur Anda dipertaruhkan. Jauhi hasrat mengunggah (hardcode) Kunci Enkripsi (API Keys) di sela baris Controller GitHub! Seluruh DNA kehidupan Serverless Web ditelan masuk via penyimpanan kotak rahasia layar dasbor **Vercel Environment Variables Configurations**:

| Label Variabel Terlarang (Env Keys) | Reaksi Ledakan Kesalahan Fatal Bila Tersentuh Tanpa Rencana (Consequence) |
|:---|:---|
| `SESSION_DRIVER` | Wajib berkondisi statik kata tunggal: `cookie`. Jika iseng diganti `file`, ribuan sesi Web Dasbor mahasiswa serempak akan menolak terautentikasi tiap memuat muat laman ke-2 (Session Wiped Out per refresh). |
| `DB_HOST`, `PORT`, Password SQL | Akses terowongan Ping PDO ke raksasa Cluster TiDB Cloud. Memplesetkan tanda baca akan berakibat kelumpuhan Tampilan "Loading Kosong". |
| `MYSQL_ATTR_SSL_CA` | Target pendaratan perisai mutlak menunjuk urat akar Vercel Node Runtime: `/var/task/cacert.pem`. Mencabut injeksi file sertifikat CA TLS ini = *Access Denied PDO SQL Exception* (Mati Tertotal). |
| `ONESIGNAL_APP_ID` | Identitas Radar Panel Aplikasi OneSignal Web Cloud (UUID Utama). Mengganti tanpa konfirmasi membuat Token Mobile Client ditolak saat Registrasi (*POST /device-token*). |
| `ONESIGNAL_REST_API...` | API Token Master Gembok Belakang layar transmisi Guzzle HTTP. Jatuhnya ini kepada tangan mahasiswa iseng mengakibatkan Spam Push Jutaan kali tak terbatas. Jaga mati matian kunci *Server Auth* ini! |

---

## 🛠️ 3. Pedoman Siklus Pemeliharaan Abadi (The Maintenance Cadence)

Keistimewaan migrasi Hibrida *(Zero-Server Deployments)* KelasHUB v2.3 membuat Anda bisa menutup terminal kontrol dan tidur tenang selama bulan tenang perkuliahan. Vercel OS akan mematikan diri sendiri dan mendaur RAM sisa sisa sampah transaksi tanpa campur tangan mesin NGINX klasik. 

**Namun Cacat Fisik Basis Data Wajib Dikelola! (Tiap Rentang Semester Genap Akhir):**
- Anda **dilarang membiarkan penumpukan log tak berguna**. Tabel Riwayat Sisa Peninggalan `learning_modules` tidak menyangga file PDF fisik lokal! Melainkan mengantongi bongkahan Batu Panjang *String Base64 Binary*. 
- Membiarkan File Kuliah yang tak relevan teronggok 3-4 Tahun memancing ambruknya kuota Tabel Teratas SQL Free-Tier Database Anda. Hancurkan entitas PDF Makalah lama lewat antarmuka Sistem Manajemen Database C-Panel TiDB untuk melonggarkan Kapasitas!
- *Cron Job Harian Vercel*: Skrip Vercel `reset-schedule` akan dieksekusi menembus End-Point Web tiap jam tengah malam menghapus tabel antrian Harian Kuliah ke nol hari berikutnya (Tidak perlu Setup Linux Crontab manual!).

---

## 📱 4. Tata Cara Publikasi Roket Mobile Kotlin (.APK Native Releases)

Bila Kelompok Tim Developer Baru hendak meluncurkan Revisi Pembaruan ke Mahasiswa: Sektor Pemeliharan Web dan Seluler Terpisah Ruang Perakitannya. Panduan mengemas Roket Mobile Android *(Bukan merilis halaman Web)*:

1. Modul Aplikasi Android kita steril, murni, tulen: *No Flutter, No WebView kosong berbungkus C-Sharp, No React Native Bloats.* Hasilkan perbaikan menggunakan instrumen Google _Android Studio Jetpack Kotlin SDK_.
2. Jika ada penambahan Modul UI API baru, sesuaikan pengumpulan Data Kontrak JSON Model pada `/api/ApiInterface.kt`. 
3. *Waktunya Pengepakan Jeroan Mesin!* Eksekusi Terminal Komando di atas teritorial repo `android-webview/`:
   - Meluncurkan Bundel Mode Produksi Terjilid: `./gradlew assembleRelease`
4. Artefak Berwujud Android Package siap panen bakal bercokol (bertelur) aman tepat di: `app/build/outputs/apk/release/`. Distribusikan lewat portal siaran GitHub Release atau Group Papan Telegram Nasional.

---

> Menutup Tirai Kendali. Mahakarya lintas-teknik pemrograman Web yang mendidik sistem menjadi hibrida api ganda. Sebuah relik arsitektur yang sangat diidamkan kampus berdana mahasiswa kecil yang enggan menyewa Insinyur System-Admin VPS tiap bulannya. Pertahankan Integritasnya.  
>  — *Wakil Komando Serah Terima, (M. Ariyas / WaveProject.ID)* 
