# Panduan Pengguna Ganda (User Utility Guide): Ekosistem KelasHUB 

**Sistem:** KelasHUB Hibrida Era Baru v2.3 (Menyatukan Desktop Web & Mobile Native)
**Kualifikasi Akses Pembaca:** Publik (Seluruh Tingkatan Mahasiswa & Kepengurusan Kampus)

Selamat datang di Panduan KelasHUB! 
Aplikasi yang Anda sentuh hari ini bukanlah sekring peninggalan web statis jaman dulu (v1.0). Melainkan sebuah Super-Ecosystem Hibrida lintas peramban yang menembus batas web. KelasHUB merajut dunia komputer laptop dengan getaran instan Handphone Anda.

Pilihlah instrumen operasi berdasarkan posisi jabatan akademis Anda pada struktur Kepengurusan Kelas.

---

## 🏗️ 1. Dua Wajah KelasHUB: Pilih Senjata Anda!

### 🖥️ A. Taktik Layar Lebar: Jalur KelasHUB Website (Sangat Disarankan Bagi Administrator)
Sejarah berdirinya KelasHUB bermula dari sebuah website, dan hingga kini, ketangguhan tampilan muka Web (`https://klas-hub.vercel.app`) tidak bisa digantikan. Anda, **Para Ketua, Sekretaris, dan Bendahara** diwajibkan menggunakan jalur Laptop/Peramban (Desktop) bila berurusan dengan ribuan data.
- **Kelebihan**: Layar besar menggunakan antarmuka Gelap Pejam (*Zinc-900 Dashboard*). Navigasi tanpa-memuat-ulang (*Alpine.js transitions*) sekencang angin. Sangat nyaman untuk mengunggah belasan PDF Modul, memeriksa Tabel Rekapitulasi Kas yang padat tulisan biner, maupun mengetik penugasan panjang dosen.
- **Penggunaan Asosiasi**: Ketik pada _Google Chrome_ atau browser PC Anda, masukkan login Nama Lengkap besar (KAPITAL) dan sandi sandi bawaan `(NIM-ANDA)KK` bagi kelas ketua.

### 📱 B. Jalur Mobilitas Mutlak: Aplikasi Native Android (Mahasiswa Biasa)
Menghilangkan kebingungan telat membalas *Chat Kampus WhatsApp*, fitur ini diperkenalkan pada gelaran ekspansi v2.3 KelasHUB. Mempersingkat operasi melalui instalasi **Aplikasi Android (.APK)**.
- **Kelebihan Mobile**: Terdorong arsitektur instan, beban halaman Web digantikan data *Json Ringan* (Irit Kuota Mahasiswa!). Aplikasi tidak perlu terus terbuka, OS Backend akan *mengintervensi sistem HP target* menyemprotkan Pop-Up Layar Penuh pada Push Pengumuman Ujian secara sinkron 0.8 Detik.
- **Peringatan Pemasangan**: Pasanglah Aplikasi ASLI dari grup mahasiswa kelas. Terpenting, biarkan saklar izin pelacak notifikasi aktif! (Jikalau ditolak, bel Anda putus mati koneksinya pada siaran Pusat OneSignal Cloud).

---

## 🎓 2. Area Penjelajahan Akademis Mahasiswa

### [A] Pengamat Kehidupan Akademik ("Pulse Pinalti Sisa Nyawa")
Absensi kertas digantikan Algo otomatis pinalti Hukuman *(Sisa Tolerance Ticket)*. 
- Tiap rentan mapel dipersenjatai kacamata default **3 Nyawa Toleransi**.
- Saat pengurus mencatat kehadiran, satu status ALFA mutlak menyedot Satu (1) Angka kehidupan tersebut.
- Sentuhan poin nol memicu Dasboard Web meledak peringatan dan App Mobile bergetar status: **DICEKAL (Pemblokiran Kritis)**.
- *Rekap Penyelamatan Nyawa*: Lempar form pengajuan Alasan Izin Khusus secara sepihak untuk diverikasi warna hijau oleh Petinggi Kelas (Ketua/Sekretaris).

