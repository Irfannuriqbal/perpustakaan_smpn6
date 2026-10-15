@extends('layouts.app', ['title' => 'Pengaturan'])

@section('content')
  <div class="space-y-6">
    <section>
      <h2 class="page-title">Pengaturan</h2>
      <p class="page-subtitle">Konfigurasi dasar sistem perpustakaan sekolah.</p>
    </section>

    <section class="admin-card p-5">
      <div class="grid gap-5 md:grid-cols-2">
        <div>
          <label for="school-name" class="block text-sm font-medium text-slate-700">Nama Sekolah</label>
          <input id="school-name" type="text" value="SMP Negeri 6 Bandung" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
        </div>
        <div>
          <label for="app-name" class="block text-sm font-medium text-slate-700">Nama Aplikasi</label>
          <input id="app-name" type="text" value="Sistem Informasi Perpustakaan" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
        </div>
      </div>
      <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
        Form ini masih tampilan awal. Penyimpanan pengaturan akan dibuat pada tahap berikutnya.
      </div>
    </section>
  </div>
@endsection
