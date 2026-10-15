@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
  <div class="space-y-6">
    <section>
      <h2 class="page-title">Dashboard Admin/Petugas</h2>
      <p class="page-subtitle">Ringkasan aktivitas perpustakaan sekolah berdasarkan data terbaru.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      @foreach ($summaryCards as $card)
        <article class="admin-card p-5">
          <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
          <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $card['value'] }}</p>
          <p class="mt-2 text-sm text-slate-500">{{ $card['note'] }}</p>
        </article>
      @endforeach
    </section>

    <section class="admin-card">
      <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h3 class="text-base font-semibold text-slate-900">Peminjaman Terbaru</h3>
          <p class="mt-1 text-sm text-slate-500">5 transaksi peminjaman terbaru dari database.</p>
        </div>
        <a href="{{ route('loans.index') }}" class="text-sm font-medium text-school-800 hover:text-school-900">Lihat semua</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Judul Buku</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Keterlambatan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($recentLoans as $loan)
              <tr>
                <td class="table-cell font-medium text-slate-900">{{ $loan['student'] }}</td>
                <td class="table-cell">{{ $loan['class'] }}</td>
                <td class="table-cell">{{ $loan['book'] }}</td>
                <td class="table-cell">{{ $loan['date'] }}</td>
                <td class="table-cell">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $loan['status_class'] }}">{{ $loan['status_label'] }}</span>
                </td>
                <td class="table-cell">{{ $loan['late_days'] > 0 ? $loan['late_days'].' hari' : '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data peminjaman.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
      <article class="admin-card">
        <div class="border-b border-slate-200 px-5 py-4">
          <h3 class="text-base font-semibold text-slate-900">Siswa Sedang Terlambat</h3>
          <p class="mt-1 text-sm text-slate-500">5 siswa dengan pinjaman aktif paling lama melewati jatuh tempo.</p>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="table-head">
              <tr>
                <th class="px-4 py-3">Siswa</th>
                <th class="px-4 py-3">Kelas</th>
                <th class="px-4 py-3">Buku</th>
                <th class="px-4 py-3">Jatuh Tempo</th>
                <th class="px-4 py-3">Terlambat</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              @forelse ($lateStudentsList as $student)
                <tr>
                  <td class="table-cell font-medium text-slate-900">{{ $student['student'] }}</td>
                  <td class="table-cell">{{ $student['class'] }}</td>
                  <td class="table-cell">{{ $student['book'] }}</td>
                  <td class="table-cell">{{ $student['due_date'] }}</td>
                  <td class="table-cell"><span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800 ring-1 ring-amber-200">{{ $student['late_days'] }} hari</span></td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Tidak ada siswa terlambat.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </article>

      <article class="admin-card">
        <div class="border-b border-slate-200 px-5 py-4">
          <h3 class="text-base font-semibold text-slate-900">Buku Paling Sering Dipinjam</h3>
          <p class="mt-1 text-sm text-slate-500">5 buku dengan jumlah peminjaman terbanyak.</p>
        </div>
        <div class="divide-y divide-slate-100">
          @forelse ($popularBooks as $book)
            <div class="flex items-center justify-between gap-4 px-5 py-4">
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ $book['title'] }}</p>
                <p class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">{{ $book['book_code'] }}</p>
              </div>
              <span class="rounded-full bg-school-50 px-3 py-1 text-sm font-semibold text-school-800 ring-1 ring-school-200">{{ $book['loans_count'] }} kali</span>
            </div>
          @empty
            <div class="px-5 py-8 text-center text-sm text-slate-500">Belum ada data peminjaman buku.</div>
          @endforelse
        </div>
      </article>
    </section>

    <section class="admin-card p-5">
      <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h3 class="text-base font-semibold text-slate-900">Statistik Kunjungan 7 Hari Terakhir</h3>
          <p class="mt-1 text-sm text-slate-500">Ringkasan kunjungan harian siswa ke perpustakaan.</p>
        </div>
      </div>

      <div class="mt-5 grid gap-3 md:grid-cols-7">
        @foreach ($visitStats as $day)
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
            <div class="flex items-center justify-between gap-2">
              <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $day['day'] }}</p>
              <p class="text-xs text-slate-500">{{ $day['date'] }}</p>
            </div>
            <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $day['total'] }}</p>
            <div class="mt-3 h-2 rounded-full bg-slate-200">
              <div class="h-2 rounded-full bg-school-700" style="width: {{ $day['percentage'] }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    </section>
  </div>
@endsection
