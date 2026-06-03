# Panduan Arsitektur & Kontribusi Terbuka (Contributing Guide) — KelasHUB

Selamat bergabung ke dalam garis pertempuran Repositori **KelasHUB**! 🎉  
Jika Anda berada di sini, Anda siap menyentuh titik-titik krusial ekosistem sistem yang secara radikal berevolusi: **Dari Platform Website Monolitik Biasa (v1.0), Bermutasi Menjadi Gerbang Hybrid API, dan Meluber Lewat Native Android Client (v2.3)**.  
Keunikan hibrida aplikasi ini menuntut Anda, sang Insinyur, menaati kompilasi standar eksekusi ekstrem agar Web Render (Alpine.js Dashboard) dan Konsumsi Layar API Mobil (Retrofit) di Cloud Vercel tidak mengalami tabrakan fatal (Crash).

---

## 📋 Tata Index Kontribusi Teknis
- [Etika Kolaborasi Sosial Jaringan](#-etika-kolaborasi-sosial-jaringan)
- [Algoritma Pelaporan Insiden Bersejarah (Bug & Celah)](#-algoritma-pelaporan-insiden-bersejarah-bug--celah)
- [Protokol Validasi Proposal Arsitektur Baru](#-protokol-validasi-proposal-arsitektur-baru)
- [Alur Version Control & Percabangan Fitur](#-alur-version-control--percabangan-fitur)
- [Manifesto Penulisan Kode Beririsan Lintas-Dunia (Web vs Mobile)](#-manifesto-penulisan-kode-beririsan-lintas-dunia-web-vs-mobile)
  - [Aturan Perilaku Edge RAM Vercel (PHP)](#aturan-perilaku-edge-ram-vercel-php)
  - [Doktrin Pertahanan Aplikasi Siber](#doktrin-pertahanan-aplikasi-siber)
  - [Tata Krama Web DOM Renderer (Blade & Alpine.js)](#tata-krama-web-dom-renderer-blade--alpinejs)
  - [Standar Operasional Robot Android (Kotlin)](#standar-operasional-robot-android-kotlin)
- [Kewajiban Tata Bahasa Commit History](#-kewajiban-tata-bahasa-commit-history)

---

## 🤝 Etika Kolaborasi Sosial Jaringan

Proyek perangkat lunak kampus edukasi berskala open-source ini adalah tempat peleburan ide. Dilarang meremehkan usulan atau kode pengembang muda. Hantaman verbal merendahkan, diskriminasi pada isu pelaporan kerentanan (blame culture), akan berakibat pada putusan penghapusan (*Ban*) tanpa musyawarah. Selalu tinggalkan komentar pada kode warisan (Legacy code).

---

## 🐛 Algoritma Pelaporan Insiden Bersejarah (Bug & Celah)

Jika Anda melihat kebobolan Keamanan Multi-Tenant (Seumpama: IDOR akses Mahasiswa Kelas C sukses masuk meretas absen teman Kelas A via injeksi form Request), segera lempar laporan *Direct Vulnerability Report* Rahasia! 

Untuk Cacat Sistem (Bug) Web Front-End / Parsing Data Error di Mobile Android:
1. Yakinkan Diri: Pastikan riwayat pencarian Issue di Repo tidak menduplikasi masalah *(No Copy-Cat Reports)*.
2. Buat Issue berstruktur dengan melampirkan kronologi dari 2 Sisi Dunia sistem ini:

```markdown
**Vonis Gangguan:** (Cth: Gagal memuat Modul File dari Kotlin Retrofit, tapi Web Unduh Berhasil)

**Konteks Sejarah & Analitik Bug:**
[Jelaskan apakah ini bug warisan pasca migrasi web API JSON atau rute terputus]

**Aksi Percobaan Ulang (Repro):**
1. Eksekusi Login Via Emulator Kotlin di Role 'Mahasiswa'.
2. Sisi Web Administrator mencoba menekan tombol Upload tipe file (Base64 Injection).
3. Modul melempar kembalian Json `Null` pada HP target!

**Reaksi Alam Bawah (Kenyataan & Ekspektasi):**
Web Browser mencetak `HTTP 200 OK` HTML File, tetapi Android mem-parse Syntax Exception. (Harap JSON turun dengan kolom URL Base64 yang Valid).

**Jejaring Tangkapan Layar (Network SS):**
[Buktikan lewat Console Chrome Inspect Element atau Logcat Android Studio]
```

---

## 💡 Protokol Validasi Proposal Arsitektur Baru

JANGAN langsung menciptakan PULL REQUEST bila Anda bersikeras merombak jantung database sistem, menaikkan librari pihak-ketiga besar (Bloatware), atau mengubah letak skenario arsitektur Monolitik menjadi Microservice liar.
1. Bukalah gelanggang persetujuan via *Github Discussion / Request For Comments (RFC)*. 
2. Buktikan argumen bahwa integrasi ide Anda (mis. Penyimpanan Beralih Munggunakan Cloud Object Storage S3, daripada Base64 string Database eksisting KelasHUB) itu hemat anggaran RAM.
3. Setelah palu Maintainer diketuk Hijau, baru buka branch `Enhancement`.

---

## 🔄 Alur Version Control & Percabangan Fitur

Ikuti skenario percabangan fitrah *Feature Toggling Isolator*: 

```bash
# 1. Bajak Repositori secara Kloning Forking
git clone https://github.com/[AKUN_ANDA]/KlasHUB.git

# 2. Pantau aliran induk pembaruan tim
git remote add upstream https://github.com/gmailtesid-stack/KlasHUB.git

# 3. Kuras perbaruan dari nadi Induk (Produksi Puncak Main)
git checkout main
git pull upstream main

# 4. Kredensial Isolasi Pekerjaan: Percabangan Tersendiri
git checkout -b feat/redesain-web-alpine-navigation

# 5. --- TULIS MANUVER KODE ANDA TANPA MENGGANGGU FUNGSI LAIN ---

# 6. Susun sejarah Commit secara Presisi Konvensional
git add .
git commit -m "feat(web-ui): perombakan layout animasi navigasi sisi Dashboard Alpine"

# 7. Unggah isolasi ke langit Fork Anda
git push origin feat/redesain-web-alpine-navigation

# 8. Lepaskan permintaan (Pull Request)
```

---

## 📝 Manifesto Penulisan Kode Beririsan Lintas-Dunia (Web vs Mobile)

Kehebatan KelasHUB hari ini adalah menahan beban menjadi **Website Blade Engine Render Sekaligus Restful API Provider** bersamaan pada lingkungan komputasi tak bernyawa *(Stateless Edge)* Vercel. 

### 🛡️ Aturan Perilaku Edge RAM Vercel (PHP)
Jangan tertipu! Mesin Vercel Edge Serverless itu miskin RAM (128mb - 256mb) dan rakus Limit Siklus **<10 Detik Timeout**.
- Jauhi pemakaian fitur Database ORM Eloquent di iterasi luar biasa tinggi (`Collection->map()` pada ratusan tagihan kas). Bergantunglah pada mesin asali *Native Data Query* `DB::table(...)`.
- Larangan Ekstrim *File Write Constraint*: Jangan paksa direktori `/logs` atau `Storage/app/public` untuk merekam sesi/upload file lokal! Anda wajib menjatuhkan (export) file lewat *Streaming `php://output`* (Seperti di Reporting Laporan Kas CSV PDF) atau memutilasi Berkas di-*encoding (Base64 Database)*. Modifikasi Sesi Sistem dipaksa mati-matian menggunakan tipe pertahanan `Encrypted SESSION_COOKIE_DRIVER`. 

### 🔐 Doktrin Pertahanan Aplikasi Siber
- Lapis Multidimensi Multi-Tenant: Setiap barisan kueri pengais Data dari Database **TIDAK BOLEH** hanya diam tanpa Scope. Apabila anda memakai Raw Kueri, *WAJIB INJEKSI* filter penangkis `->where('class_id', $classId)`. Ini nyawa bagi privasi laporan antar Universitas/Kelas terisolasi! 
- Proteksi Filter Silang Request: Lindungi rute rawan dengan Middleware Tameng `CheckRoleKelasHub.php`.

### 🎨 Tata Krama Web DOM Renderer (Blade & Alpine.js)
Evolusi Front-End Desktop/Web kami bukanlah era reaktif murni (Bukan NextJS/Vue/React). Ini perpaduan brutal Monolit Blade Engine bertemu reaktivitas asinkron Alpine.js:
- Semua model tombol dinamis (Modal Popup Tambah Absen, Animasi Hover) hidup lewat perintah atribut hantu (Directive) `x-data`, `x-show`, `x-transition`. JANGAN mengotori file menggunakan ekstensi `document.getElementById` panjang layaknya pemrograman jQuery tahun 2011.
- Lukisan Visual Website dikanvaskan memanggil properti kelas *TailwindCSS V4* murni (Zinc-900 Slate Palette).

### 📱 Standar Operasional Robot Android (Kotlin)
Kode mobile Kotlin kita berdiri merdeka. Panggil API jembatan Web lewat terowongan **Retrofit + OkHttp**. 
- Kunci Cookie Sistem Autentikasi web Vercel mesti diisap erat-erat kedalam _Persistent Cookie Jar Object_ di `ApiClient.kt`. Hilangnya cookie ini memicu Android OS diblokir tendangan `HTTP 401 Unauthorized` terus menerus dari Web Engine!
- *Worship the Token*: Lapis kode `MainActivity` ditugaskan spesifik untuk menembakkan (push-trigger) Token *OneSignal Identifier HP (Player UUID)* kapan saja App dirasa bangkit dari tidur, melempar data ke rute API `POST /kh/device-token`. (Jangan sampai User HP melewatkan bel getar penagihan kas!).

---

## 📌 Kewajiban Tata Bahasa Commit History

Untuk proyek bersejarah dengan migrasi sebesar ini, konvensi pesan (Commit Lexicon) adalah kompas pemandu *Debugging* tim lain.

Tipe Struktur Baku: `<jenis_aksi>(<modul/area>): <Deskripsi Pukulan Telak... (Tanpa Tutup Titik)>`

| Awalan Leksikal (Tipe) | Kapan Senjata ini Ditarik? |
|:---|:---|
| `feat` / `fitur` | Mencetak Sejarah Fungsi Baru! (Web Route Baru, Native Screen Activity). |
| `fix` / `bugfix` | Menghembuskan nafas menyadarkan blokir error 500 Crash Pengecualian Sistem (Patch). |
| `refactor` / `renovasi`| Pemolesan keindahan memori mesin (Logic Optimization) tanpa mengubah efek DOM UI layar. |
| `docs` | Menitikberatkan dokumentasi rahasia (*Manual Koding, API Postman Docs*, komentar). |
| `sec` | Injeksi penguatan zirah Siber. Detoksifikasi parameter URL peretas, Injeksi *X-Frame Headers*. |
| `perf` | Rekayasa mesin peras SQL demi efisiensi detik (Dari 12 Detik Timeout ➞ 0.4 Detik Kilat). |

**Pengeksekusian Elegan:**
> `refactor(web-report): menggeser mesin Eloquent All ke arah DB::table Streaming menekan I/O Memory RAM Serverless`  
> `feat(android-sync): koroutin penangkapan pembaruan token Player OneSignal` 

---

## 🧪 Ceklis Pra-Pull Request (Validasi Terakhir)

- [ ] Tidak ada penulisan skrip simpan disk Server Vercel statis (Semuanya Base64 Data / Cookies)! 
- [ ] Pemeriksaan Otentikasi Lapis Siber: Permintaan DB disuntik `$class_id` Filter Global Tenant.
- [ ] Web Render: Modal interaksi menggunakan eksekusi `x-show` Alpine.js bukan vanilla JS document-query. 
- [ ] Mesin SDK Terminal Android `gradlew app:assembleDebug` dapat mencetak APK tanpa kegagalan Gradle Sync.
- [ ] Standarisasi Rapih Format PHP MVC divalidasi ketaatan spasi lewat skrip `./vendor/bin/pint`.
