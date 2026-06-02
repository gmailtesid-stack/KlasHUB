# Panduan Kontribusi — KelasHUB

Terima kasih atas minat Anda untuk berkontribusi pada **KelasHUB**! 🎉  
Panduan ini menjelaskan cara berkontribusi dengan efektif pada platform v2.3.0+.

---

## 📋 Daftar Isi
- [Kode Etik](#-kode-etik)
- [Cara Melaporkan Bug](#-cara-melaporkan-bug)
- [Cara Mengajukan Fitur](#-cara-mengajukan-fitur)
- [Alur Kontribusi Kode](#-alur-kontribusi-kode)
- [Standar Penulisan Kode](#-standar-penulisan-kode)
- [Konvensi Commit](#-konvensi-commit)

---

## 🤝 Kode Etik

Proyek ini mengikuti standar komunitas open-source yang inklusif dan saling menghormati. Perlakukan semua kontributor dengan profesional dan supportif.

---

## 🐛 Cara Melaporkan Bug

1. Pastikan bug belum dilaporkan di [Issues](https://github.com/gmailtesid-stack/KlasHUB/issues).
2. Buat Issue baru dengan template berikut:

```markdown
**Deskripsi Bug:**
[Jelaskan bug secara singkat]

**Langkah Reproduksi:**
1. Login sebagai '...'
2. Klik '...'
3. Lihat error di '...'

**Perilaku yang Diharapkan:**
[Apa yang seharusnya terjadi]

**Screenshot / Log:**
[Tambahkan jika ada]

**Environment:**
- OS: [Windows / macOS / Linux]
- Browser / Platform: [Chrome / Android App]
- Versi PHP: [8.3.x]
- Versi App: [2.3.0]
```

---

## 💡 Cara Mengajukan Fitur

1. Diskusikan idenya terlebih dahulu via [GitHub Discussion](https://github.com/gmailtesid-stack/KlasHUB/discussions).
2. Jika disetujui, buat Issue dengan label `enhancement`.
3. Tunggu konfirmasi sebelum mulai mengerjakan.

---

## 🔄 Alur Kontribusi Kode

```bash
# 1. Fork repositori ke akun Anda

# 2. Clone fork Anda
git clone https://github.com/YOUR_USERNAME/KlasHUB.git
cd KlasHUB

# 3. Tambahkan remote upstream
git remote add upstream https://github.com/gmailtesid-stack/KlasHUB.git

# 4. Sync dengan main branch terbaru
git checkout main
git pull upstream main

# 5. Buat branch fitur baru
git checkout -b feat/nama-fitur-singkat

# 6. Kerjakan perubahan Anda

# 7. Commit dengan format konvensional
git add .
git commit -m "feat(notification): tambah trigger notifikasi saat validasi data"

# 8. Push ke fork Anda
git push origin feat/nama-fitur-singkat

# 9. Buat Pull Request ke branch main di repo utama
```

---

## 📝 Standar Penulisan Kode

### PHP / Laravel
- Ikuti **PSR-12** coding standard.
- Gunakan **Query Builder** (`DB::table()`) untuk operasi dalam loop atau batch — bukan Eloquent — demi efisiensi RAM Vercel.
- Selalu tambahkan `where('class_id', $classId)` pada setiap query untuk isolasi data antar kelas.
- Tangani exception dengan `try-catch` pada semua method controller.

```php
// ✅ Benar
$data = DB::table('assignments')
    ->where('class_id', $classId)
    ->orderBy('created_at', 'desc')
    ->get();

// ❌ Hindari di loop/batch
$data = Assignment::where('class_id', $classId)->get();
```

### Notifikasi
- **Wajib** menggunakan `NotificationService` untuk semua pengiriman notifikasi. Jangan panggil OneSignal API langsung dari Controller.

```php
// ✅ Benar
app(NotificationService::class)->notifyClass($classId, 'Ada tugas baru!');

// ❌ Hindari
Http::post('https://onesignal.com/...', [...]);  // dari dalam Controller
```

### Blade / Frontend
- Gunakan `x-data` Alpine.js untuk reaktivitas, bukan JavaScript inline.
- Semua modal harus bisa ditutup dengan klik di luar (`@click.outside`).
- Pertahankan Zinc-900 color palette untuk konsistensi tema.

### Database / Migrasi
- Selalu sertakan method `down()` yang benar pada setiap migrasi.
- Beri nama migrasi dengan format: `YYYY_MM_DD_HHMMSS_deskripsi_singkat.php`
- Tambahkan `class_id` FK ke tabel baru jika berkaitan dengan data per-kelas.

### Android (Kotlin)
- Semua API call harus melalui `ApiClient.apiInterface`.
- Token sinkronisasi (OneSignal) harus dipanggil ulang setiap kali user masuk ke `MainActivity`.

---

## 📌 Konvensi Commit

Gunakan format **Conventional Commits**:

```
<type>(<scope>): <deskripsi singkat>
```

### Tipe Commit yang Valid

| Tipe | Kapan Digunakan |
|:---|:---|
| `feat` | Fitur baru |
| `fix` | Perbaikan bug |
| `refactor` | Perubahan kode tanpa menambah/memperbaiki fitur |
| `docs` | Perubahan dokumentasi saja |
| `style` | Format, spasi (tanpa ubah logika) |
| `test` | Menambah atau memperbaiki test |
| `chore` | Update dependency, konfigurasi |
| `perf` | Peningkatan performa |

### Contoh Commit yang Benar

```bash
feat(notification): tambah NotificationService dengan OneSignal v2
fix(android): perbaiki null check pada syncOneSignalToken
docs(api): update dokumentasi endpoint /kh/device-token
chore(android): update OneSignal SDK ke versi terbaru
perf(export): ganti Eloquent dengan DB::table pada ekspor CSV
```

---

## 🧪 Sebelum Submit Pull Request

Pastikan:
- [ ] Kode tidak memiliki syntax error (`php artisan route:list`)
- [ ] Migrasi berjalan bersih (`php artisan migrate:fresh`)
- [ ] Tidak ada query tanpa `where('class_id')` pada tabel multi-kelas
- [ ] Semua endpoint baru dilindungi middleware `auth`
- [ ] Fitur yang memerlukan notifikasi sudah menggunakan `NotificationService`
- [ ] Format kode sudah sesuai PSR-12 (`./vendor/bin/pint`)
