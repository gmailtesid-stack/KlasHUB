# Panduan Kontribusi — KelasHUB

Terima kasih atas minat Anda untuk berkontribusi pada **KelasHUB**! 🎉  
Panduan ini menjelaskan cara berkontribusi dengan efektif.

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
- Browser: [Chrome / Firefox]
- Versi PHP: [8.3.x]
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
# atau untuk bugfix:
git checkout -b fix/nama-bug-singkat

# 6. Kerjakan perubahan Anda
# ... edit file ...

# 7. Commit dengan format konvensional (lihat bawah)
git add .
git commit -m "feat: tambah fitur export laporan PDF"

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

### Blade / Frontend
- Gunakan `x-data` Alpine.js untuk reaktivitas, bukan JavaScript inline.
- Semua modal harus bisa ditutup dengan klik di luar (`@click.outside`).
- Pertahankan Zinc-900 color palette untuk konsistensi tema.

### Database / Migrasi
- Selalu sertakan method `down()` yang benar pada setiap migrasi.
- Beri nama migrasi dengan format: `YYYY_MM_DD_HHMMSS_deskripsi_singkat.php`
- Tambahkan `class_id` FK ke tabel baru jika berkaitan dengan data per-kelas.

---

## 📌 Konvensi Commit

Gunakan format **Conventional Commits**:

```
<type>(<scope>): <deskripsi singkat>

[body opsional]
[footer opsional]
```

### Tipe Commit yang Valid

| Tipe | Kapan Digunakan |
|:---|:---|
| `feat` | Fitur baru |
| `fix` | Perbaikan bug |
| `refactor` | Perubahan kode tanpa menambah/memperbaiki fitur |
| `docs` | Perubahan dokumentasi saja |
| `style` | Format, spasi, titik-koma (tanpa ubah logika) |
| `test` | Menambah atau memperbaiki test |
| `chore` | Update dependency, konfigurasi |
| `perf` | Peningkatan performa |

### Contoh Commit yang Benar

```bash
feat(auth): tambah validasi NIM pada login
fix(attendance): perbaiki hitung sisa nyawa mahasiswa transfer
docs(readme): update panduan instalasi lokal
perf(export): ganti Eloquent dengan DB::table pada ekspor CSV
```

---

## 🧪 Sebelum Submit Pull Request

Pastikan:
- [ ] Kode tidak memiliki syntax error (`php artisan route:list`)
- [ ] Migrasi berjalan bersih (`php artisan migrate:fresh`)
- [ ] Tidak ada query tanpa `where('class_id')` pada tabel yang berisi data multi-kelas
- [ ] Semua endpoint yang baru sudah dilindungi middleware `auth`
- [ ] Format kode sudah sesuai PSR-12 (bisa pakai `./vendor/bin/pint`)
