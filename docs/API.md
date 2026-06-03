# Tabel Rujukan Utama API (OpenAPI REST Reference)

Dokumentasi ini adalah kontrak arsitektur otentik (The Absolute Source of Truth) bagi Interaksi Pertukaran Data JSON Mobile Android dan Frontend Web terhadap Vercel Gateway.

**Base URL Produksi**: `https://klas-hub.vercel.app`  
**Otentikasi Utama**: Mekanisme `laravel_session` Cookie berstatus *Stateful HTTP-Only* (Retrofit Android Client diwajibkan menggunakan struktur OkHttp `CookieJar`).

---

## 1. Layanan Jembatan Otentikasi (Authentication Gateway)

Pintu masukan (Entrypoint) sistem hibrida untuk Web dan Klien Android.

### 1.1 Injeksi Kredensial Masuk 
`POST /kh/api/login`

> **Pengecualian Proteksi (CSRF)**: Melewati pemeriksaan Web Token Middleware secara utuh. Terbuka.

| Parameter Payload (x-www-form-urlencoded) | Tipe | Mandatori | Keterangan |
|---|---|---|---|
| `name` | String | Wajib | Nomor Induk (NIM) ATAU Kepanjangan string Nama Valid |
| `password` | String | Wajib | Standar: NIM + Target Role (Cth `23100MHS`) |

**JSON Respons Berhasil `200 OK`**: Memulangkan Obyek User yang dikemas dan penetraan Kuki Berangkai C-UID di Header HTTP `Set-Cookie`.

```json
{
  "message": "Login successful",
  "user": {
    "id": 16,
    "class_id": 1,
    "nim": "0004",
    "name": "Budi Mahasiswa Biasa",
    "role": "mahasiswa"
  }
}
```

---

## 2. Saluran Informasi Harian Dasbor (The Heavy Loaders)

Seluruh panggilan di bawah blokir proteksi Ketat (Memerlukan Cookie Sesi Valid dari Login).

### 2.1 Tarikan Besar Layar Muka Klien (Dashboard Master Sync)
`GET /kh/api/dashboard-data`

Panggilan Tunggal Paling Berat (The Heaviest Endpoint). Dirancang merangkum Data Statistik, Penugasan (Assignments), Pengumpulan Laporan Modul E-Learning, Log Transaksi Neraca Uang, Profil Sesama Anggota (Friends), dan Kantong Darurat Notifikasi ke dalam 1 Kali HTTP Lemparan. Mencegah Android memanggil 6 URL berbeda yang rawan membuat baterai panas *(Throttling/Network Overload)*.

**JSON Respons Berhasil `200 OK`**:
```json
{
  "student": {
      "id": 1,
      "role": "ketua_kelas",
      "name": "Jefri Ketum"
  },
  "class_semester": 4,
  "qris_image": "Data:image/png;base64,iVBORw0KGgoA...[Dipotong Sangat Panjang]",
  "semua_mahasiswa": [], // Array Objek [Table: Students]  
  "semua_tugas": [], // Array Objek [Table: Assignments]
  "semua_modul": [], // Array Objek [Table: Learning_Modules]
  "transaksi_kas": [], // Array Arus Keuangan
  "notifikasi": [] // 20 Tumpukan Sinyal Log
}
```

---

## 3. Komando Reaktif Push (The Broadcasting Subsystem)

### 3.1 Registrasi Identitas Penyiaran Klien (OneSignal Token Injector)
`POST /kh/device-token`

Dipanggil di latar belakang oleh Klien Kotlin Retrofit tanpa izin pengguna (Silently via Coroutine), satu detik beriringan tatkala APK berhasil memasuki Dasbor Mobile.

| Parameter Body Payload | Tipe | Keterangan |
|---|---|---|
| `player_id` | UUID String | UUID OneSignal Khusus untuk Perangkat Android milik si Mahasiswa (Generate SDK). |

Menghasilkan Modifikasi Hening MySQL (`200 OK`). Bila dipanggil terus, dia hanya menimpa String Token yang sama (Operasi Aman Idempotent).

---

## 4. Mekanisme Unggahan Berkas Raksasa (File & Image Multipart)

### 4.1 Bungkusan Penambahan Kas Diserta Lampiran Bukti
`POST /kh/cash`

Karena melibatkan aliran foto Kwitansi Biner, formulir Web dan Android tidak menggunakan JSON mentah (`application/json`), melainkan protokol Form **`multipart/form-data`**.

| Form Part (x-multipart) | Tipe Tuntutan | Keterangan |
|---|---|---|
| `amount` | Float/Double | Nominal Uang (Misal: `250000.00`) |
| `type` | String Enum | `"income"` atau `"expense"` |
| `description` | String Panjang | Memo Tagihan/Struk Pembelian Logistik |
| `transaction_date` | String (YYYY-MM-DD) | Jejak Kalender Kejadian |
| `proof_image` | Blob/File Gambar | Bukti Fisik JPG/PNG (Max: ~2MB Limit) |

**Respon Balikan (Gagal 422 Unprocessable Entity)**:
```json
{
   "message": "The given data was invalid.",
   "errors": {
       "amount": ["Nominal Kas minimal Rp 1"]
   }
}
```
*(Catatan Arsitek: Bukti Gambar yang menyebrang sini tak akan terparkir di memori File/Disk linux app, melainkan dikonversi mentah jadi Kripto Base64 LongText di Engine MySQL).*

---

## 5. Administrasi Super Admin (Level Ketua Kelas / Root)

Rute maut ini diblokir rangkap ganda (Auth + Cek ID Enum `ketua_kelas` / `super_admin`). Apabila Mahasiswa Biasa membobol URL lewat Ekstensi Browser Postman, Server mengembalikan tamparan Exception `403 Forbidden Access Action`.

### 5.1 Pemberian Mandat Keputusan Eksekusi (The Judge / Validator Engine)
`POST /kh/validate`

Mengunci operasi Hukum Administrasi menjadi ber-Nilai Sah (`TRUE`).

| Parameter | Tipe | Keterangan |
|---|---|---|
| `id` | Integer | Primary-key Entity di Database (Cth: ID=5 untuk Laporan Alpha Mahasiswa x) |
| `type` | String | Model Target: `attendance`, `cash`, `assignment`, `module`, `schedule` |

Tindakan ini tidak bisa dihapus atau diputar ke status palsu! Hanya menarget 1 Status Permanen Arah.
