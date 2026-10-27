@extends('layouts.app', ['title' => 'Edit Anggota'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Edit Anggota</h2>
        <p class="page-subtitle">Perbarui data anggota perpustakaan.</p>
      </div>
      <a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
    </section>

    @include('components.flash-message')

    <section class="admin-card p-5 sm:p-6">
      <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('anggota._form')
      </form>
    </section>
  </div>
@endsection
