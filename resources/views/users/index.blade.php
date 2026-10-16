@extends('layouts.app', ['title' => 'Akun Admin'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h2 class="page-title">Akun Admin</h2>
        <p class="page-subtitle">Kelola akun petugas perpustakaan. Hanya super admin yang dapat membuka halaman ini.</p>
      </div>
      <a href="{{ route('users.create') }}" class="rounded-lg bg-school-800 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-school-900">Tambah Admin</a>
    </section>

    @include('components.flash-message')

    <section class="admin-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($admins as $admin)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $admin->name }}</td>
                <td class="table-cell">{{ $admin->email }}</td>
                <td class="table-cell">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $admin->is_active ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ $admin->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </td>
                <td class="table-cell">
                  <div class="flex justify-end gap-3">
                    <a href="{{ route('users.edit', $admin) }}" class="text-sm font-semibold text-school-800 hover:text-school-900">Edit</a>
                    <form action="{{ route('users.status', $admin) }}" method="POST">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="text-sm font-semibold text-slate-700 hover:text-slate-900">{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <form action="{{ route('users.destroy', $admin) }}" method="POST" onsubmit="return confirm('Hapus akun admin ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-800">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada akun admin.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($admins->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">{{ $admins->links() }}</div>
      @endif
    </section>
  </div>
@endsection
