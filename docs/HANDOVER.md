# Dokumen Serah Terima Operasional (Operational Handover): KelasHUB

**Proyek**: KelasHUB — All-in-One Class Operations Suite  
**Diserahkan Oleh**: Ariyas Pratama Ramadhan By WaveProject.ID  
**Penerima**: Tim Operasional / Pengelola Kelas  
**Tanggal**: 27 Mei 2026

---

## 1. Pendahuluan
Dokumen ini menandai penyerahan resmi sistem **KelasHUB** dari tim pengembangan (WaveProject.ID) ke tim operasional. Tujuannya adalah untuk memastikan keberlangsungan operasional sistem tanpa hambatan teknis.

---

## 2. Inventaris Sistem

### 2.1 Komponen Teknis
- **Source Code**: Laravel 13 (PHP 8.3) Monolithic Structure.
- **Frontend**: Tailwind CSS v4, Alpine.js (Reactive Dashboard).
- **Mobile**: Kotlin Wrapper (Android WebView).
- **Infrastructure**: Vercel (Hosting), TiDB Cloud (MySQL Database).

### 2.2 Inventaris Artefak Dokumentasi
- [PRD (Standard Industri)](file:///C:/Users/Admin/.gemini/antigravity/brain/ba02e238-eee1-4f61-9086-6997358eae00/product_requirement_document.md)
- [Panduan Teknis (Arsitektur)](file:///C:/Users/Admin/.gemini/antigravity/brain/ba02e238-eee1-4f61-9086-6997358eae00/technical_documentation.md)
- [Panduan Pengguna (User Guide)](file:///C:/Users/Admin/.gemini/antigravity/brain/ba02e238-eee1-4f61-9086-6997358eae00/user_guide.md)
- [Laporan Penutupan & Pemeliharaan](file:///C:/Users/Admin/.gemini/antigravity/brain/ba02e238-eee1-4f61-9086-6997358eae00/project_closure_report.md)

---

## 3. Manajemen Akses & Kredensial

> [!IMPORTANT]
> Seluruh kredensial sensitif dikelola melalui Vercel Environment Variables.

| Item | Deskripsi | Lokasi Pengaturan |
|:---|:---|:---|
| **Database** | Host, User, Pass, Port | Vercel Env (TiDB Cloud) |
| **SSL CA** | `MYSQL_ATTR_SSL_CA` | `/var/task/cacert.pem` |
| **App Key** | Encryption Key | `APP_KEY` (Laravel) |
| **Super Admin** | Akses Tertinggi | Database Table `students` (Role: super_admin) |

---

## 4. Prosedur Operasional Rutin

### 4.1 Deployment (CI/CD)
- Setiap *push* ke branch `main` pada repositori GitHub yang terhubung akan secara otomatis memicu build dan deploy ke platform Vercel.
- **Verifikasi**: Periksa tab "Deployments" di dashboard Vercel jika terjadi kegagalan build.

### 4.2 Eskalasi Masalah
Jika sistem mengalami error (misal: 504 Gateway Timeout):
1. Cek kuota database di TiDB Cloud.
2. Periksa log Laravel melalui `vercel logs`.
3. Jika masalah berlanjut pada logika "Sisa Nyawa", hubungi pengembang utama (WaveProject.ID).

---

## 5. Daftar Periksa Serah Terima (Handover Checklist)

| No | Item Peninjauan | Status |
|:---:|:---|:---:|
| 1 | Seluruh source code telah diperbarui di repositori utama. | [ ] |
| 2 | Variabel lingkungan (Environment Variables) telah dikonfigurasi. | [ ] |
| 3 | Panduan Pengguna (User Guide) telah disosialisasikan. | [ ] |
| 4 | Kredensial Super Admin telah diserahkan secara privat. | [ ] |
| 5 | Laporan maintenance dan backup database telah disetujui. | [ ] |

---

## 6. Persetujuan
Dengan menandatangani dokumen ini (secara digital/fisik), kedua belah pihak menyepakati bahwa sistem telah sesuai dengan spesifikasi dan siap untuk dioperasikan secara penuh.

**Pihak Pengembang**  
*(Ariyas Pratama Ramadhan)*  
**WaveProject.ID**

**Pihak Penerima**  
*(Ketua Kelas / Admin Operasional)*
