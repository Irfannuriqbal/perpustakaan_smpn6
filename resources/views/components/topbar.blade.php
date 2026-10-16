@php
  $currentUser = auth()->user();
  $displayName = $currentUser?->name ?? 'Admin Perpustakaan';
  $displayRole = $currentUser?->roleLabel() ?? 'Admin Perpustakaan';
  $initials = collect(explode(' ', $displayName))->filter()->take(2)->map(fn ($part) => substr($part, 0, 1))->join('') ?: 'PP';
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/90">
  <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-3">
      <button type="button" data-sidebar-open class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 lg:hidden" aria-label="Buka sidebar">
        <span class="block h-0.5 w-5 bg-current"></span>
        <span class="mt-1.5 block h-0.5 w-5 bg-current"></span>
        <span class="mt-1.5 block h-0.5 w-5 bg-current"></span>
      </button>
      <div>
        <h1 class="text-base font-semibold text-slate-900 sm:text-lg">{{ $title ?? 'Dashboard' }}</h1>
        <p class="hidden text-xs text-slate-500 sm:block">Sistem Informasi Perpustakaan SMP Negeri 6 Bandung</p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <div class="hidden text-right sm:block">
        <p class="text-sm font-medium text-slate-800">{{ $displayName }}</p>
        <p class="text-xs text-slate-500">{{ $displayRole }}</p>
      </div>
      <div class="flex h-10 w-10 items-center justify-center rounded-full bg-school-50 text-sm font-semibold text-school-800">{{ strtoupper($initials) }}</div>
      @auth
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Keluar</button>
        </form>
      @endauth
    </div>
  </div>
</header>
