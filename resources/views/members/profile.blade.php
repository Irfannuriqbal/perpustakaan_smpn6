<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Profil Saya - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

  @include('members._nav')

  <main class="mx-auto w-full max-w-2xl px-4 py-6 sm:py-10">

    @include('components.flash-message')

    {{-- Kartu Identitas --}}
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-school-700 to-school-900 p-6 text-white shadow-xl sm:p-8">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-school-100">Identitas Anggota</p>
      <div class="mt-5 flex items-center gap-5">
        @if ($photoUrl)
          <img src="{{ $photoUrl }}" alt="Foto {{ $member->name }}" class="h-20 w-20 rounded-2xl object-cover ring-2 ring-white/60">
        @else
          <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/15 text-2xl font-bold ring-2 ring-white/30">
            {{ strtoupper(substr($member->name, 0, 1)) }}
          </div>
        @endif
        <div>
          <h2 class="text-2xl font-bold">{{ $member->name }}</h2>
          <p class="mt-1 text-sm text-school-100">{{ $member->member_type }}</p>
          @if ($member->class)
            <p class="text-sm text-school-100">Kelas: {{ $member->class }}</p>
          @endif
        </div>
      </div>

      <dl class="mt-6 grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
        <div>
          <dt class="text-school-200">No. Anggota</dt>
          <dd class="mt-1 font-mono font-semibold">{{ $member->no }}</dd>
        </div>
        <div>
          <dt class="text-school-200">Kode Anggota</dt>
          <dd class="mt-1 font-semibold tracking-widest">{{ $member->member_code }}</dd>
        </div>
        <div>
          <dt class="text-school-200">Nomor HP</dt>
          <dd class="mt-1 font-semibold">{{ $member->phone ?? '-' }}</dd>
        </div>
        <div>
          <dt class="text-school-200">Jenis Kelamin</dt>
          <dd class="mt-1 font-semibold">{{ $member->gender ?? '-' }}</dd>
        </div>
        <div>
          <dt class="text-school-200">Kelas</dt>
          <dd class="mt-1 font-semibold">{{ $member->class ?? '-' }}</dd>
        </div>
        <div class="col-span-2 sm:col-span-3">
          <dt class="text-school-200">Alamat</dt>
          <dd class="mt-1 font-semibold">{{ $member->address ?? '-' }}</dd>
        </div>
        <div>
          <dt class="text-school-200">Status</dt>
          <dd class="mt-1 font-semibold capitalize">{{ $member->status }}</dd>
        </div>
      </dl>

      {{-- QR Code --}}
      <div class="mt-7 rounded-2xl bg-white p-4 text-center text-slate-900 shadow-inner">
        <div class="mx-auto w-36 max-w-full" aria-label="QR Code {{ $member->member_code }}">{!! $qrCode !!}</div>
        <p class="mt-3 text-sm font-bold tracking-widest">{{ $member->member_code }}</p>
        <p class="mt-1 text-xs text-slate-500">Scan QR ini di perpustakaan.</p>
      </div>
    </section>

    {{-- Ringkasan Pinjaman --}}
    <section class="mt-6 grid gap-4 sm:grid-cols-2">
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Buku Sedang Dipinjam</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $activeItemsCount }}</p>
        <p class="mt-1 text-xs text-slate-400">buku aktif</p>
      </article>
      <article class="admin-card p-5">
        <p class="text-sm font-medium text-slate-500">Jatuh Tempo Terdekat</p>
        <p class="mt-2 text-xl font-bold text-slate-900">{{ $nearestDueDate?->format('d M Y') ?? '-' }}</p>
        <p class="mt-1 text-xs text-slate-400">{{ $nearestDueDate ? 'Periksa agar tidak terlambat.' : 'Tidak ada pinjaman aktif.' }}</p>
      </article>
    </section>

    {{-- Tombol Edit --}}
    <div class="mt-4 flex flex-wrap justify-end gap-3">
      <a href="{{ route('members.portal-card.print') }}" class="rounded-lg border border-school-800 px-5 py-2.5 text-sm font-semibold text-school-800 hover:bg-school-50">
        Cetak Kartu Anggota
      </a>
      <a href="{{ route('members.profile.edit') }}" class="rounded-lg bg-school-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-school-900">
        Edit Profil
      </a>
    </div>

    {{-- Riwayat Peminjaman --}}
    <section class="admin-card mt-6 overflow-hidden">
      <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat Peminjaman</h2>
        <p class="mt-1 text-sm text-slate-500">Semua transaksi peminjaman buku kamu.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              <th class="px-4 py-3 text-left">Buku</th>
              <th class="px-4 py-3 text-left">Tgl Pinjam</th>
              <th class="px-4 py-3 text-left">Jatuh Tempo</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Terlambat</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($loanHistory as $loan)
              @foreach ($loan->items as $item)
                @php
                  $lateDays = $loanStatusService->lateDays($loan);
                  $badge    = $loanStatusService->badgeClass($loan);
                  $label    = $loanStatusService->displayLabel($loan);
                  if ($loan->status === 'dikembalikan' && $loan->returned_at && $loan->due_date) {
                      $returnedDay = \Carbon\Carbon::parse($loan->returned_at)->startOfDay();
                      $dueDay      = \Carbon\Carbon::parse($loan->due_date)->startOfDay();
                      $lateDays    = max(0, (int) $dueDay->diffInDays($returnedDay, false));
                  }
                @endphp
                <tr class="{{ $loan->status === 'terlambat' ? 'bg-red-50/50' : '' }}">
                  <td class="table-cell font-medium text-slate-900">{{ $item->book?->title ?? '-' }}</td>
                  <td class="table-cell text-slate-600">{{ $loan->loan_date?->format('d M Y') ?? '-' }}</td>
                  <td class="table-cell text-slate-600">{{ $loan->due_date?->format('d M Y') ?? '-' }}</td>
                  <td class="table-cell">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $badge }}">{{ $label }}</span>
                  </td>
                  <td class="table-cell">
                    @if ($lateDays > 0)
                      <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-200">{{ $lateDays }} hari</span>
                    @else
                      <span class="text-slate-400">-</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            @empty
              <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada riwayat peminjaman.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($loanHistory->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $loanHistory->links() }}
        </div>
      @endif
    </section>

  </main>
</body>
</html>
