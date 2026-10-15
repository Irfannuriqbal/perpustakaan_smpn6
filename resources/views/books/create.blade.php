@extends('layouts.app', ['title' => 'Tambah Buku'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Tambah Buku</h2>
        <p class="page-subtitle">Isi data koleksi buku perpustakaan dengan lengkap.</p>
      </div>
      <a href="{{ route('books.index') }}" class="text-sm font-medium text-school-800 hover:text-school-900">Kembali ke Data Buku</a>
    </section>

    @include('components.flash-message')

    <form action="{{ route('books.store') }}" method="POST" class="admin-card p-5">
      @include('books._form')
    </form>
  </div>
@endsection
