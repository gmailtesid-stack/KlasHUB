# Product Requirement Document (PRD): KelasHUB v2.3.0 (Enterprise Specification)

**Status Tinjauan (Review Status)**: TERKUNCI & MENCAPAI TAHAP PRODUKSI (LOCKED & IN PRODUCTION)  
**Tahun Finansial / Kalender Siklus**: Q2 2026 - Sprint v2.3.0  
**Dokumen Arsitek Utama**: Tim Pengembang KelasHUB & WaveProject.ID  
**Klasifikasi Dokumen**: Rahasia Pabrikator (Confidential Operational Blueprint)

---

## 1. Abstrak Penyatuan Platform (The Unified Platform Vision)
**KelasHUB** v2.3.0 adalah titik puncak penyatuan visi dari sebuah sistem manajemen Kelas (Web Monolitik Klasik) yang menembus batas batas menjadi Super-App ekosistem lintas batas (*Web and Mobile Native Ecosystem*). Platform dibangun sebagai solusi *enterprise-grade* untuk manajemen operasional presensi, buku kas finansial, dan pengumpulan (LMS) Repository, di bawah kompartemen kebal (*Multi-Tenant Isolation*) Serverless.

---

## 2. Kausalitas Evolusi Sejarah Sistem (*The Historical Mandate*)

### 2.1 Mengapa Bukan Sekedar "Aplikasi Web Biasa" Lagi?
- **Fase Awal (Era v1.0.0)**: Awal mulanya, KelasHUB murni sebuah *Website Dashboard Laravel PHP*. Ia menyajikan rekayasa Blade HTML form. Seluruh kegiatan wajib digerakkan mahasiswa depan Laptop atau peramban HP. Keluhan membludak: Mahasiswa kerap alfa atau melewatkan tugas saat mereka tidak dengan sadar "membuka" website tersebut. Komunikasi krusial gagal terkirim seketika.
- **Kesimpulan Cacat Web**: Sistem presensi gamefication (Sisa Nyawa) menuntut peringatan interupsi paksa yang agresif (*Out-of-band Interrupts*). Sebuah antarmuka Web statis tidak bisa memaksa HP bergetar saat layar HP terkunci di kantong celana.

### 2.2 Tonggak Transformasi Strategis (The Hybrid Shift) 
Di sinilah kami membangun arsitektur hibrida. Vercel backend PHP tidak kami buang, melainkan kinerjanya digandakan:
1. **Desktop Admin Web Engine (Tetap Dipertahankan)**: Menggunakan teknologi UI reaktif Tailwind CSS & Alpine.js bagi *Layar Lebar Desktop* (Super Admin & Ketua) untuk pengelolaan transaksi massal (Upload PDF raksasa, Audit Ribuan Log Transaksi) dengan kenyamanan layar lebar (PC/Laptop).
2. **Native Kotlin Client Engine (Fase Baru Pertempuran v2.3)**: Kami melahirkan repo `/android-webview`. Aplikasi ini dirajut via Retrofit API Client. Bergerak lincah karena hanya memanggil Json dari Server. Dampak terbesarnya: Mengawini Identitas Mahasiswa Android UUID terhadap Signal *OneSignal Push Registration*. Menyelesaikan batas keterasingan komunikasi secara radikal!

---

## 3. Titik Resolusi Solusi Kunci Aplikasi (Solution Key Drivers)

### Resolusi A: Engine Finansial Transparan ("Kas-Kaca")
- Laporan Web Streaming Output (CSV): Transformasi data dari ratusan ribu baris tabel finansial keluar menjadi file fisik CSV tanpa pernah menyebabkan memori server jebol berkat *php://output streaming*.

### Resolusi B: Arsitektur Penghakiman Kedisiplinan Sistematis ("Tiga Sisa Nyawa")
- Gamifikasi absensi. Nilai dasar disetujui (3 *Lifetime Tickets*). Absen tanpa kejelasan = Peringatan pop-up melaju deras memacu detak HP mahasiswa seketika! Titik nol absensi akan menyebabkan akun ter-*locked* merah (Status DICEKAL). 

