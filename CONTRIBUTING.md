# Panduan Kontribusi Teknikal Terbuka (Enterprise Contributing Guidelines)

Terima kasih atas dedikasi Anda berkontribusi pada repositori inti **KelasHUB**! Panduan SOP ini dirancang untuk menyetarakan paradigma di mana setiap baris kode, arsitektur, maupun penambahan fitur dapat berintegrasi secara presisi dan nir-gesekan (*seamless/friction-less*) pada kerangka hibrida Vercel-TiDB-Android kami.

Anda berkewajiban menyepakati keseluruhan pakta dokumentasi ini sebelum mengeluarkan Pengajuan (*Pull Request / PR*). Menerobos batas akan berpotensi pada penolakan *Force-Closed* automatis dari Maintainer Utama.

---

## 1. Etika Kode Kolaboratif (Code of Conduct)
Proyek ini mengakar pada fondasi solidaritas akademis dengan *deployment* berstandar produksi berat.
- Dilarang absolut memberikan komentar pasif-agresif atau menyinggung individu saat peninjauan kode (*Code Review*).
- **Protokol Celah Retas 0-Day (Zero-Day Exploits):** Anda TIDAK DIPERBOLEHKAN mendaftarkan kebuntuan fatal (Bug) Kritis seperti *Bypass Multi-Tenant IDOR* melalui laman Publik *Issue Tracker*. Penemuan celah keamanan **WAJIB** dilaporkan secara diam (pribadi/Direct Message) ke administrator untuk *Hot-fixing* 1x24 jam.

---

## 2. Paradigma Eksekusi Percabangan (Git Flow / Branching Logic)

Tim *DevOps* KelasHUB mendobrak pergerakan kacau lewat *Branch Isolation Policy*.

### A. Konvensi Nomenklatur Ranting (Feature Branch Policy)
Mendorong (*Pushing*) secara langsung pada urat nadi lingkungan produksi (`main`) adalah tindakan tabu. Bungkus pekerjaan Anda di dalam cabang dengan awalan:

* **Pembangunan Baru (Feature)**: `feat/nama-skenario-spesifik` (Contoh: `feat/android-qris-native`)
* **Pemadaman Darurat (Bugfix)**: `fix/insiden-bertabrakan` (Contoh: `fix/infinite-loop-eloquent`)
* **Penyusunan Ulang Mesin (Refactoring)**: `refactor/nama-modul-perapihan`
* **Dokumentator (Docs)**: `docs/nama-dokumen-perubahan`

### B. Kewajiban Leksikal Pesan Sejarah (Conventional Commits v1.0)
Semua narasi sejarah riwayat *commit* diregistrasikan menggunakan [Conventional Commits v1.0.0](https://www.conventionalcommits.org/en/v1.0.0/). Pesan yang buram dan asal akan membuat PR Anda dipukul mundur.

**Sintaks yang Benar**:
> `feat(android): suntikan sinkronisasi coroutine token push onesignal background`
> `fix(database): perbaikan transmisi error 500 saat ekspor json cash_ledgers`

**Sintaks yang Salah**:
> `Fix bug lol`
> `Update tampilan web kmrn yang eror.`

---

## 3. Empat Pakta Hukum Arsitektur Ekstrem (The System Guardrails)

Arsitektur kami adalah keajaiban komputasi bertahan *(Stateless Edge Survivability)*. Memori Vercel Free-Tier (128MB RAM) menetapkan **Larangan Mutlak (Anti-Patterns)**:

### 🚫 Aturan #1: Mautnya Eksekusi Piringan Lokal (Disk Write Constraint)
Anda DILARANG memanfaatkan pemanggilan PHP seperti `Storage::disk('local')->put()`, `fopen()` menulis ke Log, atau menyimpan gambar `public/images/`. 
Lapisan Vercel Node akan tertidur 10 detik sesudah merespons. Bangun tidur, fail Anda disapu ludes rata *(Wiped Out Return 404)*. 
> **Pecahkan Permasalahan (Solusi)**: Modul-modul tugas harus dikonversi *(Encoded)* menjadi tipe biner tekstual (String *Base64*) dan dimasukkan di kolom database MySQL `LONGTEXT`.

### 🚫 Aturan #2: Penyedotan RAM Ekspor Fatal (Out-Of-Memory Mass-Data Extraction)
ORM Eloquent `CashLedger::all()` akan menarik belasan ribu mutasi uang. Hal tersebut membuat memori *Vercel PHP Thread* menabrak batas hancur 128MB.
> **Pecahkan Permasalahan (Solusi)**: Apabila Anda menyentuh fitur Pengolahan Data Laporan Massal (CSV/Ekspor PDF), paksa rute menggunakan *Chunking DB Lazy stream* `DB::table()->lazy()` dan dikirim bertahap ke `php://output` *(Streamed Chunk CSV to browser Response)*. 

### 🚫 Aturan #3: Tangan Berdarah Lintas-Kelas (The IDOR Tenant Barrier)
Database kita membawahi banyak kampus dan kelas. Intervensi Model *Eloquent* tanpa tameng penjamin Otoritas Kelas (*Class Identity Check*) sama dengan meruntuhkan sekuritas. 
**Selalu dan pastikan** Model (`CashLedger`, `Assignment`, dsb) diikat Trait `BelongsToClass`. Pemanggil *Query Database* murni `DB::table()` wajib dijejali `.where('class_id', $auth->class_id)` di depan.

### 🚫 Aturan #4: Kemerdekaan Front-End UI
* **Dasbor Pandangan PC/Web**: Tidak dibolehkan memohon ketersediaan dependensi pihak luar seperti *jQuery*. Desain kami memurnikan 100% tumpuan perilaku statis dari keajaiban *Alpine.js* (`x-data`, `x-show`, `x-transition`) untuk transisi tanpa muat. Pola cat warnanya berdiri mutlak dari bawaan `Tailwind CSS V4`.
* **Klien Aplikasi Cerdas Android**: Kode Kotlin dikemudikan asinkron oleh coroutine `suspend`. Pemasok Jaringan diarsiteki penuh oleh *Retrofit2* + okHttp3. Kematian Token Sesi Web (Cookie `laravel_session`) dicegah menggunakan metode khusus di dalam Singleton pengawal kuki `cookieJar`.

---

## 4. Templat Wajib Pull Request (Pre-Flight Ceklis PR)

Copy paste lembar persetujuan (*Template*) ini ketika Submit *Pull Request*:

```markdown
### Rangkuman Insiden Proposal 
[Jelaskan mengapa PR ini hadir dan urgensinya (Contoh: Menghancurkan Bug Sinkron 500 Memory Limit saat Vercel Mengekspor File)]

### Ceklis Penerbangan Teknis:
- [ ] Saya telah membersihkan debu sisa kode tak berguna (Lint / Formatter PSR-12).
- [ ] Fitur Baru tidak memutus konektivitas integrasi lintas Vercel 10-Second Time-Limit.
- [ ] (Bila Area Web) Komponen Interaktif diprogram menggunakan metode murni (Vanilla/Alpine.js).
- [ ] (Bila Area Android Kotlin) Menjalankan terminal kompilasi `./gradlew assembleRelease` sukses tanpa macet di lokal.
- [ ] Validasi keamanan Siber (Filter Tenant Global Scope) telah diuji coba.

### Bukti Validasi Tangkapan Layar (Opsional):
[Seret Image/Log Exception Terminal Kesini]
```
