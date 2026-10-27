@extends('layouts.app', ['title' => 'Detail Anggota'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Detail Anggota</h2>
        <p class="page-subtitle">Informasi lengkap anggota perpustakaan.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('members.card.print', $member) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak Kartu</a>
        <a href="{{ route('members.edit', $member) }}" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Edit</a>
        <a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
      </div>
    </section>

    <section class="admin-card p-5">
      <div class="flex items-center gap-5">
        @if ($member->photo_path)
          <img src="{{ asset('storage/'.$member->photo_path) }}" alt="Foto {{ $member->name }}"
            class="h-20 w-20 rounded-2xl object-cover ring-1 ring-slate-200">
        @else
          <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-2xl font-bold text-slate-500">
            {{ strtoupper(substr($member->name, 0, 1)) }}
          </div>
        @endif
        <div>
          <h3 class="text-xl font-bold text-slate-900">{{ $member->name }}</h3>
          <p class="mt-1 text-sm text-slate-500">{{ $member->member_type }} &bull; {{ $member->member_code }}</p>
        </div>
      </div>

      <dl class="mt-6 grid gap-4 md:grid-cols-2">
        <div>
          <dt class="text-sm font-medium text-slate-500">No. Anggota</dt>
          <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $member->no }}</dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-slate-500">Kode Anggota</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $member->member_code }}</dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-slate-500">Jenis Anggota</dt>
          <dd class="mt-1">
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1
              {{ $member->member_type === 'Siswa/i' ? 'bg-blue-50 text-blue-800 ring-blue-200' : 'bg-amber-50 text-amber-800 ring-amber-200' }}">
              {{ $member->member_type }}
            </span>
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-slate-500">Jenis Kelamin</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $member->gender ?? '-' }}</dd>
        </div>
        @if ($member->class)
          <div>
            <dt class="text-sm font-medium text-slate-500">Kelas</dt>
            <dd class="mt-1 text-sm text-slate-900">{{ $member->class }}</dd>
          </div>
        @endif
        <div>
          <dt class="text-sm font-medium text-slate-500">Nomor HP</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $member->phone ?? '-' }}</dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-slate-500">Status</dt>
          <dd class="mt-1">
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1
              {{ $member->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">
              {{ ucfirst($member->status) }}
            </span>
          </dd>
        </div>
        <div class="md:col-span-2">
          <dt class="text-sm font-medium text-slate-500">Alamat</dt>
          <dd class="mt-1 text-sm text-slate-900">{{ $member->address ?? '-' }}</dd>
        </div>
      </dl>
    </section>
  </div>
@endsection
