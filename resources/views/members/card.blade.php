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
  <main class="mx-auto w-full max-w-lg px-4 py-6 sm:py-10">
    <header class="flex items-center justify-between gap-4">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-school-800">Perpustakaan SMP Negeri 6 Bandung</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Kartu Anggota Digital</h1>
      </div>
      <form action="{{ route('members.logout') }}" method="POST">
        @csrf
        <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">Keluar</button>
      </form>
    </header>

    <section class="mt-6 overflow-hidden rounded-3xl bg-gradient-to-br from-school-700 to-school-900 p-6 text-white shadow-xl sm:p-8">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-school-100">Kartu Anggota Perpustakaan</p>
      <div class="mt-5 flex items-center gap-4">
        @if ($photoUrl)
          <img src="{{ $photoUrl }}" alt="Foto {{ $member->name }}" class="h-20 w-20 rounded-2xl object-cover ring-2 ring-white/60">
        @else
          <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/15 text-2xl font-bold ring-2 ring-white/30">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
        @endif
        <h2 class="text-3xl font-bold">{{ $member->name }}</h2>
      </div>
      <dl class="mt-5 grid grid-cols-2 gap-4 text-sm">
        <div><dt class="text-school-100">NIS</dt><dd class="mt-1 font-semibold">{{ $member->nis }}</dd></div>
        <div><dt class="text-school-100">Kelas</dt><dd class="mt-1 font-semibold">{{ $member->class }}</dd></div>
      </dl>

      <div class="mt-7 rounded-2xl bg-white p-4 text-center text-slate-900 shadow-inner">
        <div class="mx-auto w-48 max-w-full" aria-label="QR Code {{ $member->member_code }}">{!! $qrCode !!}</div>
        <p class="mt-3 text-sm font-bold tracking-widest">{{ $member->member_code }}</p>
        <p class="mt-1 text-xs text-slate-500">Scan QR ini di perpustakaan. Sama dengan QR pada kartu fisik.</p>
      </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2">
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Buku Sedang Dipinjam</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $activeItemsCount }}</p>
        <p class="mt-1 text-sm text-slate-500">buku aktif</p>
      </article>
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Jatuh Tempo Terdekat</p>
        <p class="mt-2 text-xl font-bold text-slate-900">{{ $nearestDueDate?->format('d M Y') ?? '-' }}</p>
        <p class="mt-1 text-sm text-slate-500">{{ $nearestDueDate ? 'Periksa agar tidak terlambat.' : 'Tidak ada pinjaman aktif.' }}</p>
      </article>
    </section>

    @include('components.flash-message')

    <section class="admin-card mt-6 p-5">
      <h2 class="text-lg font-semibold text-slate-900">Ganti Password</h2>
      <p class="mt-1 text-sm text-slate-500">Gunakan password yang hanya Anda ketahui.</p>
      <form action="{{ route('members.password.update') }}" method="POST" class="mt-5 space-y-4">
        @csrf
        @method('PUT')
        <div>
          <label for="current_password" class="block text-sm font-medium text-slate-700">Password Saat Ini</label>
          <input id="current_password" name="current_password" type="password" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('current_password') border-red-300 @enderror" autocomplete="current-password" required>
          @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
            <input id="password" name="password" type="password" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('password') border-red-300 @enderror" autocomplete="new-password" required>
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" autocomplete="new-password" required>
          </div>
        </div>
        <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Simpan Password Baru</button>
      </form>
    </section>

    <section class="admin-card mt-6 overflow-hidden">
      <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Buku Saya</h2>
        <p class="mt-1 text-sm text-slate-500">Daftar buku yang masih dalam masa peminjaman.</p>
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
                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $loanStatusService->badgeClass($loan) }}">{{ $loanStatusService->displayLabel($loan) }}</span>
              </div>
              <p class="mt-3 text-sm text-slate-600">Jatuh tempo: <span class="font-semibold text-slate-900">{{ $loan->due_date?->format('d M Y') ?? '-' }}</span></p>
            </article>
          @endforeach
        @empty
          <p class="px-5 py-8 text-center text-sm text-slate-500">Belum ada buku yang sedang dipinjam.</p>
        @endforelse
      </div>
    </section>
  </main>
</body>
</html>
