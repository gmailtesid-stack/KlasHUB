# 📖 Kontrak Pusat Sistem API (Documentation & Routes Handbook) — KelasHUB

Dokumen Ekstensif ini merupakan saksi kunci *Source of Truth (SOT)* bagi tim Perekayasa Skrip Ujung (*Front-End Browser* & *Android Kotlin Engineer*) dalam berkomunikasi menterjemahkan Bahasa Mesin sistem Backend KelasHUB v2.3.0.

> **Pusat Gelombang Server Utama:** `https://klas-hub.vercel.app`  
> **Konvensi Autentikasi Rahasia (The Unified Border):** Akses Identitas diamankan total berbasis Enkripsi *Stateless Session Cookie*. Jaringan Mobile Android `(Retrofit)` dipaksa mutlak mencantol perantara _CookieJar_ persisten dalam kanal jalurnya.  
> **Perilaku Sisi-Ganda Server (Hybrid Responses):** Permintaan HTTP (Browser) berekstensi form terproses menumpahkan Pelimpahan (Redirect / DOM View 302). Permintaan Asinkron (*AJAX / OkHttp Mobile*) yang menyisipkan Header Request Target Valid `application/json` dengan setia menumpahkan susunan objek JSON.

---

## 🔑 Akar Pohon Otentikasi Sentral

### `POST /login`
Pintasan gerbang identifikasi Auth tunggal bagi seluruh Kasta Pengguna Ekosistem (Super Administrator hingga Anggota Ujung).

**Header Request (Prasyarat Mobile App)**
- `Accept`: `application/json` (Memerintahkan Laravel mencegat sifat dasar pelimpahan Redirect peramban, memutar paksa respon menjadi Payload Berstatus).

**Tumpuan Form Beban (form-data / url-encoded):**
```text
name     : "JOHN DOE MAHASISWA"        (Karakter Identitas, absolut case-sensitive wajib kapital)
password : "231011403268**"            (Kunci rahasia sandi)
```

**Evaluasi Kembalian Silang Platform:**
- Web Browser sukses `200 OK HTML` melontarkan redirect mutlak rute `GET /dashboard`.
- Modul Mobile (API Mode): Menghisap Sesi Token yang membekas (Encrypted Cookie) yang dilemparkan pada response header `Set-Cookie`.
- Kasus Data Fatal `422 Unprocessable Entity`: Celah *Validation Guard* terlanggar (Sandi retak / NIM Salah eja karakter).

### `POST /logout`
Pemusnahan brutal keping sesi (*Session Death-Spike*) ke pusat awan. Otomatis menghapuskan rekaman akses peramban dari siklus alam semesta aplikasi bersangkutan .

---

## 📱 Saluran Transmisi OneSignal (Jantung Ekstensif Mobile)

Diciptakan secara berdedikasi melayani pertukaran informasi lalu-lintas lintas Sistem Operasi Kotlin.

### `POST /kh/device-token`
*(Endpoint tak terlihat, dieksekusi asinkron seketika saat proses penggambaran halaman Activity Layar Dashboard Android dirakit)*

**Prasyarat Kuki Terhubung:**
- `Cookie: laravel_session=...`

**Muatan Variabel Taktis UUID:**
```text
player_id : "e8f6e72b-8a8c-xxxx-xxxx-xxxxxxxxxxxx"  (Penyandi Unik OneSignal Mobile Subscription ID)
```

**Konfirmasi Tembakan JSON Status 200/201:**
```json
{
  "success": true, 
  "message": "Device token updated. Broadcaster siap menyalurkan Sinyal Tarik Push Latar Belakang Cepat."
}
```
*Catatan Insinyur Utama*: Celah kueri berganda (`Update loop`) disingkirkan lewat pengekangan sinkronisasi kembar (Token identik). Logika menghasilkan respon palsu cerdas `Token Unchanged` agar memori mesin TiDB Cloud Database tak termakan habis dalam sekian Detik.

---

## 💻 Sistem Transisi Hibrida Khusus UI Mobile Murni

### `GET /kh/api/dashboard-data`
API Penghisap lintas entitas *(Model Cross-Join Extractor)*. Menggugurkan kelambatan transisi view dan menggulingkannya ke paket respon padat tunggal. Berfungsi khusus merender RecyclerView UI Android secepat aliran cahaya .

