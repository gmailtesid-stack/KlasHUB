# Panduan Teknis (Technical Documentation): KelasHUB

**Proyek**: KelasHUB — All-in-One Class Operations Suite  
**Teknologi**: Laravel 13, TiDB Cloud, Vercel Serverless, OneSignal  
**Versi**: 2.3.0  
**Tanggal**: 30 Mei 2026

---

## 1. Arsitektur Sistem & Stack

KelasHUB dirancang dengan arsitektur **Monolitik Serverless**. Backend berjalan sebagai fungsi stateless di Vercel, data disimpan di TiDB Cloud, dan notifikasi real-time dikirim via OneSignal.

### Stack Utama:
- **Backend**: Laravel 13.x (PHP 8.3+)
- **Database**: TiDB Cloud (Distributed MySQL-Compatible)
- **Frontend**: Tailwind CSS v4 + Alpine.js
- **Runtime**: Vercel Serverless Functions (`vercel-php`)
- **Mobile**: Kotlin Android Native (Retrofit2 + OneSignal SDK)
- **Notifikasi**: OneSignal REST API v2

### Diagram Arsitektur:
```
┌─────────────────────────────────────────────────────────┐
│                   CLIENT LAYER                          │
│  Android App (Kotlin Native)  /  Mobile Browser         │
└───────────┬───────────────────────────┬─────────────────┘
            │ HTTPS (Retrofit2)         │ OneSignal SDK
            │                           ▼
┌───────────▼──────────┐   ┌───────────────────────────────┐
│   VERCEL EDGE        │   │  OneSignal Platform            │
│   Laravel 13 Backend │   │  Push Notification Delivery    │
└───────────┬──────────┘   └───────────────────────────────┘
            │
┌───────────▼──────────┐
│    TiDB CLOUD        │
│  MySQL-Compatible DB │
└──────────────────────┘
```

---

## 2. Struktur Kode & Organisasi

Mengikuti pola MVC Laravel dengan penambahan Layer Service untuk notifikasi.

### Folder Utama:
- `app/Http/Controllers/`: Logika bisnis utama per domain.
- `app/Services/NotificationService.php`: **[v2.3 BARU]** Service terpusat untuk semua pengiriman notifikasi (internal + OneSignal).
- `app/Models/`: Definisi entitas data.
- `app/Http/Middleware/`: `CheckRoleKelasHub` untuk RBAC.
- `android-webview/`: Aplikasi Android native Kotlin.
  - `MainApplication.kt`: Inisialisasi OneSignal SDK.
  - `MainActivity.kt`: Dashboard + token sync logic.
  - `ApiInterface.kt`: Definisi endpoint Retrofit2.

---

## 3. Logika Inti (Core Logic)

### 3.1 Multi-Tenant Isolation (Data Tenancy)
Keamanan data antar kelas dijamin melalui Global Scope pada Eloquent. Setiap model yang menggunakan trait `BelongsToClass` memfilter query berdasarkan `class_id` pengguna yang login.

```php
// app/Traits/BelongsToClass.php
static::addGlobalScope('class_isolation', function (Builder $builder) {
    if (Auth::hasUser() && Auth::user()->role !== 'super_admin') {
        $builder->where($builder->getQuery()->from . '.class_id', Auth::user()->class_id);
    }
});
```

### 3.2 Engine "Sisa Nyawa" (Attendance Logic)
1. Setiap mahasiswa memiliki **3 Nyawa** per mata kuliah.
2. Setiap record `ClassAttendance` dengan status **'Alfa'** yang **'is_validated'** mengurangi 1 nyawa.
3. Rumus: `3 - COUNT(alfa_validated)`.
4. Jika hasil <= 0, status otomatis **DICEKAL**.

### 3.3 NotificationService (v2.3)
Service terpusat yang menangani dua jenis notifikasi:

**Internal** (disimpan di tabel `notifications`):
```php
Notification::create([
    'class_id'   => $classId,
    'student_id' => $studentId,  // null = untuk semua
    'message'    => $message,
]);
```

**Eksternal** via OneSignal REST API v2:
```php
// Kirim push notification ke perangkat yang terdaftar
Http::withHeaders(['Authorization' => 'Key ' . env('ONESIGNAL_REST_API_KEY')])
    ->post('https://onesignal.com/api/v1/notifications', [
        'app_id'                   => env('ONESIGNAL_APP_ID'),
        'include_subscription_ids' => $subscriptionIds,  // onesignal_id dari students
        'contents'                 => ['en' => $message],
    ]);
```

**Method yang tersedia:**
| Method | Deskripsi |
|:---|:---|
| `notifyClass($classId, $message)` | Kirim ke semua anggota kelas |
| `notifyStudent($studentId, $message)` | Kirim ke individu tertentu |
| `notifyAdmins($classId, $message)` | Kirim ke Ketua Kelas dan pengurus |

