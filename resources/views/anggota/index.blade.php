@extends('layouts.app', ['title' => 'Data Anggota'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Data Anggota</h2>
        <p class="page-subtitle">Kelola data anggota perpustakaan — siswa dan guru/staf SMP Negeri 6 Bandung.</p>
      </div>
      <div class="flex flex-col gap-2 sm:flex-row">
        <a href="{{ route('members.cards.print', request()->query()) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak Kartu</a>
        <a href="{{ route('members.create') }}" class="rounded-lg bg-school-800 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-school-900">+ Tambah Anggota</a>
      </div>
    </section>

    @include('components.flash-message')

    <section class="admin-card">
      <form method="GET" action="{{ route('members.index') }}" class="grid gap-3 border-b border-slate-200 px-5 py-4 lg:grid-cols-[1fr_180px_180px_160px_auto]">
        <div>
          <label for="search" class="sr-only">Cari anggota</label>
          <input id="search" name="search" type="search" value="{{ $search }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Cari nama, no. anggota, atau kode">
        </div>
        <div>
          <label for="member_type" class="sr-only">Filter jenis</label>
          <select id="member_type" name="member_type" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
            <option value="">Semua jenis</option>
            <option value="Siswa/i"   @selected($memberType === 'Siswa/i')>Siswa/i</option>
            <option value="Guru/Staf" @selected($memberType === 'Guru/Staf')>Guru/Staf</option>
          </select>
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
            <option value="aktif"    @selected($status === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
          </select>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Filter</button>
          <a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3 text-left">No. Anggota</th>
              <th class="px-4 py-3 text-left">Nama</th>
              <th class="px-4 py-3 text-left">Jenis</th>
              <th class="px-4 py-3 text-left">Kelas</th>
              <th class="px-4 py-3 text-left">Nomor HP</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($members as $member)
              <tr>
                <td class="table-cell font-mono text-sm font-medium text-slate-900">{{ $member->no }}</td>
                <td class="table-cell font-medium text-slate-900">{{ $member->name }}</td>
                <td class="table-cell">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1
                    {{ $member->member_type === 'Siswa/i' ? 'bg-blue-50 text-blue-800 ring-blue-200' : 'bg-amber-50 text-amber-800 ring-amber-200' }}">
                    {{ $member->member_type }}
                  </span>
                </td>
                <td class="table-cell text-slate-600">{{ $member->class ?? '-' }}</td>
                <td class="table-cell text-slate-600">{{ $member->phone ?? '-' }}</td>
                <td class="table-cell">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1
                    {{ $member->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                    {{ ucfirst($member->status) }}
                  </span>
                </td>
                <td class="table-cell">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('members.show', $member) }}" class="text-sm font-medium text-school-800 hover:text-school-900">Detail</a>
                    <a href="{{ route('members.card.print', $member) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Cetak Kartu</a>
                    <a href="{{ route('members.edit', $member) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Edit</a>
                    <form action="{{ route('members.destroy', $member) }}" method="POST"
                      onsubmit="return confirm('Hapus data anggota ini? Data yang masih terhubung peminjaman/kunjungan tidak dapat dihapus.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Data anggota tidak ditemukan.</p>
                  <p class="mt-1 text-sm text-slate-500">Ubah filter pencarian atau tambahkan data anggota baru.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($members->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $members->links() }}
        </div>
      @endif
    </section>
  </div>
@endsection
