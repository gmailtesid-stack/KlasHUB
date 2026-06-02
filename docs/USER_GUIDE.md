# Panduan Pengguna (User Guide): KelasHUB

**Proyek**: KelasHUB — All-in-One Class Operations Suite  
**Author**: Ariyas Pratama Ramadhan By WaveProject.ID  
**Versi**: 2.3.0  
**Bahasa**: Indonesia  
**Tanggal**: 30 Mei 2026

---

## 1. Pengenalan
Selamat datang di **KelasHUB**, platform operasional kelas modern yang dirancang untuk memudahkan manajemen perkuliahan, transparansi keuangan, distribusi materi, dan kini dilengkapi **notifikasi push real-time** langsung ke HP Anda.

Fitur unggulan:
- 📱 Notifikasi pop-up saat ada tugas baru, modul, atau pengumuman.
- 📊 Dashboard personal dengan sistem **Sisa Nyawa** untuk memantau kehadiran.
- 💰 Transparansi penuh kas kelas secara real-time.

---

## 2. Memulai — Download & Login Aplikasi

### 2.1 Via Aplikasi Android (Direkomendasikan)
1. Install file **APK** KelasHUB yang dibagikan oleh pengurus kelas.
2. Buka aplikasi. Notifikasi push akan aktif secara otomatis sejak pertama kali dibuka.
3. Login dengan NIM dan password Anda.

### 2.2 Via Browser
1. Buka `https://klas-hub.vercel.app`
2. Masukkan **Nama Lengkap** (huruf kapital) sebagai username.
3. Masukkan password default dari Ketua Kelas.

### 2.3 Mengganti Password
Sangat disarankan untuk mengganti password default:
1. Di sidebar (Desktop) atau menu Profil (Mobile), pilih **"Ganti Password"**.
2. Masukkan password lama dan buat password baru (minimal 6 karakter).

---

## 3. Fitur Utama Mahasiswa

### 3.1 🔔 Notifikasi Push
Fitur **baru di v2.3** — Anda akan menerima pop-up di HP saat:
- Ada **tugas baru** yang diunggah pengurus.
- Ada **modul pembelajaran** yang dibagikan.
- Ada **transaksi kas** yang berkaitan dengan Anda.
- Pengajuan **rekap mandiri** Anda perlu divalidasi.

> **Penting:** Pastikan Anda menggunakan **aplikasi Android**, bukan browser, agar fitur notifikasi berfungsi. Izinkan notifikasi saat pertama kali membuka aplikasi.

### 3.2 🎓 Akademi Hub
Menu pusat informasi akademik harian:
- **Jadwal Kuliah**: Lihat mata kuliah hari ini, ruangan, dan dosen. Perhatikan indikator **OFFLINE** (biru) atau **ONLINE** (kuning).
- **Tugas & Deadline**: Daftar tugas terdekat. Klik untuk melihat detail dan link pengumpulan.
- **Kalender Deadline**: Pantau deadline dalam tampilan kalender visual sebulan ke depan.

### 3.3 📚 Repositori Materi
Tempat mengunduh modul atau melihat link referensi yang dibagikan:
- Klik tombol **Download** untuk mengunduh modul langsung ke perangkat Anda.
- Modul tersedia dalam format PDF, DOCX, TXT, atau link eksternal.

### 3.4 💰 Finansial Kelas (Buku Kas)
Transparansi penuh terhadap kas kelas:
- **Saldo Saat Ini**: Total uang yang ada di bendahara.
- **Riwayat Transaksi**: Setiap pengeluaran dan pemasukan yang telah divalidasi.

### 3.5 ❤️ Presensi Tracker & "Sisa Nyawa"
Fitur paling kritis di KelasHUB:
- **Sisa Nyawa**: Setiap mata kuliah memberikan **3 Nyawa**. Setiap ketidakhadiran (Alfa) tervalidasi mengurangi 1 nyawa.
- **Status DICEKAL**: Nyawa = 0 → Status **DICEKAL** (Risiko nilai E). Segera hubungi pengurus kelas jika ada kesalahan data.
- **Rekap Mandiri**: Anda bisa menginput kehadiran sendiri, statusnya **PENDING** hingga divalidasi Ketua/Sekretaris. Pengurus akan mendapat notifikasi saat Anda submit.

---

## 4. Panduan Khusus Pengurus (Admin)

### 4.1 Ketua Kelas & Sekretaris
- **Input Jadwal/Tugas/Modul**: Setiap input baru akan otomatis mengirim notifikasi push ke seluruh anggota kelas.
- **Validasi Data**: Cek notifikasi di dashboard — setiap input dari mahasiswa harus disetujui agar muncul di laporan resmi.
- **Manajemen Anggota**: Tambah mahasiswa baru atau ubah peran (misal: promosi ke Bendahara).

### 4.2 Bendahara
- **Catat Kas**: Masukkan pemasukan/pengeluaran melalui menu Finansial. Anggota kelas akan mendapat notifikasi otomatis.
- **Export Laporan**: Unduh laporan dalam format **PDF** (formal) atau **Excel/CSV** dari tab Presensi.

### 4.3 Super Admin
- **Registrasi Kelas**: Gunakan form terpadu untuk mendaftarkan kelas baru sekaligus akun Ketua Kelas dalam satu langkah.

---

## 5. FAQ (Tanya Jawab)

**Q: Kenapa saya tidak dapat notifikasi?**  
*A: Pastikan Anda menggunakan aplikasi Android (bukan browser) dan sudah mengizinkan notifikasi. Coba logout dan login ulang agar token perangkat diperbarui.*

**Q: Kenapa tugas yang saya lihat berbeda dengan teman?**  
*A: Pastikan Anda berada di Kelas yang benar. Hubungi Super Admin jika ada kesalahan.*

**Q: Saya lupa password, bagaimana cara resetnya?**  
*A: Hubungi Ketua Kelas. Ketua Kelas bisa mereset password anggota melalui panel admin.*

**Q: Apakah aplikasi ini aman?**  
*A: Ya. KelasHUB menggunakan enkripsi SSL/TLS berlapis, perlindungan session cookie, dan API Key notifikasi disimpan aman di server — tidak terekspos ke aplikasi.*

---

© 2026 **WaveProject.ID** | Dikembangkan oleh **Ariyas Pratama Ramadhan** | v2.3.0
