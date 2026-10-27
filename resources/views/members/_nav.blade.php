<header class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 backdrop-blur-sm">
  <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3">
    <a href="{{ route('members.home') }}" class="flex shrink-0 items-center gap-3">
      <img src="{{ asset('images/logo-smpn-6.jpg') }}" alt="Logo SMP Negeri 6 Bandung" class="h-9 w-9 rounded-lg object-contain">
      <span class="hidden text-sm font-bold text-school-800 sm:inline">Portal Anggota</span>
    </a>

    <nav class="flex min-w-0 items-center gap-1 overflow-x-auto text-sm font-semibold" aria-label="Menu portal anggota">
      @php
        $links = [
          ['label' => 'Beranda', 'route' => 'members.home', 'active' => 'members.home'],
          ['label' => 'Riwayat', 'route' => 'members.loans', 'active' => 'members.loans*'],
          ['label' => 'Notifikasi', 'route' => 'members.notifications', 'active' => 'members.notifications'],
          ['label' => 'Pengembalian', 'route' => 'members.returns', 'active' => 'members.returns*'],
          ['label' => 'Profil', 'route' => 'members.profile', 'active' => 'members.profile*'],
          ['label' => 'Kartu', 'route' => 'members.card', 'active' => 'members.card*'],
          ['label' => 'Katalog', 'route' => 'members.catalog', 'active' => 'members.catalog'],
        ];
      @endphp
      @foreach ($links as $link)
        <a href="{{ route($link['route']) }}"
          class="shrink-0 rounded-lg px-3 py-2 transition {{ request()->routeIs($link['active']) ? 'bg-school-800 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
          {{ $link['label'] }}
        </a>
      @endforeach
      <form action="{{ route('members.logout') }}" method="POST" class="ml-1 shrink-0">
        @csrf
        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 hover:bg-slate-100">Keluar</button>
      </form>
    </nav>
  </div>
</header>