### [B] Repositori File Awan Abadi
Dulu Anda memohon ulang pengiriman PDF ke dosen karena berkas memori internal kadaluwarsa WhatsApp hilang? Solusi repositori *Base64 Cloud Injections* Web kami menjawabnya. 
Seluruh presentasi, file modul ujian yang pernah eksis diletakkan permanen (terenkripsi ke dalam database String biner raksasa TiDB). Tekan Ikon Unduh Biru dari layar sentuh; modul dihidupkan ulang dekripsinya dan dilempar utuh ke folder `Downloads/` gawai Anda!

---

## 👑 3. Mode Pengendali Kasta Tinggi (Buku Manual Administrator)

Anda (Petinggi) direkomendasikan mengendalikan jalannya Kelas dari Monitor Web! Hindari Input transaksi ribuan data via Keyboard HP.

### Tab Otoritas Bendahara (Penyelamat Perhitungan Kas)
* Transparansi Kelas Terbuka! Apabila Anda merilis Form Injeksi Angka Rupiah Masuk (Income), Dashboard Android teman semahasiswa secara realtime berubah angkanya (Sync Real Time). Lupakan debat kotor kalkulator kuno mahasiswa, cukup 1 pencetan klik untuk melempar Laporan Neraca Saldo formal bulanan (Excel CSV atau Salinan Kertas PDF Formal). 

### Tab Otoritas Sekretaris (Loud-Speaker Kelas)
* Jika tugas dosen yang tenggat esok subuh diletakkan pada formulir "Penugasan Mahasiswa". *Push Action Module* dari Dashboard (Web) akan memicu siaran sinyal *(Payload Array Serverless OneSignal Cloud)* menyusup menyebarkan Broadcast getaran pemberitahuan mendesak melintasi gawai puluhan kawan sekelas anda serentak detik itu juga!

### Mahkamah Agung Ketua Kelas (The Final Validation Court)
Semua form permintaan absen Mahasiswa via Aplikasi Handphone *(App)* maupun *Web* = akan menggantung diam pada Status Abu-Abu *(Tertunda)*. 
1. Di bilik Web Dasbord maupun HP anda, gelembung konter notifikasi membengkak angkanya.
2. Anda harus mendaratkan Palu Validasi (Tekan Check-mark). Log akan terekam terkunci di database abadi. Kehadiran sukses dipangkas / diselamatkan.

---

## 🆘 Troubleshooting Evakuasi (Insiden & Kegagalan Bawaan) 

**Problem 1: Kawan Semangkuk Mengeluh: "Mengapa Notif Punya Saya Tidak Bunyi dan Layar Mati saat Ada PR Ujian?"**
*Analisa Forensik Keras (Untuk Mahasiswa Bersangkutan):*
- Kamu menekan tombol tolak saat App menuntut izin akses *Pop-up OneSignal* perdana dibuka? Masuk Halaman Pengaturan (Setting) OS Android -> Manajer Aplikasi -> Buka KelasHUB -> Angkat Centang Izin Pemberitahuan.
- ID Sinyal Vercel Retrofit Cloud terputus (*Cache Out*). Bukalah laci Profil Pojok Atasan, Tarik Geser menu dan Logout dari sesi Android sejenak. Menyuntik (Login) akun kembali akan merekognisi mesin HP barumu terangkai di SQL Data Notif Kampus. Beres!
 
**Problem 2: "Mengapa Dasboard Web Saya Lambat/Tidak Memunculkan Update Data Terbaru?"**
*Solusi Hard-Cache Bursting:*
Dikala pembaruan massal (Ribuan Transaksi Masuk), koneksi anda nyandet (*Client-Freeze*). Cukup perintahkan *Refresh Super Hard-Reset Browser PC* (Kombinasi `CTRL + SHIFT + R`). Sesi DOM *Alpine.js* memuat paksa bongkahan Data TiDB SQL murni mutakhir saat itu juga. 

*— Selamat mengarungi ombak evolusi edukasi KelasHUB. Teruji tanpa kompromi! —*
