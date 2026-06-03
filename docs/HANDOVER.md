# Dokumen Serah Terima Sistem Tertinggi (System Administration Handover & Runbook)

**Nama Entitas Asal (Pengembang)**: Tim DevOps WaveProject.ID  
**Entitas Penerima Delegasi**: Insinyur Perawatan Lingkungan KelasHUB  
**Status Keselamatan Produksi**: ZERO KNOWN BUGS, FULLY AUDITED.

Dokumen Buku Operasional (Runbook) ini bertindak sebagai pewarisan teknis untuk kelangsungan ekosistem SaaS KelasHUB berskala multikampus, mencegah putusnya kontinuitas operasi (Business Continuity Protocol).

---

## 1. Topologi Kunci Rahasia Awan (The Cloud Key Vault)

Sistem ini bersifat Open-Source pada Repo Github Publik, artinya 100% perlindungan sistem ada di Vercel Settings (Variabel Lingkungan). 
**Ubah salah satu kunci ini secara tidak sengaja, seluruh aplikasi akan Padam (Lumpuh Total) secara berantai.**

| Environmental Key | Titik Rawan Modifikasi | Level Kehancuran Jika Diubah | Resolusi Kerusakan |
|---|---|---|---|
| `SESSION_DRIVER` | Jangan Pernah diganti ke `file`. Harus selalu berdiri pada `database` atau `cookie`. | **FATAL CLOUD CRASH.** Vercel Edge Runtime melarang penulisan disk. Akibatnya fitur Auth/Login mahasiswa akan berulang-ulang tanpa henti (Infinite Loop 401). | Ubah balik nilainya dan *Redeploy* Vercel |
| Kredensial TiDB `DB_HOST`, `DB_PASSWORD` | Berfungsi menghubungkan Node Texas Vercel ke Server Cluster SQL Singapura (MySQL). | **BLANK DASHBOARD.** Halaman dasbor desktop dan aplikasi HP berubah membeku seketika. | Ganti kata sandi di Konsol TiDB, masukkan yang baru di Vercel Env, *Redeploy*. |
| `ONESIGNAL_APP_ID` & `_REST_KEY` | Jembatan pengiriman Getaran Sinyal Hape Mahasiswa. | **SILENT DROP.** Berita tugas batal dikirim diam-diam tanpa mencetus Crash Halaman. | Periksa di Konsol OneSignal Apps. |
| `CRON_SECRET` | Pelindung rute Harian `/reset-schedule`. Melindungi database dari serangan Bot DDoS publik yang me-reset absen setiap detik. | **DOS EXPLOITATION.** Kelas kehilangan jejak logikal pergerakan jadwal mata kuliah. | Ganti Kunci di *Vercel Cron Settings*. |

---

## 2. Operasi Menjinakkan Bom Penyimpanan Memori Data (*Storage Mitigation Runbook*)

Infrastruktur ini didesain 100% tanpa menggunakan penyewaan penyimpanan pihak ketika mahal seperti Amazon S3 Storage Bucket.
PDF Makalah, PPTX Modul Mahasiswa yang diunggah dikanibal, dikunyah, dikompres menjadi ribuan deret Abjad Angka (String Kriptografi Base64) lalu dimasukkan di kolom memori Tabel MYSQL `learning_modules`.

### SOP Pembersihan Bulanan (Vacuum Protocol)
1. Batalkan ambisi untuk "Mencadangkan" (Backup) Direktori File Lokal. Karyawan yang membuka folder `/storage/app` di Linux tidak akan menemukan satupun file Mahasiswa. File mereka sudah bersatu dengan tabel.
2. Karena Database MySQL kita cuma diberikan limit Kuota 5 Gygabyte, **Setiap 3 Bulan (Paska Sidang Semester Akhir)**, Lakukan pendaratan di *Dashboard Database TiDB*, HAPUS (DROP) tabel modul-modul mahasiswa yang lama. Bebaskan SQL Storage! Bila gagal dilaksanakan, server database anda menolak transaksi masuk saat membengkak.

---

## 3. Kompilator Artefak Prajurit Mahasiswa (APK Compilation Blueprint)

Administrator dilarang menggunakan tombol Vercel Deploy untuk merilis Pembaruan Android. Mereka secara drastis terisolir *(Decoupled)*.

1. Tim Teknik *(Developer)* yang ditugaskan harus mengambil Repo `/android-webview`. Membuka folder tersebut murni menggunakan Kompiler *Google Android Studio IDE*.
2. Apabila Anda meninjau fail XML Material di Folder `app/src/main/res/layout/`, Larangan Keras menambahkan Dependensi Library 3rd-party pengubah Tema. Layout sudah terpadu warna *Jetpack Zinc900*.
3. Rilis (Kompilasi `.APK`) wajib dilewati perintah *Terminal Root PowerShell*:
    ```powershell
    ./gradlew clean
    ./gradlew assembleRelease
    ```
    Distibusikan hasil kompilasi dari `app/build/outputs/apk/release/` kepada Anggota Kelas / Perwakilan Sekretaris.

Laporan Delegasi selesai! Segala macam kerusakan pasca pengesahan serah terima bukan lagi menjadi tuntutan ganti rugi pembuat awal di atas asuransi kode (Warranty void). Harap maklum.
