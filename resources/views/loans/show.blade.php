@extends('layouts.app', ['title' => 'Detail Peminjaman'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Detail Peminjaman</h2>
        <p class="page-subtitle">Informasi transaksi peminjaman dan status jatuh tempo.</p>
      </div>
      <a href="{{ route('loans.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Peminjaman</a>
    </section>

    @include('components.flash-message')

    <section class="admin-card p-5">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <p class="text-sm font-medium text-slate-500">Kode Transaksi</p>
          <h3 class="mt-1 text-xl font-semibold text-slate-900">{{ $loan->loan_code }}</h3>
        </div>
        @if ($canExtend)
          <form action="{{ route('loans.extend', $loan) }}" method="POST" onsubmit="return confirm('Perpanjang masa peminjaman selama 7 hari?');">
            @csrf
            <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Perpanjang</button>
          </form>
        @else
          <span class="inline-flex rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 ring-1 ring-slate-200">Tidak dapat diperpanjang</span>
        @endif
      </div>

      <dl class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Siswa</dt>
          <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $loan->student?->name ?? '-' }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">No. Anggota / Kelas</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $loan->student?->no ?? '-' }} / {{ $loan->student?->class ?? '-' }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Admin</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $loan->user?->name ?? '-' }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</dt>
          <dd class="mt-1">
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $statusClass }}">{{ $statusLabel }}</span>
          </dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Tanggal Pinjam</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $loan->loan_date?->format('d M Y') }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Jatuh Tempo</dt>
          <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $loan->due_date?->format('d M Y') }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Jumlah Perpanjangan</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $loan->extension_count }} kali</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Keterlambatan</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $lateDays > 0 ? $lateDays.' hari' : '-' }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Tanggal Kembali</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $loan->returned_at?->format('d M Y H:i') ?? '-' }}</dd>
        </div>
      </dl>
    </section>

    <section class="admin-card">
      <div class="border-b border-slate-200 px-5 py-4">
        <h3 class="text-base font-semibold text-slate-900">Daftar Buku</h3>
        <p class="mt-1 text-sm text-slate-500">Item buku dalam transaksi peminjaman.</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Kode Buku</th>
              <th class="px-4 py-3">Judul</th>
              <th class="px-4 py-3">Penulis</th>
              <th class="px-4 py-3">Status Item</th>
              <th class="px-4 py-3">Tanggal Kembali</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @foreach ($loan->items as $item)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $item->book?->book_code ?? '-' }}</td>
                <td class="table-cell">{{ $item->book?->title ?? '-' }}</td>
                <td class="table-cell">{{ $item->book?->author ?? '-' }}</td>
                <td class="table-cell">{{ ucfirst($item->status) }}</td>
                <td class="table-cell">{{ $item->returned_at?->format('d M Y H:i') ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
