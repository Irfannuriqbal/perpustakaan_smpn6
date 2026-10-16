@extends('layouts.app', ['title' => 'Backup Database'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Backup Database</h2>
        <p class="page-subtitle">Backup tersimpan di server lokal selama 30 hari dan hanya dapat diakses super admin.</p>
      </div>
      <form action="{{ route('backups.store') }}" method="POST">
        @csrf
        <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Buat Backup Sekarang</button>
      </form>
    </section>

    @include('components.flash-message')

    <section class="admin-card p-5">
      <h3 class="text-base font-semibold text-slate-900">Backup Otomatis Harian</h3>
      <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-slate-600">
        <li>Pastikan <code>mysqldump</code> tersedia di PATH atau isi <code>MYSQLDUMP_PATH</code> pada file <code>.env</code>.</li>
        <li>Buat Windows Task Scheduler yang menjalankan <code>php artisan schedule:run</code> setiap menit.</li>
        <li>Sistem menjalankan backup tiap hari pukul 01:00 dan menghapus file yang lebih dari 30 hari.</li>
      </ol>
    </section>

    <section class="admin-card overflow-hidden">
      <div class="border-b border-slate-200 px-5 py-4">
        <h3 class="text-base font-semibold text-slate-900">Riwayat Backup</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head"><tr><th class="px-4 py-3">File</th><th class="px-4 py-3">Waktu</th><th class="px-4 py-3">Ukuran</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($backups as $backup)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $backup['filename'] }}</td>
                <td class="table-cell">{{ $backup['created_at']->format('d M Y H:i') }}</td>
                <td class="table-cell">{{ number_format($backup['size'] / 1024, 1, ',', '.') }} KB</td>
                <td class="table-cell text-right"><a href="{{ route('backups.download', $backup['filename']) }}" class="text-sm font-semibold text-school-800 hover:text-school-900">Download</a></td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada file backup.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