---

## 4. Koneksi Database & Optimasi Serverless

### 4.1 Custom TiDB Connector
`CustomMySqlConnector` menyalin `cacert.pem` dari direktori proyek ke `/tmp` saat runtime untuk membangun koneksi SSL aman ke TiDB Cloud (karena filesystem Vercel bersifat read-only kecuali `/tmp`).

### 4.2 Stateless File Storage
KelasHUB menyimpan file modul pembelajaran (PDF/DOCX) sebagai string **Base64** dalam kolom `LONGTEXT` di tabel `learning_modules` — menghindari keterbatasan filesystem Vercel.

### 4.3 Environment Variables Wajib

| Key | Deskripsi |
|:---|:---|
| `ONESIGNAL_APP_ID` | ID Aplikasi OneSignal |
| `ONESIGNAL_REST_API_KEY` | REST API Key v2 OneSignal |
| `SESSION_DRIVER` | Wajib `cookie` untuk Serverless |
| `MYSQL_ATTR_SSL_CA` | Path SSL cert TiDB: `/var/task/cacert.pem` |
| `DB_HOST`, `DB_PORT`, dst. | Koneksi database |

---

## 5. Keamanan & RBAC

Akses fitur dibatasi melalui middleware `CheckRoleKelasHub`.

| Fitur | Super Admin | Ketua | Sekretaris | Bendahara | Mahasiswa |
|:---|:---:|:---:|:---:|:---:|:---:|
| Create Class | ✅ | ❌ | ❌ | ❌ | ❌ |
| Validate Data | ✅ | ✅ | ❌ | ❌ | ❌ |
| Input Cash | ✅ | ✅ | ❌ | ✅ | ❌ |
| Input Absensi | ✅ | ✅ | ✅ | ❌ | 🔘 Self |
| Kirim Notifikasi | ✅ | ✅ (auto) | ✅ (auto) | ✅ (auto) | ❌ |

---

## 6. Integrasi Android Native (v2.3)

### `MainApplication.kt` — Inisialisasi OneSignal
Kelas ini dijalankan pertama kali sebelum Activity manapun. Bertanggung jawab menginisialisasi OneSignal SDK dengan App ID produksi.

```kotlin
class MainApplication : Application() {
    override fun onCreate() {
        super.onCreate()
        OneSignal.initWithContext(this, "04a9cff3-874a-4e84-96c0-f79cfa86d255")
    }
}
```

### `MainActivity.kt` — Sinkronisasi Token
Setelah dashboard selesai dimuat, `syncOneSignalToken()` dipanggil untuk mengirimkan `onesignal_id` ke backend:

```kotlin
private fun syncOneSignalToken() {
    val subscriptionId = OneSignal.User.pushSubscription.id
    if (!subscriptionId.isNullOrEmpty()) {
        ApiClient.apiInterface.updateDeviceToken(subscriptionId).enqueue(...)
    }
}
```

### Alur Registrasi Token Perangkat:
```
Mahasiswa Login → MainActivity.onCreate()
    → fetchData()       (load dashboard)
    → syncOneSignalToken()
        → OneSignal.User.pushSubscription.id  (ambil UUID perangkat)
        → POST /kh/device-token { player_id: "uuid" }
            → Backend: students.onesignal_id = uuid  (disimpan di database)
```

---

## 7. Panduan Instalasi Lokal

1. **Clone & Install**:
   ```bash
   git clone https://github.com/gmailtesid-stack/KlasHUB.git
   composer install
   npm install && npm run build
   ```
2. **Environment**:
   Salin `.env.example` ke `.env`, atur `DB_CONNECTION=sqlite` untuk lokal.
   Tambahkan `ONESIGNAL_APP_ID` dan `ONESIGNAL_REST_API_KEY` ke `.env`.
3. **Database**:
   ```bash
   php artisan migrate
   ```

---

## 8. Strategi Deployment (Vercel)

1. Pastikan `vercel.json` ada di root proyek.
2. Atur *Environment Variables* di Vercel Dashboard:
   - `SESSION_DRIVER=cookie` (Wajib)
   - `ONESIGNAL_APP_ID` dan `ONESIGNAL_REST_API_KEY` (Wajib untuk notifikasi)
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `MYSQL_ATTR_SSL_CA`
3. Deploy: `vercel --prod` atau push ke GitHub.

---
**Standard Operasional Prosedur**: Setiap penambahan fitur yang memerlukan notifikasi push WAJIB menggunakan `NotificationService` — jangan panggil OneSignal API secara langsung dari Controller.
