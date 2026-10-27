@extends('layouts.app', ['title' => 'Data Siswa'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Data Siswa</h2>
        <p class="page-subtitle">Kelola data anggota perpustakaan dari siswa SMP Negeri 6 Bandung.</p>
      </div>
      <div class="flex flex-col gap-2 sm:flex-row">
        <a href="{{ route('students.cards.print', request()->query()) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak Kartu</a>
        <a href="{{ route('students.create') }}" class="rounded-lg bg-school-800 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-school-900">Tambah Siswa</a>
      </div>
    </section>

    @include('components.flash-message')

    <section class="admin-card">
      <form method="GET" action="{{ route('students.index') }}" class="grid gap-3 border-b border-slate-200 px-5 py-4 lg:grid-cols-[1fr_180px_160px_auto]">
        <div>
          <label for="search" class="sr-only">Cari siswa</label>
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
          <label for="status" class="sr-only">Filter status</label>
          <select id="status" name="status" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
            <option value="">Semua status</option>
            <option value="aktif" @selected($status === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
          </select>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Filter</button>
          <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">NIS</th>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Nomor HP</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($students as $student)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $student->nis }}</td>
                <td class="table-cell">{{ $student->name }}</td>
                <td class="table-cell">{{ $student->class }}</td>
                <td class="table-cell">{{ $student->phone ?? '-' }}</td>
                <td class="table-cell">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $student->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ ucfirst($student->status) }}</span>
                </td>
                <td class="table-cell">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('students.show', $student) }}" class="text-sm font-medium text-school-800 hover:text-school-900">Detail</a>
                    <a href="{{ route('students.card.print', $student) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Cetak Kartu</a>
                    <a href="{{ route('students.edit', $student) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Edit</a>
                    <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Hapus data siswa ini? Data yang sudah terhubung peminjaman/kunjungan tidak dapat dihapus.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Data siswa tidak ditemukan.</p>
                  <p class="mt-1 text-sm text-slate-500">Ubah filter pencarian atau tambahkan data siswa baru.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($students->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $students->links() }}
        </div>
      @endif
    </section>
  </div>
@endsection
