# Panduan Teknis (Technical Documentation): KelasHUB

**Proyek**: KelasHUB — All-in-One Class Operations Suite  
**Teknologi**: Laravel 13, TiDB Cloud, Vercel Serverless  
**Bahasa**: Indonesia

---

## 1. Arsitektur Sistem & Stack

KelasHUB dirancang dengan arsitektur **Monolitik Serverless**. Seluruh backend berjalan sebagai fungsi stateless di Vercel, sementara data disimpan di cluster terdistribusi TiDB Cloud.

### Stack Utama:
- **Backend**: Laravel 13.x (PHP 8.3+)
- **Database**: TiDB Cloud (Distributed MySQL-Compatible)
- **Frontend**: Tailwind CSS v4 + Alpine.js
- **Runtime**: Vercel Serverless Functions (`vercel-php`)
- **Mobile**: Kotlin Android WebView

---

## 2. Struktur Kode & Organisasi

Aplikasi mengikuti pola MVC Laravel standar dengan penambahan beberapa komponen kunci untuk menangani *Multi-tenancy* dan *Serverless constraints*.

### Folder Utama:
- `app/Http/Controllers/`: Logika bisnis utama (Engine, Laporan, Simulasi).
- `app/Models/`: Definisi entitas data.
- `app/Traits/`: Trait `BelongsToClass` untuk isolasi data otomatis.
- `app/Http/Middleware/`: `CheckRoleKelasHub` untuk RBAC.
- `app/Database/Connectors/`: `CustomMySqlConnector` untuk koneksi TiDB Cloud dengan SSL manual.

---

## 3. Logika Inti (Core Logic)

### 3.1 Multi-Tenant Isolation (Data Tenancy)
Keamanan data antar kelas dijamin melalui Global Scope pada Eloquent. Setiap model yang menggunakan trait `BelongsToClass` secara otomatis akan memfilter query berdasarkan `class_id` pengguna yang sedang login.

```php
// app/Traits/BelongsToClass.php
static::addGlobalScope('class_isolation', function (Builder $builder) {
    if (Auth::hasUser() && Auth::user()->role !== 'super_admin') {
        $builder->where($builder->getQuery()->from . '.class_id', Auth::user()->class_id);
    }
});
```

### 3.2 Engine "Sisa Nyawa" (Attendance Logic)
Sistem kehadiran menggunakan perhitungan pinalti secara real-time:
1. Setiap mahasiswa memiliki **3 Nyawa** per mata kuliah.
2. Setiap record `ClassAttendance` dengan status **'Alfa'** yang sudah **'is_validated'** akan mengurangi 1 nyawa.
3. Rumus: `3 - COUNT(alfa_validated)`.
4. Jika hasil <= 0, status otomatis menjadi **DICEKAL**.

---

## 4. Koneksi Database & Optimasi Serverless

### 4.1 Custom TiDB Connector
Vercel adalah lingkungan stateless yang tidak memiliki penyimpanan persisten untuk sertifikat CA. KelasHUB menggunakan `CustomMySqlConnector` yang secara dinamis menyalin `cacert.pem` dari direktori proyek ke `/tmp` saat runtime untuk membangun koneksi SSL aman ke TiDB Cloud.

### 4.2 Stateless File Storage
Karena filesystem Vercel bersifat *read-only* (kecuali `/tmp`), KelasHUB menyimpan file modul pembelajaran (PDF/DOCX) langsung ke database sebagai string **Base64** dalam kolom `LONGTEXT` di tabel `learning_modules`.

---

## 5. Keamanan & RBAC (Role-Based Access Control)

Akses fitur dibatasi melalui middleware `CheckRoleKelasHub`. Matriks hak akses didefinisikan sebagai berikut:

| Fitur | Super Admin | Ketua | Sekretaris | Bendahara | Mahasiswa |
|:---|:---:|:---:|:---:|:---:|:---:|
| Create Class | ✅ | ❌ | ❌ | ❌ | ❌ |
| Validate Data | ✅ | ✅ | ❌ | ❌ | ❌ |
| Input Cash | ✅ | ✅ | ❌ | ✅ | ❌ |
| Input Absensi | ✅ | ✅ | ✅ | ❌ | 🔘 Self |
| Admin Panel | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 6. Panduan Instalasi Lokal

1. **Clone & Install**:
   ```bash
   git clone <repo_url>
   composer install
   npm install && npm run build
   ```
2. **Environment**:
   Salin `.env.example` ke `.env` dan atur `DB_CONNECTION=sqlite` untuk pengembangan lokal yang cepat.
3. **Database**:
   ```bash
   php artisan migrate
   php artisan db:seed --class=DatabaseSeeder
   ```

---

## 7. Strategi Deployment (Vercel)

1. Pastikan `vercel.json` ada di root proyek.
2. Atur *Environment Variables* di dashboard Vercel:
   - `SESSION_DRIVER=cookie` (Wajib untuk Serverless)
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (TiDB Cloud)
   - `MYSQL_ATTR_SSL_CA=/var/task/cacert.pem`
3. Gunakan Vercel CLI: `vercel --prod`.

---
**Standard Operasional Prosedur**: Setiap perubahan pada skema database harus diikuti dengan pembuatan migrasi baru untuk menjaga integritas data lintas *tenant*.
