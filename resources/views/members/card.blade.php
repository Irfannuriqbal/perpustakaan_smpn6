<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Kartu Digital - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

  @include('members._nav')

  <main class="mx-auto w-full max-w-lg px-4 py-6 sm:py-10">

    @include('components.flash-message')

    <div class="mb-4 flex justify-end">
      <a href="{{ route('members.portal-card.print') }}" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">
        Cetak Kartu Anggota
      </a>
    </div>

    {{-- Kartu Digital --}}
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-school-700 to-school-900 p-6 text-white shadow-xl sm:p-8">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-school-100">Kartu Anggota Perpustakaan</p>
      <div class="mt-4 flex items-center gap-4">
        @if ($photoUrl)
          <img src="{{ $photoUrl }}" alt="Foto {{ $member->name }}" class="h-20 w-20 rounded-2xl object-cover ring-2 ring-white/60">
        @else
          <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/15 text-2xl font-bold ring-2 ring-white/30">
            {{ strtoupper(substr($member->name, 0, 1)) }}
          </div>
        @endif
        <div>
          <h2 class="text-2xl font-bold leading-tight">{{ $member->name }}</h2>
          <p class="mt-1 text-sm text-school-100">{{ $member->member_type }}</p>
        </div>
      </div>

      <dl class="mt-5 grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
        <div>
          <dt class="text-school-200">No. Anggota</dt>
          <dd class="mt-1 font-mono font-semibold">{{ $member->no }}</dd>
        </div>
        @if ($member->class)
          <div>
            <dt class="text-school-200">Kelas</dt>
            <dd class="mt-1 font-semibold">{{ $member->class }}</dd>
          </div>
        @endif
        <div>
          <dt class="text-school-200">Status</dt>
          <dd class="mt-1 font-semibold capitalize">{{ $member->status }}</dd>
        </div>
      </dl>

      {{-- QR Code --}}
      <div class="mt-6 rounded-2xl bg-white p-4 text-center text-slate-900 shadow-inner">
        <div class="mx-auto w-44 max-w-full" aria-label="QR Code {{ $member->member_code }}">{!! $qrCode !!}</div>
        <p class="mt-2 text-sm font-bold tracking-widest">{{ $member->member_code }}</p>
        <p class="mt-1 text-xs text-slate-500">Tunjukkan QR ini kepada petugas perpustakaan.</p>
      </div>
    </section>

    {{-- Ringkasan Pinjaman --}}
    <section class="mt-6 grid gap-4 sm:grid-cols-2">
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Buku Dipinjam</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $activeItemsCount }}</p>
        <p class="mt-1 text-xs text-slate-400">buku aktif</p>
      </article>
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Jatuh Tempo</p>
        <p class="mt-2 text-xl font-bold text-slate-900">{{ $nearestDueDate?->format('d M Y') ?? '-' }}</p>
        <p class="mt-1 text-xs text-slate-400">{{ $nearestDueDate ? 'Segera kembalikan!' : 'Tidak ada pinjaman aktif.' }}</p>
      </article>
    </section>

    {{-- Buku Sedang Dipinjam --}}
    <section class="admin-card mt-6 overflow-hidden">
      <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-base font-semibold text-slate-900">Buku yang Dipinjam</h2>
      </div>
      <div class="divide-y divide-slate-100">
        @forelse ($loans as $loan)
          @foreach ($loan->items->whereNull('returned_at') as $item)
            <article class="px-5 py-4">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <p class="font-semibold text-slate-900">{{ $item->book?->title ?? '-' }}</p>
                  <p class="mt-1 text-sm text-slate-500">{{ $item->book?->author ?? '-' }}</p>
                </div>
                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $loanStatusService->badgeClass($loan) }}">
                  {{ $loanStatusService->displayLabel($loan) }}
                </span>
              </div>
              <p class="mt-2 text-sm text-slate-600">
                Jatuh tempo: <span class="font-semibold text-slate-900">{{ $loan->due_date?->format('d M Y') ?? '-' }}</span>
              </p>
            </article>
          @endforeach
        @empty
          <p class="px-5 py-8 text-center text-sm text-slate-500">Belum ada buku yang sedang dipinjam.</p>
        @endforelse
      </div>
    </section>

    {{-- Ganti Password --}}
    <section class="admin-card mt-6 p-5">
      <h2 class="text-base font-semibold text-slate-900">Ganti Password</h2>
      <p class="mt-1 text-sm text-slate-500">Gunakan password yang hanya kamu ketahui.</p>
      <form action="{{ route('members.password.update') }}" method="POST" class="mt-5 space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label for="current_password" class="block text-sm font-medium text-slate-700">Password Saat Ini</label>
          <input id="current_password" name="current_password" type="password"
            class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('current_password') border-red-300 @enderror"
            autocomplete="current-password" required>
          @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
            <input id="password" name="password" type="password"
              class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('password') border-red-300 @enderror"
              autocomplete="new-password" required>
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
              class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700"
              autocomplete="new-password" required>
          </div>
        </div>
        <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">
          Simpan Password
        </button>
      </form>
    </section>

  </main>
</body>
</html>