**Batas Kuasa (Role Shield):** Dibuka lebar dengan pagar pelindung Global scope pembatas Tenancy.
**Bongkahan Hasil Konfigurasi Array:**
```json
{
  "students_active": [
    { "id": 14, "name": "BUDI A.", "nim": "234401", "onesignal_is_linked": true }
  ],
  "learning_modules_blob": [
    { "id": 78, "title": "Bahan Uji Enkripsi v2", "type": "file", "download_url": "/kh/module/78/download" }
  ],
  "financial_ledgers": [
    { "id": 105, "type": "income", "amount": 250000, "date": "2026-06-03", "student_name": "RIKA AMIRA" }
  ]
}
```

---

## 🎓 Modul Transaksi Injeksi Administratif (Jadwal & Penugasan Berkas)

### `POST /kh/module` (Transmisi Enkripsi Modul Kuliah Raksasa Base64)
Mentransfer file dalam enkripsi peredam bentuk String Karakter murni Base64 melayang ke TiDB Database.
**Batasan Skala Upload File:** Limitasi Mutlak Maks 4.5 Megabyte (Hukum Limit Batas Memori Kerja Vercel Edge Serverless).

- *Tipe Pengikatan Tautan Link (Referensi Luar)*: Form menembakkan `type="link"`, `link_url="http:.."`.
- *Peleburan Tipe File (Doc/Pdf)*: `type="file"`, `file=[Upload Format Stream Binar]`. (Backend menelannya ke *Logic Array Serialization* dalam nanodetik sebelum menyimpan kolom table).

### `POST /kh/assignment` (Siaran Darurat Tenggat Tugas)
Mencetak misi penugasan mahasiswa. Puncak fungsionalitas Endpoint menyusupkan fungsi `broadcastClass` melantai *(Push Event Signal Lintas Batas OS Aplikasi Ponsel seluruh grup terdaftar Mahasiswa HP Android).*
```json
{
  "subject_name": "Manajemen Sistem (07-TPK)",
  "title": "Tugas Makalah Audit Terpusat",
  "deadline": "2026-10-15 15:30:00",
  "type": "individual"
}
```

---

## 💰 Gerbang Pencatatan Neraca Finansial (Sistem Kunci Bendahara)

### `POST /kh/cash`
**Prasyarat Hak Hierarki (Role RBAC):** Khusus Keturunan Petinggi Kelas `bendahara, ketua, sekretaris`. Mahasiswa akan dibom respons status kode kematian `403 Forbidden` jika nekad menendang rute pelacak uang.
```json
{
  "student_id": 19, 
  "type": "income",
  "amount": 20000,
  "description": "Tagihan Beli Kado Ultah Dosen",
  "transaction_date": "2026-06-03"
}
```

---

## 🗃️ Ekstraksi Streaming Big-Data (Laporan Pencetak 0-RAM API)

Mesin Cetak Kueri Tak Terbatas v1.5 Vercel Stateless (Direct Output Streaming Response - `php://output`).

| Rute URL Mesin Generator | Wujud Transaksi Pembuangan Biner Cetak Tinta Data Akhir |
|:---|:---|
| `/report/pdf/{class_id}` | Biner Aliran *Download Dokumen PDF Resolusi Statis Formal* (Enjin perender DomPDF) |
| `/report/excel/{class_id}` | Tumpahan aliran Streaming Skala raksasa ribuan Baris Entri Neraca Saldo CSV Data Asli |
| `/kh/reports/attendance/excel` | Tabel Ekspor Sisa Nyawa dan Daftar Hadir Kelas (CSV) |

---

## 💀 Engine Pengetesan Khusus Simulasi Ops (Tes Stres Limit Berkelanjutan)

### `GET /simulasi`
Jalur bypass demo pengetesan mesin awan tiada henti mendemonstrasikan kelincahan kueri kustom (Bukan Model ORM Biasa). Melontarkan 5 paket jadwal ganda secara paralel per detikan nafas mengukur kedigdayaan respons TiDB AWS Cluster.

### `GET /test-full`
Jalur pembuktian (E2E Integration Loop Check). Mencoba menembak, mendeteksi kelambatan tugas buatan, memvalidasi dan memvonis bunuh nyawa ALFA pada subjek kelas, dan mengakhiri sesi dalam sekian waktu rekaman log pelacakan milisecond performa server Vercel Edge Limits.

*(Pedomani rujukan balasan Kesalahan Tipe 422 Validasi, 403 Perampasan RBAC, dan Kematian 504 Beban Timeout Eksekusi PHP di Rute-Rute Berisko Tinggi Transaksi Besar di atas)*.
*(Terikat Perjanjian Evolusi Historikal Modifikasi 2024-2026).*
