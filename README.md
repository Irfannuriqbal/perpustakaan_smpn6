# Sistem Informasi Perpustakaan SMP Negeri 6 Bandung

Aplikasi web untuk membantu pengelolaan perpustakaan SMP Negeri 6 Bandung, meliputi data siswa, koleksi buku, peminjaman, pengembalian, kunjungan, dan laporan.

## Teknologi

- PHP 8.2+
- Laravel 12
- Blade
- Tailwind CSS
- Vite
- MySQL

## Persiapan lokal

Pastikan PHP, Composer, Node.js, dan MySQL sudah tersedia. Jalankan perintah berikut dari direktori proyek:

```bash
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Untuk pengembangan frontend dengan hot reload:

```bash
npm run dev
```

Sesuaikan koneksi database di `.env` sebelum menjalankan migrasi. File `.env` bersifat lokal dan tidak boleh di-commit.

## Pengujian

```bash
php artisan test
npm run build
```

## Modul utama

- `/login` — autentikasi pengguna
- `/dashboard` — ringkasan aktivitas perpustakaan
- `/members` — pengelolaan data anggota perpustakaan (siswa dan guru/staf)
- `/books` — koleksi dan stok buku
- `/loans` — transaksi peminjaman
- `/returns` — proses pengembalian
- `/visits` — pencatatan kunjungan
- `/reports` — laporan perpustakaan
- `/settings` — pengaturan aplikasi

### Portal anggota

Anggota masuk melalui `/anggota/login` menggunakan nomor anggota dan password yang dikelola petugas. Portal menyediakan beranda, riwayat peminjaman, notifikasi keterlambatan dan jatuh tempo, pengembalian, perpanjangan pinjaman, profil anggota, serta kartu digital dengan QR yang dapat dicetak.

## Kontribusi

Alur kerja tim, aturan branch, dan standar pull request tersedia di [CONTRIBUTING.md](CONTRIBUTING.md).

## Lisensi

Proyek ini menggunakan lisensi MIT.`