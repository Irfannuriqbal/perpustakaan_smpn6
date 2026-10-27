<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Peminjaman Saya - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

  @include('members._nav')

  <main class="mx-auto w-full max-w-3xl px-4 py-6 sm:py-10">

    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Peminjaman Saya</h1>
        <p class="mt-1 text-sm text-slate-500">Riwayat dan status peminjaman buku kamu.</p>
      </div>
      <a href="{{ route('members.catalog') }}" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Cari Buku</a>
    </div>

    @include('components.flash-message')

    {{-- Notifikasi keterlambatan --}}
    @php
      $overdueLoans = $loans->filter(fn($l) => $l->status === 'terlambat' && is_null($l->returned_at));
    @endphp
    @if ($overdueLoans->isNotEmpty())
      <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
        <div class="flex items-start gap-3">
          <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div>
            <p class="text-sm font-semibold text-red-800">Kamu memiliki {{ $overdueLoans->count() }} pinjaman terlambat!</p>
            <p class="mt-1 text-sm text-red-700">Segera kembalikan buku agar bisa meminjam lagi. Hubungi petugas perpustakaan.</p>
          </div>
        </div>
      </div>
    @endif

    {{-- Filter --}}
    <div class="mb-4 flex gap-2">
      <a href="{{ route('members.loans', ['filter' => 'aktif']) }}"
        class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $filter === 'aktif' ? 'bg-school-800 text-white' : 'border border-slate-300 text-slate-600 hover:bg-white' }}">
        Aktif
      </a>
      <a href="{{ route('members.loans', ['filter' => 'semua']) }}"
        class="rounded-full px-4 py-1.5 text-sm font-semibold {{ $filter === 'semua' ? 'bg-school-800 text-white' : 'border border-slate-300 text-slate-600 hover:bg-white' }}">
        Semua Riwayat
      </a>
    </div>

    {{-- Daftar Pinjaman --}}
    @forelse ($loans as $loan)
      @php
        $badge       = $loanStatusService->badgeClass($loan);
        $label       = $loanStatusService->displayLabel($loan);
        $lateDays    = $loanStatusService->lateDays($loan);
        $canExtend   = $extensionService->canExtend($loan);
      @endphp
      <article class="admin-card mb-4 overflow-hidden">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
          <div>
            <p class="text-xs font-mono text-slate-500">{{ $loan->loan_code }}</p>
            <p class="mt-1 text-sm text-slate-700">
              Pinjam: <strong>{{ $loan->loan_date?->format('d M Y') }}</strong> &rarr;
              Jatuh Tempo: <strong class="{{ $loan->status === 'terlambat' ? 'text-red-700' : '' }}">{{ $loan->due_date?->format('d M Y') }}</strong>
            </p>
            @if ($lateDays > 0 && $loan->status !== 'dikembalikan')
              <p class="mt-1 text-xs font-semibold text-red-600">Terlambat {{ $lateDays }} hari</p>
            @endif
          </div>
          <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $badge }}">{{ $label }}</span>
        </div>

        <div class="divide-y divide-slate-50 px-5">
          @foreach ($loan->items as $item)
            <div class="flex items-center justify-between gap-4 py-3">
              <div class="min-w-0">
                <p class="truncate font-medium text-slate-900">{{ $item->book?->title ?? '-' }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ $item->book?->author ?? '-' }}</p>
                @if ($item->returned_at)
                  <p class="mt-0.5 text-xs text-emerald-600">Dikembalikan: {{ \Carbon\Carbon::parse($item->returned_at)->format('d M Y') }}</p>
                @endif
              </div>
            </div>
          @endforeach
        </div>

        @if (is_null($loan->returned_at))
          <div class="flex items-center gap-3 border-t border-slate-100 bg-slate-50 px-5 py-3">
            @if ($canExtend)
              <form action="{{ route('members.loans.extend', $loan) }}" method="POST"
                onsubmit="return confirm('Perpanjang pinjaman ini 7 hari?');">
                @csrf
                <button type="submit" class="rounded-lg border border-school-700 px-3 py-1.5 text-xs font-semibold text-school-800 hover:bg-school-50">
                  Perpanjang (+ 7 hari)
                </button>
              </form>
            @elseif ($loan->extension_count >= 1)
              <span class="text-xs text-slate-400">Sudah diperpanjang 1x</span>
            @elseif ($loan->status === 'terlambat')
              <span class="text-xs text-red-500">Tidak bisa diperpanjang (terlambat)</span>
            @endif
            <a href="{{ route('members.returns') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
              Kembalikan &rarr;
            </a>
          </div>
        @endif
      </article>
    @empty
      <div class="admin-card py-16 text-center">
        <p class="font-medium text-slate-700">
          {{ $filter === 'aktif' ? 'Tidak ada pinjaman aktif.' : 'Belum ada riwayat peminjaman.' }}
        </p>
        <p class="mt-1 text-sm text-slate-500">
          {{ $filter === 'aktif' ? 'Kunjungi perpustakaan untuk meminjam buku.' : '' }}
        </p>
      </div>
    @endforelse

    @if ($loans->hasPages())
      <div class="mt-4">{{ $loans->links() }}</div>
    @endif

  </main>
</body>
</html>
