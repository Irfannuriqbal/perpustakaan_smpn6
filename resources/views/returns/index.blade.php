@extends('layouts.app', ['title' => 'Pengembalian'])

@section('content')
  <div class="space-y-6">
    <section>
      <h2 class="page-title">Pengembalian</h2>
      <p class="page-subtitle">Proses pengembalian buku berdasarkan kode buku. Input mendukung keyboard dan barcode scanner USB.</p>
    </section>

    @include('components.flash-message')

    <section class="grid gap-6 xl:grid-cols-[360px_1fr]">
      <div class="space-y-6">
        <div class="admin-card p-5">
          <h3 class="text-base font-semibold text-slate-900">Scan Kode Buku</h3>
          <p class="mt-1 text-sm text-slate-500">Masukkan atau scan <span class="font-medium text-slate-700">book_code</span>, lalu tekan Enter.</p>

          <form action="{{ route('returns.search') }}" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
              <label for="book_code" class="sr-only">Kode buku</label>
              <input id="book_code" name="book_code" type="text" value="{{ old('book_code') }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Contoh: BK-001" autocomplete="off" autofocus>
              @error('book_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Cari Peminjaman</button>
          </form>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
          <p class="font-semibold">Catatan</p>
          <p class="mt-1 leading-6">Hanya buku dengan status sedang dipinjam atau terlambat yang bisa diproses. Sistem tidak menghitung denda uang.</p>
        </div>
      </div>

      <div class="admin-card">
        <div class="border-b border-slate-200 px-5 py-4">
          <h3 class="text-base font-semibold text-slate-900">Detail Pengembalian</h3>
          <p class="mt-1 text-sm text-slate-500">Periksa data sebelum menekan tombol konfirmasi.</p>
        </div>

        @if ($loanItem)
          <div class="p-5">
            <dl class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Kode Buku</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $loanItem->book?->book_code }}</dd>
              </div>
              <div class="xl:col-span-2">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Judul Buku</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $loanItem->book?->title }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nama Siswa</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $loanItem->loan?->student?->name }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">NIS</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $loanItem->loan?->student?->nis }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Kelas</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $loanItem->loan?->student?->class }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Tanggal Pinjam</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $loanItem->loan?->loan_date?->format('d M Y') }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Jatuh Tempo</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $loanItem->loan?->due_date?->format('d M Y') }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Status Keterlambatan</dt>
                <dd class="mt-1">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $isLate ? 'bg-amber-50 text-amber-800 ring-amber-200' : 'bg-emerald-50 text-emerald-800 ring-emerald-200' }}">{{ $isLate ? 'Terlambat' : 'Tepat waktu' }}</span>
                </dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Jumlah Hari Terlambat</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $lateDays }} hari</dd>
              </div>
            </dl>

            <form action="{{ route('returns.store', $loanItem) }}" method="POST" class="mt-6 border-t border-slate-200 pt-5" onsubmit="return confirm('Konfirmasi pengembalian buku ini?');">
              @csrf
              <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Konfirmasi Pengembalian</button>
            </form>
          </div>
        @else
          <div class="p-5">
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
              <p class="text-sm font-medium text-slate-700">Belum ada buku dipilih.</p>
              <p class="mt-1 text-sm text-slate-500">Scan atau masukkan kode buku untuk mencari peminjaman aktif.</p>
            </div>
          </div>
        @endif
      </div>
    </section>

    <section class="admin-card">
      <div class="border-b border-slate-200 px-5 py-4">
        <h3 class="text-base font-semibold text-slate-900">Riwayat Pengembalian Terbaru</h3>
        <p class="mt-1 text-sm text-slate-500">Daftar buku yang sudah dikembalikan.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Kode Buku</th>
              <th class="px-4 py-3">Judul Buku</th>
              <th class="px-4 py-3">Siswa</th>
              <th class="px-4 py-3">Tanggal Kembali</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($returnedItems as $item)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $item->book?->book_code }}</td>
                <td class="table-cell">{{ $item->book?->title }}</td>
                <td class="table-cell">{{ $item->loan?->student?->name ?? '-' }}</td>
                <td class="table-cell">{{ $item->returned_at?->format('d M Y H:i') }}</td>
                <td class="table-cell"><span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-800 ring-1 ring-emerald-200">Dikembalikan</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Belum ada riwayat pengembalian.</p>
                  <p class="mt-1 text-sm text-slate-500">Data akan muncul setelah buku berhasil dikembalikan.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
