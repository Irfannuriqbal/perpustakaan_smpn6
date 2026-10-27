@php
  $navigation = [
      ['label' => 'Dashboard',     'route' => 'dashboard',     'icon' => 'dashboard'],
      ['label' => 'Data Anggota',  'route' => 'members.index', 'icon' => 'students'],
      ['label' => 'Data Buku',     'route' => 'books.index',   'icon' => 'books'],
      ['label' => 'Peminjaman',    'route' => 'loans.index',   'icon' => 'loan'],
      ['label' => 'Pengembalian',  'route' => 'returns.index', 'icon' => 'return'],
      ['label' => 'Kunjungan',     'route' => 'visits.index',  'icon' => 'visit'],
      ['label' => 'Laporan',       'route' => 'reports.index', 'icon' => 'report'],
  ];

  if (auth()->user()?->isSuperAdmin()) {
      $navigation = array_merge($navigation, [
          ['label' => 'Akun Admin', 'route' => 'users.index', 'icon' => 'settings'],
          ['label' => 'Backup', 'route' => 'backups.index', 'icon' => 'report'],
          ['label' => 'Pengaturan', 'route' => 'settings.index', 'icon' => 'settings'],
      ]);
  }
@endphp

<aside data-sidebar class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0">
  <div class="flex h-20 items-center justify-between border-b border-slate-200 px-5">
    <div class="flex min-w-0 items-center gap-3">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-school-100 bg-white p-1 shadow-sm">
        <img src="{{ asset('images/logo-smpn-6.jpg') }}" alt="Logo SMP Negeri 6 Bandung" class="h-full w-full rounded-xl object-contain">
      </div>
      <div class="min-w-0">
        <p class="truncate text-sm font-bold uppercase tracking-wide text-school-800">Perpustakaan</p>
        <p class="truncate text-xs font-medium text-slate-500">SMP Negeri 6 Bandung</p>
      </div>
    </div>
    <button type="button" data-sidebar-close class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Tutup sidebar">
      <span class="text-xl leading-none">×</span>
    </button>
  </div>

  <nav class="flex-1 space-y-1.5 px-4 py-6" aria-label="Menu utama">
    @foreach ($navigation as $item)
      @php $active = request()->routeIs($item['route']); @endphp
      <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-school-50 text-school-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border transition {{ $active ? 'border-school-800 bg-school-800 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-500 group-hover:border-school-100 group-hover:text-school-700' }}">
          @switch($item['icon'])
            @case('dashboard')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1.5"/><rect x="14" y="4" width="6" height="6" rx="1.5"/><rect x="4" y="14" width="6" height="6" rx="1.5"/><rect x="14" y="14" width="6" height="6" rx="1.5"/></svg>
              @break
            @case('students')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 20v-1.5A4.5 4.5 0 0 0 11.5 14h-5A4.5 4.5 0 0 0 2 18.5V20"/><circle cx="9" cy="7" r="4"/><path d="M22 20v-1.5a4.5 4.5 0 0 0-3-4.25"/><path d="M16.5 3.5a4 4 0 0 1 0 7"/></svg>
              @break
            @case('books')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4.5A2.5 2.5 0 0 1 7.5 2H20v18H7.5A2.5 2.5 0 0 0 5 22V4.5Z"/><path d="M5 19.5A2.5 2.5 0 0 1 7.5 17H20"/><path d="M9 6h7"/><path d="M9 10h5"/></svg>
              @break
            @case('loan')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h11"/><path d="m15 4 3 3-3 3"/><rect x="4" y="13" width="6" height="7" rx="1.5"/><path d="M14 17h6"/><path d="m17 14 3 3-3 3"/></svg>
              @break
            @case('return')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 7-4 4 4 4"/><path d="M4 11h10a5 5 0 0 1 0 10h-2"/><rect x="14" y="3" width="6" height="7" rx="1.5"/></svg>
              @break
            @case('visit')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
              @break
            @case('report')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 17v-4"/><path d="M12 17v-7"/><path d="M15 17v-2"/></svg>
              @break
            @case('settings')
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.04.04a2 2 0 1 1-2.83 2.83l-.04-.04A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.05A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.04.04a2 2 0 1 1-2.83-2.83l.04-.04A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.05A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.04-.04a2 2 0 1 1 2.83-2.83l.04.04A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.05A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.04-.04a2 2 0 1 1 2.83 2.83l-.04.04A1.7 1.7 0 0 0 19.4 9c.2.32.3.7.3 1.1H21a2 2 0 1 1 0 4h-.05A1.7 1.7 0 0 0 19.4 15Z"/></svg>
              @break
          @endswitch
        </span>
        <span class="truncate">{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>

  <div class="border-t border-slate-200 px-5 py-4">
    <p class="text-xs font-semibold text-slate-600">Sistem Informasi Perpustakaan</p>
    <p class="mt-1 text-xs text-slate-400">SMP Negeri 6 Bandung</p>
  </div>
</aside>
