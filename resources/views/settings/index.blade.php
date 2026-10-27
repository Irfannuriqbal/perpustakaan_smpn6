@extends('layouts.app', ['title' => 'Pengaturan'])

@section('content')
  <div class="space-y-6">
    <section>
      <h2 class="page-title">Pengaturan Sistem</h2>
      <p class="page-subtitle">Kontrol operasional perpustakaan. Hanya super admin yang dapat membuka halaman ini.</p>
    </section>

    <section class="grid gap-5 lg:grid-cols-2">
      <article class="admin-card p-5">
        <h3 class="text-base font-semibold text-slate-900">Akun Admin</h3>
        <p class="mt-2 text-sm text-slate-600">Tambah, reset password, aktifkan, atau nonaktifkan akun petugas perpustakaan.</p>
        <a href="{{ route('users.index') }}" class="mt-4 inline-flex rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Kelola Akun Admin</a>
      </article>

      <article class="admin-card p-5">
        <h3 class="text-base font-semibold text-slate-900">Backup Database</h3>
        <p class="mt-2 text-sm text-slate-600">Buat, unduh, dan pantau backup MySQL yang tersimpan di server lokal selama 30 hari.</p>
        <a href="{{ route('backups.index') }}" class="mt-4 inline-flex rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Buka Backup</a>
      </article>
    </section>

    <section class="admin-card p-5">
      <h3 class="text-base font-semibold text-slate-900">Checklist Scanner Sebelum Dipakai</h3>
      <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm text-slate-700">
        <li>Gunakan scanner USB <strong>2D</strong> agar dapat membaca QR kartu fisik, QR dari layar HP, dan barcode buku.</li>
        <li>Atur scanner agar mengirim tombol <strong>Enter</strong> setelah scan.</li>
        <li>Uji QR kartu fisik yang sudah dilaminating, QR kartu digital pada HP, serta barcode buku dengan komputer perpustakaan.</li>
        <li>Buka <strong>Layar Scan Kunjungan</strong> untuk scan mandiri siswa. Peminjaman dan pengembalian tetap dilakukan admin agar stok dan transaksi terjaga.</li>
      </ol>
    </section>

    <section class="admin-card p-5">
      <h3 class="text-base font-semibold text-slate-900">Setup Sekali Setelah Deploy</h3>
      <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm text-slate-700">
        <li>Jalankan <code>php artisan storage:link</code> agar foto anggota tampil pada kartu fisik dan kartu digital.</li>
        <li>Pastikan <code>mysqldump</code> tersedia pada komputer server atau isi <code>MYSQLDUMP_PATH</code> di file <code>.env</code>.</li>
        <li>Buat Windows Task Scheduler yang menjalankan <code>php artisan schedule:run</code> setiap menit agar backup harian pukul 01:00 berjalan.</li>
      </ol>
    </section>
  </div>
@endsection
