@extends('layouts.app', ['title' => 'Detail Kategori'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Detail Kategori</h2>
        <p class="page-subtitle">Informasi kategori buku perpustakaan.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('categories.edit', $category) }}" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Edit</a>
        <a href="{{ route('categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
      </div>
    </section>

    <section class="admin-card p-5">
      <dl class="grid gap-5 md:grid-cols-2">
        <div><dt class="text-sm font-medium text-slate-500">Nama Kategori</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $category->name }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Jumlah Buku</dt><dd class="mt-1 text-sm text-slate-900">{{ $category->books_count }}</dd></div>
        <div class="md:col-span-2"><dt class="text-sm font-medium text-slate-500">Deskripsi</dt><dd class="mt-1 text-sm text-slate-900">{{ $category->description ?? '-' }}</dd></div>
      </dl>
    </section>
  </div>
@endsection