### Resolusi C: Penetrasi Ekstrim Jaringan Info ("Zero-Delay Notif")
- Terbelahnya ekosistem Web ke Mobile Native Android melahirkan Push Notification yang bergetar secara asinkron dalam durasi di bawah **800 miliseconds** (Kecepatan Injeksi Serverless OneSignal REST API).  Materi Tugas, Berkas Tagihan Bendahara, Modul baru: Seluruh interupsi ditujukan menganggu kedamaian layar ponsel jika terdapat agenda kelas. 

### Resolusi D: Kehampaan Fisik Server ("0 Maintenance")
- Pengubahan penyimpanan Fisik Modul dan Foto *(Media Repository)* menjadi penyimpanan abadi Enkripsi Karakter *LongText Base64* TiDB SQL Cloud. Menyeka segala bentuk kendala server-crash karena limit penyimpanan lokal (*Read-Only Stateless Disks* pada tier Free Vercel).

---

## 4. Analisis Matriks Hak Otorisasi Lintas Ekosistem

Pengkategorian Kasta Akses Terpisah:
| Stratifikasi Peran | Media Eksekusi Akses | Fungsional Dominasi (Dominant Core Routine) |
|:---|:---|:---|
| **Super Admin Platform** | Murni Web Laptop / Desktop | Cipta Opsi Ruang Kelas Sekali Jadi (Atomic Commit Class). Pembaruan silang akses (Cross Tenant bypass). |
| **Ketua Kelas** | Web Laptop + Mobile App | Memancarkan Otorisasi Hakim Validasi secara konstan. Merespon pemecatan data dan pengubahan peran (Role Switch). |
| **Sekretaris & Bendahara** | Web Laptop + Mobile App | Ekskusi Modul pembelajaran Base64 *(Sekretaris)* dan Injeksi arus debet/kredit Kas *(Bendahara)*. |
| **Mahasiswa Umum** | Murni Native Mobile Handphone | Pengguna Ujung (*Endpoint Entity*). Menerima ledakan Notif Hati-Hati Alfa. Memantau keuangan secara konstan. Membaca transparasi nilai dan materi secara portabel di HP. |

---

## 5. Kriteria Teknis Penyelesaian Evolusioner 

### 5.1 Infrastruktur TiDB SQL vs Vercel Timeout Hybrid
* **[Jaminan Standar Arsitektur]**: Web Console harus merespon HTML tanpa delay (Client-Transitions Alpine). Bersamaan itu, Rute API Android harus melontarkan String Json Array < 2 detik pada data besar (10.000 log history) lewat optimasi Kueri Tabel Tanpa Model (Raw Queries). 

### 5.2 Perlindungan Isolasi Siber Ekstrim (Cross-Tenant Multi-Tenancy Defenses) 
* **[Kriteria Validasi Evolusioner]**: Setelah sistem membelah antara Web dan Android App, injeksi ID Serang (*Attacker Packet*) meningkat. Seluruh gerbang model Eloquent wajib mengenakan *Global Scope Tenant ($classid)*, memblokir pencurian identitas mutlak dari pergeseran aplikasi API REST eksternal.

### 5.3 Kontinuitas Jantung Sinkronisasi Token Mobile Kotlin
* **[Jaminan Operasional Akhir]**: Pada rilis ini, Sistem API wajib menerima UUID *OneSignal Identifier* tiap rute App dibuka pada `POST /kh/device-token`. Bila siklus ini tewas, mahasiswa akan buta terputus dari pengumuman Notifikasi seumur hidup siklus Instalasi perangkat tersebut.

---

## 6. Proyeksi Evolusi SaaS (Feature Roadmap Jangka Panjang)

1. **AI Native Scanner (Q3 2027)**: Modul *Optical Character Recognition (OCR)* AI dalam Aplikasi Android, merubah bidikan Foto Catatan Papan Tulis Kelas langung menyalin ke dalam dokumen teks Repository Modul di Dashboard.
2. **Katalis Cloud Skala Berat (2028)**: Jika evolusi Base64 Database mencapai ukuran tabel multi-gigabytes TiDB Limit Teratas, pergeseran repositori CDN (Cloudflare R2 API) mutlak harus direkonstruksi total.
3. **Penyambungan Telekomunikasi Lintas Server**: Modul Cadangan Penyebaran Telegram / WhatsApp Bot menduplikasi alarm push OneSignal via antarmuka WhatsApp Bussiness.

*Penutup Dokumen Final Produksi Evolusioner - Mengakhiri Spesifikasi Bisnis Evolusi KelasHUB v2.3.0*
