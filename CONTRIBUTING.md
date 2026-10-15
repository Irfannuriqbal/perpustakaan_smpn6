# Panduan Kontribusi

Dokumen ini menjadi acuan kerja bersama agar perubahan dari anggota kelompok tetap mudah ditinjau dan diintegrasikan.

## Alur kerja

1. Sinkronkan branch utama sebelum mulai bekerja:

   ```bash
   git switch main
   git pull origin main
   ```

2. Buat branch baru dengan nama yang menjelaskan pekerjaan:

   ```bash
   git switch -c feature/nama-fitur
   ```

   Gunakan prefix `feature/`, `fix/`, `refactor/`, atau `docs/` sesuai jenis perubahan.

3. Kerjakan satu fokus perubahan dalam satu branch.
4. Jalankan test dan build sebelum membuat pull request.
5. Push branch dan buat pull request ke `main`.
6. Minta minimal satu anggota lain melakukan review sebelum merge.

## Standar commit

Gunakan format singkat dan konsisten:

```text
type: ringkasan perubahan
```

Contoh:

- `feat: tambah validasi stok buku`
- `fix: perbaiki proses pengembalian`
- `docs: perbarui panduan instalasi`

## Checklist pull request

- [ ] Perubahan memiliki tujuan yang jelas.
- [ ] Tidak ada file `.env`, password, atau credential yang ikut di-commit.
- [ ] `php artisan test` berhasil.
- [ ] `npm run build` berhasil jika menyentuh frontend.
- [ ] Perubahan database memiliki migration atau seeder yang sesuai.
- [ ] Dokumentasi diperbarui jika alur penggunaan berubah.

## Aturan keamanan

Jangan mengunggah `.env`, kredensial database, API key, atau data siswa yang sebenarnya. Gunakan `.env.example` sebagai referensi konfigurasi lokal.
