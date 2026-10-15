@extends('layouts.app', ['title' => 'Peminjaman'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Peminjaman</h2>
        <p class="page-subtitle">Pencatatan transaksi peminjaman buku perpustakaan sekolah.</p>
      </div>
      <a href="{{ route('loans.create') }}" class="rounded-lg bg-school-800 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-school-900">Catat Peminjaman</a>
    </section>

    @include('components.flash-message')

    <section class="admin-card">
      <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 md:flex-row md:items-center md:justify-between">
        <input type="search" class="block w-full max-w-md rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Cari kode, siswa, atau buku">
        <p class="text-sm text-slate-500">Data terbaru transaksi peminjaman.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Kode</th>
              <th class="px-4 py-3">Siswa</th>
              <th class="px-4 py-3">Petugas</th>
              <th class="px-4 py-3">Buku</th>
              <th class="px-4 py-3">Tanggal Pinjam</th>
              <th class="px-4 py-3">Jatuh Tempo</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Keterlambatan</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($loans as $loan)
              @php($lateDays = $loanStatusService->lateDays($loan))
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $loan->loan_code }}</td>
                <td class="table-cell">{{ $loan->student?->name ?? '-' }}</td>
                <td class="table-cell">{{ $loan->user?->name ?? '-' }}</td>
                <td class="table-cell">{{ $loan->items->pluck('book.title')->filter()->join(', ') ?: '-' }}</td>
                <td class="table-cell">{{ $loan->loan_date?->format('d M Y') }}</td>
                <td class="table-cell">{{ $loan->due_date?->format('d M Y') }}</td>
                <td class="table-cell">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $loanStatusService->badgeClass($loan) }}">{{ $loanStatusService->displayLabel($loan) }}</span>
                </td>
                <td class="table-cell">{{ $lateDays > 0 ? $lateDays.' hari' : '-' }}</td>
                <td class="table-cell text-right">
                  <a href="{{ route('loans.show', $loan) }}" class="text-sm font-medium text-school-800 hover:text-school-900">Detail</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Belum ada data peminjaman.</p>
                  <p class="mt-1 text-sm text-slate-500">Klik Catat Peminjaman untuk membuat transaksi baru.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if (method_exists($loans, 'hasPages') && $loans->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $loans->links() }}
        </div>
      @endif
    </section>
  </div>
@endsection
