# 📖 Dokumentasi API — KelasHUB

Dokumen ini menjelaskan semua endpoint HTTP yang tersedia di KelasHUB, parameter yang diterima, dan contoh respons yang dikembalikan.

> **Base URL:** `https://klas-hub.vercel.app`  
> **Autentikasi:** Session Cookie (login via `POST /login` terlebih dahulu)  
> **Format Respons:** JSON untuk semua endpoint AJAX, HTML Redirect untuk form submission

---

## 🔑 Autentikasi

### `POST /login`
Masuk ke sistem menggunakan nama lengkap dan password.

**Body (form-data):**
```
name     : "ARIYAS PRATAMA RAMADHAN"   (Nama lengkap, case-sensitive)
password : "231011403268KK"            (NIM + "KK" untuk Ketua, atau custom)
```

**Response:**
- **Sukses:** Redirect → `GET /dashboard`
- **Gagal:** Redirect kembali ke `/login` dengan error message

---

### `POST /logout`
Invalidasi sesi aktif dan redirect ke halaman login.

**Headers:** `_token: {CSRF_TOKEN}` (form hidden field)  
**Response:** Redirect → `GET /`

---

## 📊 Dashboard

### `GET /dashboard`
Halaman utama dashboard pengguna. Mengembalikan HTML view `dashboard.main_mobile`.

**Data yang dikirim ke view:**
| Variable | Tipe | Deskripsi |
|:---|:---|:---|
| `$student` | `Student` | Data pengguna yang login |
| `$absensi` | `Collection` | Rekap sisa nyawa per mata kuliah |
| `$saldo_kas` | `int` | Saldo kas berjalan (total income - expense) |
| `$pemasukan_mingguan` | `int` | Total pemasukan minggu ini |
| `$pengeluaran_mingguan` | `int` | Total pengeluaran minggu ini |
| `$master_subjects` | `Collection` | Daftar semua mata kuliah |
| `$jadwal_harian` | `Collection` | Jadwal kuliah yang aktif |
| `$pending_count` | `int` | Jumlah data menunggu validasi (untuk admin) |
| `$academic_classes` | `Collection` | Semua kelas (hanya Super Admin) |

---

### `GET /kh/api/dashboard-data`
Endpoint AJAX untuk refresh data dashboard tanpa reload halaman.

**Middleware:** `auth`, `role:ketua_kelas`  
**Response JSON:**
```json
{
  "students": [...],
  "modules": [...],
  "assignments": [...],
  "cash_flows": [...]
}
```

---

## 🎓 Akademi — Jadwal & Mata Kuliah

### `POST /kh/schedule`
Tambah jadwal kuliah baru.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Body:**
```json
{
  "subject_name": "Rekayasa Perangkat Lunak",
  "lecturer_name": "Dr. Ahmad Fauzi",
  "day": "Senin",
  "time_start": "08:00",
  "time_end": "10:00",
  "room": "Lab 301"
}
```
**Response:** Redirect dengan flash message

---

### `POST /kh/schedule/toggle-delivery`
Toggle mode Online/Offline untuk jadwal kuliah tertentu.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Body:**
```json
{ "schedule_id": 5 }
```
**Response JSON:**
```json
{
  "success": true,
  "new_type": "online",
  "message": "Mode berhasil diubah ke Online"
}
```

---

### `POST /kh/master-subject`
Tambah mata kuliah baru ke daftar master.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Body:**
```json
{
  "name": "Kecerdasan Buatan",
  "sks": 3,
  "code": "06TPLE025",
  "default_lecturer": "Prof. Dian Pratiwi"
}
```

---

### `DELETE /kh/subject/{id}`
Hapus mata kuliah dari daftar master.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Response JSON:**
```json
{ "success": true }
```

---

## 📋 Presensi

### `POST /kh/attendance`
Input absensi kelas untuk satu sesi perkuliahan.

**Middleware:** `auth`  
**Catatan:** Role `mahasiswa` hanya bisa input absensi untuk dirinya sendiri (Rekap Mandiri).

**Body:**
```json
{
  "subject_name": "Rekayasa Perangkat Lunak",
  "date": "2026-05-21",
  "notes": "Kelas pengganti",
  "attendances": [
    { "student_id": 1, "status": "Hadir" },
    { "student_id": 2, "status": "Alfa" },
    { "student_id": 3, "status": "Izin" }
  ]
}
```

