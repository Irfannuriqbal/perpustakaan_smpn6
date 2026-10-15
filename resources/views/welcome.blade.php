@extends('layouts.auth', ['title' => 'Selamat Datang'])

@section('content')
  <div class="admin-card max-w-lg p-8 text-center">
    <img src="{{ asset('images/logo-smpn-6.jpg') }}" alt="Logo SMP Negeri 6 Bandung" class="mx-auto mb-4 h-20 w-20 rounded-2xl object-contain shadow-soft">
    <h1 class="text-2xl font-semibold text-slate-900">Perpustakaan SMP Negeri 6 Bandung</h1>
    <p class="mt-3 text-sm text-slate-600">Silakan masuk untuk mengakses sistem administrasi perpustakaan.</p>
    <a href="{{ route('login') }}" class="mt-6 inline-flex rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Masuk</a>
  </div>
@endsection
