@extends('layouts.app', ['title' => 'Edit Kategori'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Edit Kategori</h2>
        <p class="page-subtitle">Perbarui kategori {{ $category->name }}.</p>
      </div>
      <a href="{{ route('categories.index') }}" class="text-sm font-medium text-school-800 hover:text-school-900">Kembali ke Kategori</a>
    </section>

    @include('components.flash-message')

    <form action="{{ route('categories.update', $category) }}" method="POST" class="admin-card p-5">
      @method('PUT')
      @include('categories._form')
    </form>
  </div>
@endsection
