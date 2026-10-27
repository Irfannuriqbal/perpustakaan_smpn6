<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Beranda Anggota - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
  @include('members._nav')

  <main class="mx-auto w-full max-w-6xl px-4 py-6 sm:py-10">
    @include('components.flash-message')

    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-school-700 to-school-900 p-6 text-white shadow-xl sm:p-9">
      <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="text-sm font-semibold text-school-100">Portal Anggota Perpustakaan SMP Negeri 6 Bandung</p>
          <h1 class="mt-2 text-3xl font-bold sm:text-4xl">Halo, {{ $member->name }}!</h1>
          <p class="mt-2 text-sm text-school-100">Kelola peminjaman, pengembalian, dan kartu anggota kamu di sini.</p>
          <p class="mt-4 inline-flex rounded-full bg-white/15 px-3 py-1 text-sm font-medium">{{ $member->member_type }} <span class="mx-2">·</span> No. {{ $member->no }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('members.loans') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-school-900 hover:bg-school-50">Riwayat Peminjaman</a>
          <a href="{{ route('members.card') }}" class="rounded-xl border border-white/40 px-4 py-2.5 text-sm font-bold text-white hover:bg-white/10">Kartu Anggota</a>
        </div>
      </div>
    </section>

    @if ($overdueLoans->isNotEmpty())
      <section class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5" role="alert">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="font-bold text-red-900">Ada {{ $overdueLoans->count() }} peminjaman terlambat</h2>
            <p class="mt-1 text-sm text-red-800">Segera serahkan buku kepada petugas perpustakaan dan catat pengembaliannya.</p>
          </div>
          <a href="{{ route('members.notifications') }}" class="shrink-0 rounded-lg bg-red-700 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-red-800">Lihat Notifikasi</a>
        </div>
      </section>
    @elseif ($dueSoonLoans->isNotEmpty())
      <section class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5" role="status">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="font-bold text-amber-900">Jatuh tempo dalam 3 hari</h2>
            <p class="mt-1 text-sm text-amber-800">Periksa jadwal pengembalian agar peminjaman tidak terlambat.</p>
          </div>
          <a href="{{ route('members.notifications') }}" class="shrink-0 rounded-lg border border-amber-300 px-4 py-2 text-center text-sm font-semibold text-amber-900 hover:bg-amber-100">Lihat Notifikasi</a>
        </div>
      </section>
    @endif

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Buku sedang dipinjam</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $activeItemsCount }}</p>
        <a href="{{ route('members.loans') }}" class="mt-3 inline-block text-sm font-semibold text-school-800 hover:underline">Lihat riwayat</a>
      </article>
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Pinjaman terlambat</p>
        <p class="mt-2 text-3xl font-bold {{ $overdueLoans->isNotEmpty() ? 'text-red-700' : 'text-slate-900' }}">{{ $overdueLoans->count() }}</p>
        <a href="{{ route('members.notifications') }}" class="mt-3 inline-block text-sm font-semibold text-school-800 hover:underline">Lihat notifikasi</a>
      </article>
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Jatuh tempo terdekat</p>
        <p class="mt-2 text-xl font-bold text-slate-900">{{ $nearestDueDate?->format('d M Y') ?? '-' }}</p>
        <a href="{{ route('members.returns') }}" class="mt-3 inline-block text-sm font-semibold text-school-800 hover:underline">Pengembalian buku</a>
      </article>
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Kode anggota</p>
        <p class="mt-2 truncate font-mono text-lg font-bold text-slate-900">{{ $member->member_code }}</p>
        <a href="{{ route('members.profile') }}" class="mt-3 inline-block text-sm font-semibold text-school-800 hover:underline">Lihat profil & QR</a>
      </article>
    </section>

    <section class="admin-card mt-6 overflow-hidden">
      <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-900">Peminjaman Aktif</h2>
          <p class="mt-1 text-sm text-slate-500">Buku yang masih tercatat dipinjam atas nama kamu.</p>
        </div>
        <a href="{{ route('members.returns') }}" class="text-sm font-semibold text-school-800 hover:underline">Ke halaman pengembalian →</a>
      </div>

      @forelse ($loans as $loan)
        <article class="border-b border-slate-100 px-5 py-4 last:border-b-0">
          <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
              <p class="font-mono text-xs text-slate-500">{{ $loan->loan_code }}</p>
              <p class="mt-1 text-sm text-slate-600">Jatuh tempo <strong>{{ $loan->due_date?->format('d M Y') ?? '-' }}</strong></p>
            </div>
            <span class="rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $loanStatusService->badgeClass($loan) }}">{{ $loanStatusService->displayLabel($loan) }}</span>
          </div>
          <ul class="mt-3 space-y-2">
            @foreach ($loan->items->whereNull('returned_at')->whereIn('status', ['dipinjam', 'terlambat']) as $item)
              <li class="flex flex-wrap items-center justify-between gap-2 text-sm">
                <span class="font-medium text-slate-800">{{ $item->book?->title ?? 'Buku tidak tersedia' }}</span>
                @if ($loanStatusService->isOverdue($loan))
                  <span class="text-xs font-semibold text-red-700">Terlambat {{ $loanStatusService->lateDays($loan) }} hari</span>
                @endif
              </li>
            @endforeach
          </ul>
        </article>
      @empty
        <div class="px-5 py-10 text-center">
          <p class="font-medium text-slate-700">Belum ada peminjaman aktif.</p>
          <p class="mt-1 text-sm text-slate-500">Kunjungi perpustakaan untuk meminjam buku.</p>
        </div>
      @endforelse
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-3">
      <a href="{{ route('members.loans') }}" class="admin-card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <h2 class="font-semibold text-slate-900">Riwayat Peminjaman</h2>
        <p class="mt-1 text-sm text-slate-500">Lihat status dan perpanjang pinjaman yang memenuhi syarat.</p>
      </a>
      <a href="{{ route('members.returns') }}" class="admin-card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <h2 class="font-semibold text-slate-900">Pengembalian Buku</h2>
        <p class="mt-1 text-sm text-slate-500">Catat pengembalian setelah buku diserahkan kepada petugas.</p>
      </a>
      <a href="{{ route('members.profile') }}" class="admin-card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <h2 class="font-semibold text-slate-900">Profil & Kartu Anggota</h2>
        <p class="mt-1 text-sm text-slate-500">Periksa data anggota, QR, dan cetak kartu perpustakaan.</p>
      </a>
    </section>
  </main>
</body>
</html>
