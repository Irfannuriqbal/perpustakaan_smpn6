@extends('layouts.app', ['title' => 'Edit Siswa'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Edit Siswa</h2>
        <p class="page-subtitle">Perbarui data siswa {{ $student->name }}.</p>
      </div>
      <a href="{{ route('students.index') }}" class="text-sm font-medium text-school-800 hover:text-school-900">Kembali ke Data Siswa</a>
    </section>

    @include('components.flash-message')

    <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data" class="admin-card p-5">
      @method('PUT')
      @include('students._form')
    </form>
  </div>
@endsection
