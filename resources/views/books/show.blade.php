@extends('layouts.app', ['title' => 'Detail Buku'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Detail Buku</h2>
        <p class="page-subtitle">Informasi lengkap koleksi buku perpustakaan.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('books.barcode.print', $book) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak Label</a>
        <a href="{{ route('books.edit', $book) }}" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Edit</a>
        <a href="{{ route('books.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
      </div>
    </section>

    <section class="admin-card p-5">
      <dl class="grid gap-5 md:grid-cols-2">
        <div><dt class="text-sm font-medium text-slate-500">Kode Buku</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $book->book_code }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">ISBN</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->isbn ?? '-' }}</dd></div>
        <div class="md:col-span-2"><dt class="text-sm font-medium text-slate-500">Judul</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->title }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Penulis</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->author }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Penerbit</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->publisher ?? '-' }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Tahun Terbit</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->publication_year ?? '-' }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Kategori</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->category?->name ?? '-' }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Stok</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->stock }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Stok Tersedia</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->available_stock }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Lokasi Rak</dt><dd class="mt-1 text-sm text-slate-900">{{ $book->shelf_location ?? '-' }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Status</dt><dd class="mt-1"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $book->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($book->status) }}</span></dd></div>
      </dl>
    </section>
  </div>
@endsection
