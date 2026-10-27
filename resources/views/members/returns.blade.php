<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Pengembalian Buku - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

  @include('members._nav')

  <main class="mx-auto w-full max-w-2xl px-4 py-6 sm:py-10">

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Pengembalian Buku</h1>
      <p class="mt-1 text-sm text-slate-500">Pilih buku yang ingin kamu kembalikan.</p>
    </div>

    @include('components.flash-message')

    {{-- Notifikasi terlambat --}}
    @php
      $hasLate = $activeLoans->contains(fn($l) => $l->status === 'terlambat');
    @endphp
    @if ($hasLate)
      <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
        <p class="text-sm font-semibold text-red-800">Perhatian: Kamu memiliki buku terlambat.</p>
        <p class="mt-1 text-sm text-red-700">Segera kembalikan untuk menghindari sanksi dan agar bisa meminjam lagi.</p>
      </div>
    @endif

    @forelse ($activeLoans as $loan)
      @php
        $lateDays = $loanStatusService->lateDays($loan);
        $badge    = $loanStatusService->badgeClass($loan);
        $label    = $loanStatusService->displayLabel($loan);
      @endphp
      <article class="admin-card mb-4 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-5 py-3">
          <div class="flex items-center justify-between gap-4">
            <p class="text-xs font-mono text-slate-500">{{ $loan->loan_code }}</p>
            <span class="rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $badge }}">{{ $label }}</span>
          </div>
          <p class="mt-1 text-sm text-slate-700">
            Jatuh Tempo: <strong class="{{ $loan->status === 'terlambat' ? 'text-red-700' : '' }}">
              {{ $loan->due_date?->format('d M Y') }}
            </strong>
            @if ($lateDays > 0)
              <span class="ml-2 text-xs font-semibold text-red-600">({{ $lateDays }} hari terlambat)</span>
            @endif
          </p>
        </div>

        <div class="divide-y divide-slate-100">
          @foreach ($loan->items->whereNull('returned_at') as $item)
            <div class="flex items-center justify-between gap-4 px-5 py-4">
              <div class="min-w-0">
                <p class="font-semibold text-slate-900 leading-snug">{{ $item->book?->title ?? '-' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $item->book?->author ?? '-' }}</p>
                @if ($item->book?->book_code)
                  <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $item->book->book_code }}</p>
                @endif
              </div>
              <form action="{{ route('members.returns.store', $item) }}" method="POST"
                onsubmit="return confirm('Konfirmasi pengembalian buku &quot;{{ addslashes($item->book?->title ?? '') }}&quot;?\n\nPastikan kamu sudah menyerahkan buku secara fisik kepada petugas.');">
                @csrf
                <button type="submit"
                  class="shrink-0 rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-800">
                  Kembalikan
                </button>
              </form>
            </div>
          @endforeach
        </div>
      </article>
    @empty
      <div class="admin-card py-16 text-center">
        <svg class="mx-auto mb-3 h-10 w-10 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        <p class="font-medium text-slate-700">Tidak ada buku yang perlu dikembalikan.</p>
        <p class="mt-1 text-sm text-slate-500">Semua pinjaman sudah dikembalikan atau belum ada pinjaman aktif.</p>
      </div>
    @endforelse

  </main>
</body>
</html>