**Status yang valid:** `Hadir` | `Alfa` | `Sakit` | `Izin`

**Response JSON:**
```json
{ "success": true }
```

---

## 📚 Repositori Modul

### `POST /kh/module`
Upload modul mata kuliah (file atau link URL).

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Content-Type:** `multipart/form-data`

**Body (tipe `link`):**
```
type         : "link"
subject_name : "Pemrograman II"
title        : "Slide Pertemuan 5: OOP"
link_url     : "https://drive.google.com/file/xxx"
```

**Body (tipe `file`):**
```
type         : "file"
subject_name : "Pemrograman II"
file         : [binary file, max 4MB, .pdf/.doc/.docx/.txt]
```

**Response JSON:**
```json
{
  "success": true,
  "module": {
    "id": 101,
    "title": "Slide Pertemuan 5: OOP",
    "type": "link"
  }
}
```

---

### `GET /kh/module/{id}/download`
Download atau akses modul berdasarkan ID.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Response:**
- Tipe `link`: Redirect ke URL eksternal
- Tipe `file`: Binary stream dengan header `Content-Disposition: attachment`

---

## 📝 Tugas

### `POST /kh/assignment`
Input tugas baru untuk kelas.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Body:**
```json
{
  "subject_name": "Basis Data II",
  "title": "ER Diagram Studi Kasus",
  "description": "Buat ER Diagram untuk sistem perpustakaan",
  "deadline": "2026-05-28 23:59:00",
  "material_link": "https://classroom.google.com/xxx",
  "type": "individual",
  "members": null
}
```

**Tipe tugas:** `individual` | `kelompok`  
**Response JSON:**
```json
{
  "success": true,
  "assignment": { "id": 30002, "title": "ER Diagram Studi Kasus" }
}
```

---

## 💰 Keuangan Kas

### `POST /kh/cash`
Catat transaksi keuangan kelas (masuk atau keluar).

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Body:**
```json
{
  "student_id": null,
  "type": "income",
  "amount": 50000,
  "description": "Iuran mingguan angkatan ke-5",
  "transaction_date": "2026-05-21"
}
```

**Tipe transaksi:** `income` (pemasukan) | `expense` (pengeluaran)  
**Response JSON:**
```json
{
  "success": true,
  "ledger": { "id": 3, "type": "income", "amount": 50000 }
}
```

---

## 👤 Manajemen Mahasiswa

### `POST /kh/student`
Daftarkan mahasiswa baru ke kelas.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Body:**
```json
{
  "name": "BUDI SANTOSO",
  "nim": "231011401500",
  "role": "mahasiswa",
  "class_id": 1
}
```

**Password** dibuat otomatis = `nim` (login pertama, mahasiswa harus ganti).

**Response:** Redirect dengan flash message

---

### `DELETE /kh/student/{id}`
Hapus mahasiswa dari sistem.

**Middleware:** `role:ketua_kelas,sekretaris,bendahara`  
**Response JSON:**
```json
{ "success": true }
```

---

### `POST /kh/student/{id}/role`
Ubah jabatan (role) seorang mahasiswa dalam kelas.

**Middleware:** `role:ketua_kelas`  
**Body:**
```json
{ "role": "sekretaris" }
```

**Role valid:** `ketua_kelas` | `sekretaris` | `bendahara` | `mahasiswa`  
**Response JSON:**
```json
{ "success": true, "new_role": "sekretaris" }
```

---

## 🏛️ Super Admin — Manajemen Kelas

### `POST /kh/class`
Daftarkan kelas baru beserta Ketua Kelasnya dalam satu operasi atomik.

**Middleware:** `role:ketua_kelas` (termasuk `super_admin`)  
**Body:**
```json
{
  "name": "ARIYAS PRATAMA RAMADHAN",
  "nim": "231011403268",
  "code": "06TPLE013",
  "department": "Teknik Informatika",
  "contact": "08123456789"
}
```

**Efek:**
1. Membuat record di `academic_classes` dengan kode `code`
2. Membuat akun `students` dengan role `ketua_kelas`
3. Password otomatis = `NIM + "KK"`

**Response:** Redirect dengan flash success message

---

## ✅ Validasi Data

### `POST /kh/validate`
Validasi (approve) data yang masih berstatus `is_validated = false`.

