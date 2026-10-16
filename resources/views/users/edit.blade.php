@extends('layouts.app', ['title' => 'Edit Admin'])

@section('content')
  <div class="mx-auto max-w-2xl space-y-6">
    <section>
      <h2 class="page-title">Edit Akun Admin</h2>
      <p class="page-subtitle">Ubah data atau reset password petugas perpustakaan.</p>
    </section>
    <form action="{{ route('users.update', $user) }}" method="POST" class="admin-card p-5">
      @method('PUT')
      @include('users._form')
    </form>
  </div>
@endsection
