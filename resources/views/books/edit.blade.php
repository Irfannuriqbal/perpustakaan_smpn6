@extends('layouts.app', ['title' => 'Edit Buku'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Edit Buku</h2>
        <p class="page-subtitle">Perbarui data buku {{ $book->title }}.</p>
      </div>
      <a href="{{ route('books.index') }}" class="text-sm font-medium text-school-800 hover:text-school-900">Kembali ke Data Buku</a>
    </section>

    @include('components.flash-message')

    <form action="{{ route('books.update', $book) }}" method="POST" class="admin-card p-5">
      @method('PUT')
      @include('books._form')
    </form>
  </div>
@endsection
