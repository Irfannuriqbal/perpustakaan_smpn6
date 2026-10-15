@extends('layouts.app', ['title' => 'Catat Peminjaman'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Catat Peminjaman</h2>
        <p class="page-subtitle">Gunakan input keyboard atau scanner barcode USB untuk kode anggota dan kode buku.</p>
      </div>
      <a href="{{ route('loans.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Peminjaman</a>
    </section>

    @include('components.flash-message')

    <section class="grid gap-6 xl:grid-cols-[360px_1fr]">
      <div class="space-y-6">
        <div class="admin-card p-5">
          <h3 class="text-base font-semibold text-slate-900">1. Scan Kode Anggota</h3>
          <p class="mt-1 text-sm text-slate-500">Masukkan atau scan <span class="font-medium text-slate-700">member_code</span> siswa, lalu tekan Enter.</p>

          <form action="{{ route('loans.student') }}" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
              <label for="member_code" class="sr-only">Kode anggota</label>
              <input id="member_code" name="member_code" type="text" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('member_code') border-red-300 @enderror" placeholder="Contoh: AGT-2026-001" autocomplete="off" @if (! $student) autofocus @endif>
              @error('member_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Cari Siswa</button>
          </form>
        </div>

        <div class="admin-card p-5">
          <h3 class="text-base font-semibold text-slate-900">2. Scan Kode Buku</h3>
          <p class="mt-1 text-sm text-slate-500">Setelah siswa ditemukan, scan <span class="font-medium text-slate-700">book_code</span> buku.</p>

          <form action="{{ route('loans.books.add') }}" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
              <label for="book_code" class="sr-only">Kode buku</label>
              <input id="book_code" name="book_code" type="text" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('book_code') border-red-300 @enderror" placeholder="Contoh: BK-003" autocomplete="off" @if ($student) autofocus @endif @disabled(! $student)>
              @error('book_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900 disabled:cursor-not-allowed disabled:bg-slate-300" @disabled(! $student)>Tambah Buku</button>
          </form>
        </div>
      </div>

      <div class="space-y-6">
        <div class="admin-card p-5">
          <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
              <h3 class="text-base font-semibold text-slate-900">Informasi Siswa</h3>
              <p class="mt-1 text-sm text-slate-500">Detail siswa tampil setelah kode anggota ditemukan.</p>
            </div>
            <form action="{{ route('loans.draft.reset') }}" method="POST" onsubmit="return confirm('Kosongkan draft peminjaman ini?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset Draft</button>
            </form>
          </div>

          @if ($student)
            <dl class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nama</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->name }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">NIS</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $student->nis }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Kelas</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $student->class }}</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Status Siswa</dt>
                <dd class="mt-1">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $student->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($student->status) }}</span>
                </dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Buku Aktif</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $activeBorrowedCount }} buku</dd>
              </div>
              <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Pinjaman Terlambat</dt>
                <dd class="mt-1">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $hasLateLoan ? 'bg-amber-50 text-amber-800 ring-amber-200' : 'bg-emerald-50 text-emerald-800 ring-emerald-200' }}">{{ $hasLateLoan ? 'Ada' : 'Tidak ada' }}</span>
                </dd>
              </div>
            </dl>
          @else
            <div class="mt-5 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada siswa dipilih.</div>
          @endif
        </div>

        <div class="admin-card">
          <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 md:flex-row md:items-center md:justify-between">
            <div>
              <h3 class="text-base font-semibold text-slate-900">Daftar Peminjaman Sementara</h3>
              <p class="mt-1 text-sm text-slate-500">Tanggal pinjam {{ $loanDate->format('d M Y') }}, jatuh tempo {{ $dueDate->format('d M Y') }}.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">{{ $books->count() }} buku</span>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="table-head">
                <tr>
                  <th class="px-4 py-3">Kode Buku</th>
                  <th class="px-4 py-3">Judul</th>
                  <th class="px-4 py-3">Penulis</th>
                  <th class="px-4 py-3">Stok Tersedia</th>
                  <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($books as $book)
                  <tr>
                    <td class="table-cell font-medium text-slate-900">{{ $book->book_code }}</td>
                    <td class="table-cell">{{ $book->title }}</td>
                    <td class="table-cell">{{ $book->author }}</td>
                    <td class="table-cell">{{ $book->available_stock }}</td>
                    <td class="table-cell">
                      <form action="{{ route('loans.books.remove', $book) }}" method="POST" class="flex justify-end">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">Hapus</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="px-4 py-10 text-center">
                      <p class="text-sm font-medium text-slate-700">Belum ada buku dalam daftar peminjaman.</p>
                      <p class="mt-1 text-sm text-slate-500">Scan kode buku untuk menambahkan item.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Maksimal 2 buku aktif per siswa. Semua aturan divalidasi ulang saat disimpan.</p>
            <form action="{{ route('loans.store') }}" method="POST">
              @csrf
              <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900 disabled:cursor-not-allowed disabled:bg-slate-300" @disabled(! $student || $books->isEmpty())>Simpan Peminjaman</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection
