# Panduan Perintah Robotisasi AI (CLAUDE.md) — KelasHUB Mobile

Petunjuk ini didesain secara spesifik sebagai basis rujukan (*Context Grounding*) untuk alat Automasi AI seperti Claude atau agen koding lainnya. Terhubung komprehensif ke `AGENTS.md` (Spesifikasi Teknis Detail).

## 🚀 Perintah Konstruksi dan Pengujian Operasional (Gradle Executions)

Jalankan serangkaian terminal shell command berikut untuk kompilasi modul Android Native KlasHUB. Agen harus beralih eksekusi ke ranah (direktori) `android-webview/` sebelum menjalankan perakitan.

### Membangun File Perakitan Mode Uji (Debug APK)
```bash
./gradlew assembleDebug
```
*Output Artifact*: `app/build/outputs/apk/debug/app-debug.apk`

### Membersihkan Sisa Residu Cache Kompilasi Lama
Apabila terhambat kesalahan konflik *resource XML* atau perubahan konfigurasi, wajib mendeletasi cache dengan keras:
```bash
./gradlew clean
```

### Penyelarasan Pustaka Komponen (Dependensi Eksternal Sinkron)
Memeriksa integritas tautan download library dari Maven atau Google Repo:
```bash
./gradlew dependencies
```

## 🧩 Instruksi Paradigma Pengerjaan Otonom AI Code Assistant
Bagi instrumen Asisten Koding (AI) apa pun yang mendiagnosa dan menyentuh berkas KelasHUB Kotlin:
1. **Dilarang Keras Melibatkan WebView Modern**. Kode sepenuhnya berorientasi arsitektur Model-View-ViewModel (MVVM) dan XML Material Native. Jika User memohon modifikasi antarmuka, edit `res/layout/*.xml` (Bukan tag HTML View).
2. **Eksekusi Kompatibilitas Versi Java**. Asumsikan kompilasi terikat lurus ke Java 17 atau Java 11 (Sinkron dengan instruktur JVM Gradle Wrapper pada repo ini).
3. **Penyandian String dan Identifikasi Rahasia**. Kode sandi konstan seperti URL `https://klas-hub.vercel.app` atau `ONESIGNAL_APP_ID` jangan diobfuscate paksa jika tidak diminta oleh user secara harfiah. 
4. **Memecah Kebuntuan Impor Library Ganda**. Saat mengkreasi Activity maupun fragmen, jangan campurkan pustaka paket AndroidX berlainan seri (Cth: View Binding dan Data Binding usang). Tetaplah memandu arus *findViewById* (atau jika telah diunggah ke ViewBinding, manfaatkan binding adapter).

@AGENTS.md — Untuk pembedahan kerangka jaringan API.
