@extends('layouts.app', ['title' => 'Kategori Buku'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Kategori Buku</h2>
        <p class="page-subtitle">Kelompokkan koleksi buku berdasarkan kategori perpustakaan.</p>
      </div>
      <div class="flex flex-col gap-2 sm:flex-row">
        <a href="{{ route('books.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Data Buku</a>
        <a href="{{ route('categories.create') }}" class="rounded-lg bg-school-800 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-school-900">Tambah Kategori</a>
      </div>
    </section>

    @include('components.flash-message')

    <section class="admin-card">
      <form method="GET" action="{{ route('categories.index') }}" class="grid gap-3 border-b border-slate-200 px-5 py-4 lg:grid-cols-[1fr_auto]">
        <div>
          <label for="search" class="sr-only">Cari kategori</label>
          <input id="search" name="search" type="search" value="{{ $search }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Cari nama atau deskripsi kategori">
        </div>
        <div class="flex gap-2">
          <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Filter</button>
          <a href="{{ route('categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Nama Kategori</th>
              <th class="px-4 py-3">Deskripsi</th>
              <th class="px-4 py-3">Jumlah Buku</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($categories as $category)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $category->name }}</td>
                <td class="table-cell">{{ $category->description ?? '-' }}</td>
                <td class="table-cell">{{ $category->books_count }}</td>
                <td class="table-cell">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('categories.show', $category) }}" class="text-sm font-medium text-school-800 hover:text-school-900">Detail</a>
                    <a href="{{ route('categories.edit', $category) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Edit</a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini? Kategori yang masih digunakan buku tidak dapat dihapus.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Kategori tidak ditemukan.</p>
                  <p class="mt-1 text-sm text-slate-500">Ubah filter pencarian atau tambahkan kategori baru.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($categories->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $categories->links() }}
        </div>
      @endif
    </section>
  </div>
@endsection
