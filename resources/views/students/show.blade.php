@extends('layouts.app', ['title' => 'Detail Siswa'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Detail Siswa</h2>
        <p class="page-subtitle">Informasi lengkap anggota perpustakaan siswa.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('students.card.print', $student) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak Kartu</a>
        <a href="{{ route('students.edit', $student) }}" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Edit</a>
        <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
      </div>
    </section>

    <section class="admin-card p-5">
      <dl class="grid gap-5 md:grid-cols-2">
        <div><dt class="text-sm font-medium text-slate-500">NIS</dt><dd class="mt-1 text-sm font-semibold text-slate-900">{{ $student->nis }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Kode Anggota</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->member_code }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Nama</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->name }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Jenis Kelamin</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->gender ?? '-' }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Kelas</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->class }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Nomor HP</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->phone ?? '-' }}</dd></div>
        <div><dt class="text-sm font-medium text-slate-500">Status</dt><dd class="mt-1"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $student->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($student->status) }}</span></dd></div>
        <div class="md:col-span-2"><dt class="text-sm font-medium text-slate-500">Alamat</dt><dd class="mt-1 text-sm text-slate-900">{{ $student->address ?? '-' }}</dd></div>
      </dl>
    </section>
  </div>
@endsection
