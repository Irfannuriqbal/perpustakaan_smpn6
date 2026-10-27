<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Notifikasi Peminjaman - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
  @include('members._nav')

  <main class="mx-auto w-full max-w-4xl px-4 py-6 sm:py-10">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Notifikasi Peminjaman</h1>
      <p class="mt-1 text-sm text-slate-500">Informasi pinjaman terlambat dan buku yang segera jatuh tempo.</p>
    </div>
    @include('components.flash-message')

    @if ($overdueLoans->isNotEmpty())
      <section class="mb-6 overflow-hidden rounded-2xl border border-red-200 bg-white">
        <div class="border-b border-red-200 bg-red-50 px-5 py-4">
          <h2 class="font-bold text-red-900">Terlambat ({{ $overdueLoans->count() }})</h2>
          <p class="mt-1 text-sm text-red-800">Segera serahkan buku kepada petugas perpustakaan.</p>
        </div>
        <div class="divide-y divide-slate-100">
          @foreach ($overdueLoans as $loan)
            <article class="px-5 py-4">
              <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <p class="font-mono text-xs text-slate-500">{{ $loan->loan_code }}</p>
                  <p class="mt-1 text-sm text-slate-700">Jatuh tempo {{ $loan->due_date?->format('d M Y') ?? '-' }}</p>
                </div>
                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-800 ring-1 ring-red-200">Terlambat {{ $loanStatusService->lateDays($loan) }} hari</span>
              </div>
              <ul class="mt-3 space-y-2">
                @foreach ($loan->items->whereNull('returned_at')->whereIn('status', ['dipinjam', 'terlambat']) as $item)
                  <li class="text-sm font-medium text-slate-900">{{ $item->book?->title ?? 'Buku tidak tersedia' }}</li>
                @endforeach
              </ul>
              <a href="{{ route('members.returns') }}" class="mt-3 inline-flex rounded-lg bg-red-700 px-3 py-2 text-xs font-semibold text-white hover:bg-red-800">Buka pengembalian</a>
            </article>
          @endforeach
        </div>
      </section>
    @endif

    @if ($dueSoonLoans->isNotEmpty())
      <section class="mb-6 overflow-hidden rounded-2xl border border-amber-200 bg-white">
        <div class="border-b border-amber-200 bg-amber-50 px-5 py-4">
          <h2 class="font-bold text-amber-900">Segera jatuh tempo ({{ $dueSoonLoans->count() }})</h2>
          <p class="mt-1 text-sm text-amber-800">Jatuh tempo dalam tiga hari ke depan. Pengembalian lebih awal membantu menghindari keterlambatan.</p>
        </div>
        <div class="divide-y divide-slate-100">
          @foreach ($dueSoonLoans as $loan)
            <article class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <p class="font-mono text-xs text-slate-500">{{ $loan->loan_code }}</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Jatuh tempo {{ $loan->due_date?->format('d M Y') ?? '-' }}</p>
                <ul class="mt-2 space-y-1">
                  @foreach ($loan->items->whereNull('returned_at')->whereIn('status', ['dipinjam', 'terlambat']) as $item)
                    <li class="text-sm text-slate-600">{{ $item->book?->title ?? 'Buku tidak tersedia' }}</li>
                  @endforeach
                </ul>
              </div>
              <div class="flex shrink-0 gap-2">
                <a href="{{ route('members.loans') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Riwayat / Perpanjangan</a>
                <a href="{{ route('members.returns') }}" class="rounded-lg bg-school-800 px-3 py-2 text-xs font-semibold text-white hover:bg-school-900">Pengembalian</a>
              </div>
            </article>
          @endforeach
        </div>
      </section>
    @endif

    @if ($overdueLoans->isEmpty() && $dueSoonLoans->isEmpty())
      <section class="admin-card px-5 py-14 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700" aria-hidden="true">✓</div>
        <h2 class="mt-4 font-semibold text-slate-900">Tidak ada notifikasi keterlambatan</h2>
        <p class="mt-1 text-sm text-slate-500">Saat ini tidak ada pinjaman terlambat atau yang jatuh tempo dalam tiga hari.</p>
        <a href="{{ route('members.loans') }}" class="mt-4 inline-flex text-sm font-semibold text-school-800 hover:underline">Lihat riwayat peminjaman</a>
      </section>
    @endif
  </main>
</body>
</html>
