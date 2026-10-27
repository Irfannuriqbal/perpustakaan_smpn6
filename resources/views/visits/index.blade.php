@extends('layouts.app', ['title' => 'Kunjungan'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Kunjungan</h2>
        <p class="page-subtitle">Catatan kunjungan siswa ke perpustakaan berdasarkan scan kode anggota.</p>
      </div>
      <a href="{{ route('visits.kiosk') }}" class="rounded-lg border border-school-800 px-4 py-2 text-center text-sm font-semibold text-school-800 hover:bg-school-50">Buka Layar Scan</a>
    </section>

    @include('components.flash-message')

    <section class="grid gap-6 xl:grid-cols-[360px_1fr]">
      <div class="admin-card p-5">
        <h3 class="text-base font-semibold text-slate-900">Scan Kode Anggota</h3>
        <p class="mt-1 text-sm text-slate-500">Masukkan atau scan <span class="font-medium text-slate-700">member_code</span> pada kartu siswa, lalu tekan Enter.</p>

        <form action="{{ route('visits.store') }}" method="POST" class="mt-4 space-y-3">
          @csrf
          <div>
            <label for="member_code" class="sr-only">Kode anggota</label>
            <input id="member_code" name="member_code" type="text" value="{{ old('member_code') }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Contoh: AGT-2026-001" autocomplete="off" autofocus>
            @error('member_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
          <button type="submit" class="w-full rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Catat Kunjungan</button>
        </form>

        <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-900">
          Sistem menolak pencatatan ulang siswa yang sama dalam waktu kurang dari 5 menit.
        </div>
      </div>

      <div class="admin-card p-5">
        <h3 class="text-base font-semibold text-slate-900">Ringkasan Hari Ini</h3>
        <p class="mt-1 text-sm text-slate-500">Data kunjungan tersimpan otomatis dari scan kode anggota.</p>
        <dl class="mt-5 grid gap-4 sm:grid-cols-3">
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Tanggal</dt>
            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ now()->format('d M Y') }}</dd>
          </div>
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Mode Input</dt>
            <dd class="mt-1 text-sm font-semibold text-slate-900">Keyboard / Scanner USB</dd>
          </div>
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Validasi</dt>
            <dd class="mt-1 text-sm font-semibold text-slate-900">Backend</dd>
          </div>
        </dl>
      </div>
    </section>

    <section class="admin-card">
      <form method="GET" action="{{ route('visits.index') }}" class="grid gap-3 border-b border-slate-200 px-5 py-4 lg:grid-cols-[1fr_180px_180px_auto]">
        <div>
          <label for="search" class="sr-only">Cari kunjungan</label>
          <input id="search" name="search" type="search" value="{{ $search }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Cari nama atau NIS">
        </div>
        <div>
          <label for="class" class="sr-only">Filter kelas</label>
          <select id="class" name="class" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
            <option value="">Semua kelas</option>
            @foreach ($classes as $classOption)
              <option value="{{ $classOption }}" @selected($class === $classOption)>{{ $classOption }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="visit_date" class="sr-only">Filter tanggal</label>
          <input id="visit_date" name="visit_date" type="date" value="{{ $visitDate }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
        </div>
        <div class="flex gap-2">
          <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Filter</button>
          <a href="{{ route('visits.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Jam Masuk</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($visits as $visit)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $visit->student?->name ?? '-' }}</td>
                <td class="table-cell">{{ $visit->student?->nis ?? '-' }}</td>
                <td class="table-cell">{{ $visit->student?->class ?? '-' }}</td>
                <td class="table-cell">{{ $visit->visit_date?->format('d M Y') }}</td>
                <td class="table-cell">{{ substr($visit->check_in_time, 0, 5) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Data kunjungan tidak ditemukan.</p>
                  <p class="mt-1 text-sm text-slate-500">Scan kode anggota atau ubah filter pencarian.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if (method_exists($visits, 'hasPages') && $visits->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $visits->links() }}
        </div>
      @endif
    </section>
  </div>
@endsection
