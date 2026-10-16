<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Layar Scan Kunjungan - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-school-900 text-white">
  @php($result = session('kiosk_result'))

  <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col px-5 py-6 sm:px-10 sm:py-10">
    <header class="flex items-center justify-between gap-4">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-school-200">Perpustakaan SMP Negeri 6 Bandung</p>
        <h1 class="mt-2 text-xl font-semibold sm:text-2xl">Layar Scan Kunjungan</h1>
      </div>
      <a href="{{ route('visits.index') }}" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Kembali ke Admin</a>
    </header>

    <section class="flex flex-1 flex-col items-center justify-center py-10 text-center">
      @if ($result)
        @if ($result['status'] === 'success')
          <div data-kiosk-result data-kiosk-reset-url="{{ route('visits.kiosk') }}" class="w-full max-w-2xl rounded-3xl border border-emerald-300/40 bg-emerald-400/15 p-8 shadow-2xl shadow-emerald-950/30 sm:p-12" role="status" aria-live="polite">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-100">Kunjungan Berhasil Dicatat</p>
            <p class="mt-5 text-4xl font-bold sm:text-5xl">Selamat datang, {{ $result['name'] }}</p>
            <p class="mt-4 text-xl text-emerald-50 sm:text-2xl">Kelas {{ $result['class'] }}</p>
            <p class="mt-8 text-lg font-medium text-emerald-100">Tercatat pukul {{ $result['check_in_time'] }}</p>
            <p class="mt-8 text-sm text-emerald-100">Layar siap untuk kartu berikutnya dalam <span data-kiosk-countdown>4</span> detik.</p>
          </div>
        @else
          <div data-kiosk-result data-kiosk-reset-url="{{ route('visits.kiosk') }}" class="w-full max-w-2xl rounded-3xl border border-red-300/40 bg-red-400/15 p-8 shadow-2xl shadow-red-950/30 sm:p-12" role="alert" aria-live="assertive">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-red-100">Scan Tidak Berhasil</p>
            <p class="mt-5 text-2xl font-bold sm:text-3xl">{{ $result['message'] }}</p>
            <p class="mt-8 text-sm text-red-100">Layar siap untuk kartu berikutnya dalam <span data-kiosk-countdown>4</span> detik.</p>
          </div>
        @endif
      @else
        <div class="w-full max-w-2xl">
          <p class="text-sm font-semibold uppercase tracking-[0.2em] text-school-200">Kunjungan Perpustakaan</p>
          <h2 class="mt-5 text-4xl font-bold leading-tight sm:text-6xl">Silakan scan kartu anggota Anda</h2>
          <p class="mx-auto mt-6 max-w-xl text-lg text-school-100 sm:text-xl">Arahkan QR Code kartu ke scanner. Anda juga dapat memasukkan kode anggota lalu tekan Enter.</p>

          <form action="{{ route('visits.kiosk.store') }}" method="POST" class="mt-10">
            @csrf
            <label for="member_code" class="sr-only">Kode anggota</label>
            <input data-kiosk-input id="member_code" name="member_code" type="text" class="block w-full rounded-2xl border-4 border-white bg-white px-6 py-5 text-center text-2xl font-semibold tracking-wide text-slate-900 placeholder:text-slate-400 focus:border-school-300 focus:ring-4 focus:ring-school-300/40 sm:text-3xl" placeholder="Scan kartu di sini" autocomplete="off" autofocus required>
            <p class="mt-4 text-sm text-school-200">Scanner USB akan mengisi kode ini otomatis.</p>
          </form>
        </div>
      @endif
    </section>

    <footer class="text-center text-sm text-school-200">Scan mandiri hanya untuk kunjungan. Peminjaman dan pengembalian tetap diproses admin perpustakaan.</footer>
  </main>
</body>
</html>
