@extends('layouts.app', ['title' => 'Tambah Admin'])

@section('content')
  <div class="mx-auto max-w-2xl space-y-6">
    <section>
      <h2 class="page-title">Tambah Akun Admin</h2>
      <p class="page-subtitle">Buat akun untuk petugas perpustakaan.</p>
    </section>
    <form action="{{ route('users.store') }}" method="POST" class="admin-card p-5">
      @include('users._form')
    </form>
  </div>
@endsection
