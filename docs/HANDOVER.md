# Dokumen Serah Terima Operasional (Operational Handover): KelasHUB

**Proyek**: KelasHUB — All-in-One Class Operations Suite  
**Diserahkan Oleh**: Ariyas Pratama Ramadhan By WaveProject.ID  
**Penerima**: Tim Operasional / Pengelola Kelas  
**Versi**: 2.3.0  
**Tanggal**: 30 Mei 2026

---

## 1. Pendahuluan
Dokumen ini menandai penyerahan resmi sistem **KelasHUB v2.3.0** dari tim pengembangan (WaveProject.ID) ke tim operasional. Versi ini mencakup fitur push notification real-time via OneSignal dan aplikasi Android native berbasis Kotlin.

---

## 2. Inventaris Sistem

### 2.1 Komponen Teknis
| Komponen | Teknologi | Versi |
|:---|:---|:---|
| Backend | Laravel (PHP) | 13.x / PHP 8.3 |
| Frontend | Tailwind CSS + Alpine.js | v4 / 3.x |
| Database | TiDB Cloud (MySQL-compat) | — |
| Hosting | Vercel Serverless | Free Tier |
| Mobile App | Kotlin Android Native | API 24+ |
| Push Notification | OneSignal SDK + REST API | v5.x / v2 |

### 2.2 Inventaris Artefak Dokumentasi
| Dokumen | Lokasi |
|:---|:---|
| PRD | `docs/PRD.md` |
| Panduan Teknis | `docs/TECHNICAL.md` |
| API Reference | `docs/API.md` |
| User Guide | `docs/USER_GUIDE.md` |
| Laporan Penutupan | `docs/CLOSURE_REPORT.md` |
| Changelog | `CHANGELOG.md` |

---

## 3. Manajemen Akses & Kredensial

> [!IMPORTANT]
> Seluruh kredensial sensitif dikelola melalui **Vercel Environment Variables**. Jangan pernah hardcode credential di kode sumber.

| Item | Deskripsi | Lokasi Pengaturan |
|:---|:---|:---|
| **Database** | Host, User, Pass, Port TiDB Cloud | Vercel Env |
| **SSL CA** | `MYSQL_ATTR_SSL_CA` | `/var/task/cacert.pem` |
| **App Key** | Laravel Encryption Key (`APP_KEY`) | Vercel Env |
| **OneSignal App ID** | `ONESIGNAL_APP_ID` | Vercel Env |
| **OneSignal REST Key** | `ONESIGNAL_REST_API_KEY` | Vercel Env |
| **Super Admin** | Akses tertinggi | DB `students` (Role: super_admin) |
| **OneSignal Dashboard** | Manajemen aplikasi push | `onesignal.com` |

---

## 4. Prosedur Operasional Rutin

### 4.1 Deployment (CI/CD)
- Setiap *push* ke branch `main` pada GitHub akan otomatis memicu build dan deploy ke Vercel.
- **Verifikasi**: Periksa tab "Deployments" di Vercel Dashboard jika terjadi kegagalan build.

### 4.2 Distribusi APK Android
1. Build APK terbaru via Android Studio: **Build → Build Bundle(s)/APK(s) → Build APK(s)**.
2. File output di: `android-webview/app/build/outputs/apk/debug/app-debug.apk`
3. Distribusikan file APK ke mahasiswa melalui grup kelas.
4. Mahasiswa perlu mengizinkan "Install dari sumber tidak dikenal" di pengaturan HP.

### 4.3 Eskalasi Masalah
Jika sistem mengalami error:
1. Cek log di Vercel Dashboard → Logs.
2. Cek kuota dan status database di TiDB Cloud.
3. Jika notifikasi tidak terkirim: Pastikan `ONESIGNAL_APP_ID` dan `ONESIGNAL_REST_API_KEY` benar di Vercel Env.
4. Jika token tidak terdaftar: Mahasiswa perlu logout → login ulang di aplikasi Android.

---

## 5. Daftar Periksa Serah Terima (Handover Checklist)

| No | Item Peninjauan | Status |
|:---:|:---|:---:|
| 1 | Source code telah diperbarui di repositori utama. | [ ] |
| 2 | Environment Variables (termasuk OneSignal) telah dikonfigurasi di Vercel. | [ ] |
| 3 | Database sudah dimigrasi (`add_onesignal_id_to_students`). | [ ] |
| 4 | APK Android v2.3.0 telah didistribusikan ke anggota kelas. | [ ] |
| 5 | User Guide telah disosialisasikan ke seluruh anggota. | [ ] |
| 6 | Kredensial Super Admin diserahkan secara privat. | [ ] |
| 7 | Laporan closure dan rencana backup telah disetujui. | [ ] |

---

## 6. Persetujuan
Dengan menandatangani dokumen ini, kedua belah pihak menyepakati bahwa sistem telah sesuai spesifikasi dan siap dioperasikan secara penuh.

**Pihak Pengembang**  
*(Ariyas Pratama Ramadhan)*  
**WaveProject.ID**

**Pihak Penerima**  
*(Ketua Kelas / Admin Operasional)*
