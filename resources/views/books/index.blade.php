@extends('layouts.app', ['title' => 'Data Buku'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Data Buku</h2>
        <p class="page-subtitle">Daftar koleksi buku dan ketersediaan stok perpustakaan.</p>
      </div>
      <div class="flex flex-col gap-2 sm:flex-row">
        <a href="{{ route('books.barcodes.print', request()->query()) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak Barcode</a>
        <a href="{{ route('categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Kelola Kategori</a>
        <a href="{{ route('books.create') }}" class="rounded-lg bg-school-800 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-school-900">Tambah Buku</a>
      </div>
    </section>

    @include('components.flash-message')

    <section class="admin-card">
      <form method="GET" action="{{ route('books.index') }}" class="grid gap-3 border-b border-slate-200 px-5 py-4 lg:grid-cols-[1fr_220px_auto]">
        <div>
          <label for="search" class="sr-only">Cari buku</label>
          <input id="search" name="search" type="search" value="{{ $search }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Cari kode, judul, penulis, atau ISBN">
        </div>
        <div>
          <label for="category_id" class="sr-only">Filter kategori</label>
          <select id="category_id" name="category_id" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Filter</button>
          <a href="{{ route('books.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Kode Buku</th>
              <th class="px-4 py-3">Judul</th>
              <th class="px-4 py-3">Penulis</th>
              <th class="px-4 py-3">Kategori</th>
              <th class="px-4 py-3">Stok</th>
              <th class="px-4 py-3">Tersedia</th>
              <th class="px-4 py-3">Rak</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($books as $book)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $book->book_code }}</td>
                <td class="table-cell">{{ $book->title }}</td>
                <td class="table-cell">{{ $book->author }}</td>
                <td class="table-cell">{{ $book->category?->name ?? '-' }}</td>
                <td class="table-cell">{{ $book->stock }}</td>
                <td class="table-cell">{{ $book->available_stock }}</td>
                <td class="table-cell">{{ $book->shelf_location ?? '-' }}</td>
                <td class="table-cell">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $book->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($book->status) }}</span>
                </td>
                <td class="table-cell">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('books.show', $book) }}" class="text-sm font-medium text-school-800 hover:text-school-900">Detail</a>
                    <a href="{{ route('books.barcode.print', $book) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Cetak Label</a>
                    <a href="{{ route('books.edit', $book) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Edit</a>
                    <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Hapus data buku ini? Buku yang sudah terhubung peminjaman tidak dapat dihapus.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Data buku tidak ditemukan.</p>
                  <p class="mt-1 text-sm text-slate-500">Ubah filter pencarian atau tambahkan data buku baru.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($books->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $books->links() }}
        </div>
      @endif
    </section>
  </div>
@endsection
