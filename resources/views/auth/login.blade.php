@extends('layouts.auth', ['title' => 'Login'])

@section('content')
  <section class="w-full max-w-md">
    <div class="mb-6 text-center">
      <img src="{{ asset('images/logo-smpn-6.jpg') }}" alt="Logo SMP Negeri 6 Bandung" class="mx-auto mb-4 h-20 w-20 rounded-2xl object-contain shadow-soft">
      <h1 class="text-2xl font-semibold text-slate-900">Sistem Informasi Perpustakaan</h1>
      <p class="mt-2 text-sm text-slate-600">SMP Negeri 6 Bandung</p>
    </div>

    <div class="admin-card p-6 sm:p-8">
      <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-900">Masuk ke akun perpustakaan</h2>
        <p class="mt-1 text-sm text-slate-500">Gunakan akun super admin atau admin perpustakaan.</p>
      </div>

      @include('components.flash-message')

      <form class="mt-5 space-y-5" action="{{ route('login.store') }}" method="POST">
        @csrf
        <div>
          <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 block w-full rounded-lg text-sm shadow-sm focus:border-school-700 focus:ring-school-700 {{ $errors->has('email') ? 'border-red-300' : 'border-slate-300' }}" placeholder="admin@smpn6bandung.sch.id" autocomplete="email" autofocus required>
          @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
          <input id="password" name="password" type="password" class="mt-2 block w-full rounded-lg text-sm shadow-sm focus:border-school-700 focus:ring-school-700 {{ $errors->has('password') ? 'border-red-300' : 'border-slate-300' }}" placeholder="Masukkan password" autocomplete="current-password" required>
          @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-lg bg-school-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-school-900 focus:outline-none focus:ring-2 focus:ring-school-700 focus:ring-offset-2">Masuk</button>
      </form>
    </div>

    <p class="mt-5 text-center text-sm text-slate-600">Anggota perpustakaan? <a href="{{ route('members.login') }}" class="font-semibold text-school-800 hover:text-school-900">Buka kartu digital</a>.</p>
  </section>
@endsection
