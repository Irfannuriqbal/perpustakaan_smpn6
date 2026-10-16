@extends('layouts.app', ['title' => 'Tambah Siswa'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Tambah Siswa</h2>
        <p class="page-subtitle">Isi data anggota perpustakaan siswa dengan lengkap.</p>
      </div>
      <a href="{{ route('students.index') }}" class="text-sm font-medium text-school-800 hover:text-school-900">Kembali ke Data Siswa</a>
    </section>

    @include('components.flash-message')

    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="admin-card p-5">
      @include('students._form')
    </form>
  </div>
@endsection
