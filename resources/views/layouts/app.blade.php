<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Dashboard' }} - Perpustakaan SMPN 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  <div class="min-h-screen bg-slate-100">
    <div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-900/40 lg:hidden"></div>

    @include('components.sidebar')

    <div class="lg:pl-72">
      @include('components.topbar')

      <main class="px-4 py-6 sm:px-6 lg:px-8">
        @yield('content')
      </main>
    </div>
  </div>
</body>
</html>