**Middleware:** `role:ketua_kelas`  
**Body:**
```json
{
  "type": "attendance",
  "id": 42
}
```

**Tipe valid:** `attendance` | `cash` | `module` | `assignment` | `schedule`  
**Response JSON:**
```json
{ "success": true }
```

---

## 📈 Laporan

### `GET /report/pdf/{class_id}`
Download laporan keuangan kelas dalam format PDF.

**Middleware:** `auth`  
**Response:** Binary stream, `Content-Type: application/pdf`, `Content-Disposition: attachment; filename=Laporan-Keuangan-{code}.pdf`

---

### `GET /report/excel/{class_id}`
Download laporan keuangan kelas dalam format CSV.

**Middleware:** `auth`  
**Response:** Streaming CSV, `Content-Type: text/csv`, `Content-Disposition: attachment; filename=Laporan-Kas-{code}-{date}.csv`

**Kolom CSV:** `ID | Tanggal | Tipe | Jumlah | Nama Mahasiswa | Keterangan`

---

### `GET /kh/reports/attendance/pdf`
Download laporan presensi kelas dalam format PDF.

**Middleware:** `auth` | **Response:** PDF stream

---

### `GET /kh/reports/attendance/excel`
Download laporan presensi kelas dalam format Excel (CSV).

**Middleware:** `auth` | **Response:** CSV stream

---

### `GET /kh/reports/cash/pdf`
Download laporan keuangan kelas dalam format PDF (via ReportController).

**Middleware:** `auth` | **Response:** PDF stream

---

### `GET /kh/reports/cash/excel`
Download laporan keuangan kelas dalam format Excel (CSV, via ReportController).

**Middleware:** `auth` | **Response:** CSV stream

---

## 🤖 Engine Khusus

### `GET /simulasi`
Jalankan simulasi aktivitas kelas otomatis.

**Middleware:** `auth`  
**Durasi:** ~4 detik (aman dari timeout Vercel)  
**Response JSON:**
```json
{
  "success": true,
  "message": "Simulasi selesai dalam rentang < 5 detik",
  "total_inserted": 6,
  "environment": "Vercel Optimized"
}
```

---

### `GET /test-full`
Jalankan semua uji fungsional dalam satu request.

**Middleware:** `auth`  
**Efek:** Insert data uji ke `assignments`, `learning_modules`, `class_attendances` (3 Alfa), dan `cash_ledgers`

**Response JSON:**
```json
{
  "status": "Testing Finalized",
  "results": {
    "upload_tugas": "SUCCESS (ID: 30002)",
    "upload_modul": "SUCCESS (ID: 60002)",
    "notif_3_alfa": "SUCCESS (3 Alfa Inserted for ARIYAS)",
    "kas_manajemen": "SUCCESS (IN: 3, OUT: 4)"
  },
  "message": "Silakan cek Dashboard ARIYAS untuk melihat notifikasi DICEKAL dan data lainnya."
}
```

---

### `GET /kh/cron/reset-schedule`
Reset (truncate) seluruh jadwal kuliah. Dipanggil otomatis via Vercel Cron setiap `23:59 WIB`.

**Middleware:** `role:ketua_kelas`  
**Jadwal Cron:** `59 16 * * *` (UTC) = 23:59 WIB  
**Response JSON:**
```json
{ "success": true, "message": "Academic schedule reset successfully" }
```

---

## 🔐 Ganti Password

### `POST /kh/password`
Ganti password akun pengguna yang sedang login.

**Middleware:** `auth`  
**Body:**
```json
{
  "current_password": "231011403268KK",
  "new_password": "password_baru_saya",
  "new_password_confirmation": "password_baru_saya"
}
```

**Response JSON:**
```json
{ "success": true, "message": "Password berhasil diperbarui." }
```

---

## ⚠️ Error Codes

| HTTP Status | Penyebab Umum |
|:---:|:---|
| `401` | Tidak terautentikasi / sesi habis |
| `403` | Tidak memiliki role yang diperlukan |
| `404` | Resource tidak ditemukan |
| `422` | Validasi gagal (data tidak sesuai) |
| `500` | Server error / exception tidak tertangkap |
| `504` | Gateway timeout Vercel (request > 10 detik) |

---

*Didokumentasikan secara otomatis berdasarkan `routes/web.php` dan implementasi controller. Terakhir diperbarui: **21 Mei 2026**.*
