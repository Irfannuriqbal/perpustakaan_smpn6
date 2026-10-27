<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Katalog Buku - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

  @include('members._nav')

  <main class="mx-auto w-full max-w-4xl px-4 py-6 sm:py-10">

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Katalog Buku</h1>
      <p class="mt-1 text-sm text-slate-500">Temukan buku yang tersedia di perpustakaan SMP Negeri 6 Bandung.</p>
    </div>

    @include('components.flash-message')

    {{-- Filter --}}
    <form method="GET" action="{{ route('members.catalog') }}" class="admin-card mb-6 grid gap-3 p-4 sm:grid-cols-[1fr_180px_160px_auto]">
      <div>
        <label for="search" class="sr-only">Cari buku</label>
        <input id="search" name="search" type="search" value="{{ $search }}"
          class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700"
          placeholder="Cari judul, penulis, atau ISBN...">
      </div>
      <div>
        <label for="category_id" class="sr-only">Kategori</label>
        <select id="category_id" name="category_id" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
          <option value="">Semua kategori</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected((string)$categoryId === (string)$cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label for="available" class="sr-only">Ketersediaan</label>
        <select id="available" name="available" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
          <option value="">Semua buku</option>
          <option value="1" @selected($available === '1')>Tersedia saja</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Cari</button>
        <a href="{{ route('members.catalog') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
      </div>
    </form>

    {{-- Buku Grid --}}
    @if ($books->isNotEmpty())
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($books as $book)
          <article class="admin-card flex flex-col overflow-hidden">
            <div class="flex-1 p-5">
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                    {{ $book->category?->name ?? '-' }}
                  </span>
                  @if ($book->ddc)
                    <span class="ml-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-xs font-mono font-medium text-blue-700">{{ $book->ddc }}</span>
                  @endif
                </div>
                @if ($book->available_stock > 0)
                  <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">Tersedia</span>
                @else
                  <span class="shrink-0 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-600 ring-1 ring-red-200">Habis</span>
                @endif
              </div>

              <h3 class="mt-3 text-base font-bold text-slate-900 leading-snug">{{ $book->title }}</h3>
              <p class="mt-1 text-sm text-slate-500">{{ $book->author }}</p>
              @if ($book->editor)
                <p class="mt-0.5 text-xs text-slate-400">Editor: {{ $book->editor }}</p>
              @endif
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-5 py-3">
              <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                <div class="flex gap-1">
                  <dt class="text-slate-400">Penerbit:</dt>
                  <dd class="font-medium text-slate-700 truncate">{{ $book->publisher ?? '-' }}</dd>
                </div>
                <div class="flex gap-1">
                  <dt class="text-slate-400">Tahun:</dt>
                  <dd class="font-medium text-slate-700">{{ $book->publication_year ?? '-' }}</dd>
                </div>
                <div class="flex gap-1">
                  <dt class="text-slate-400">Stok:</dt>
                  <dd class="font-semibold text-slate-900">{{ $book->available_stock }}/{{ $book->stock }}</dd>
                </div>
                @if ($book->total_pages)
                  <div class="flex gap-1">
                    <dt class="text-slate-400">Hal:</dt>
                    <dd class="font-medium text-slate-700">{{ $book->total_pages }}</dd>
                  </div>
                @endif
                <div class="col-span-2 flex gap-1">
                  <dt class="text-slate-400">Rak:</dt>
                  <dd class="font-mono font-semibold text-school-800">{{ $book->shelf_location ?? '-' }}</dd>
                </div>
              </dl>
              @if ($book->notes)
                <p class="mt-2 text-xs italic text-slate-500 line-clamp-2">{{ $book->notes }}</p>
              @endif
            </div>
          </article>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $books->links() }}
      </div>
    @else
      <div class="admin-card py-16 text-center">
        <p class="text-slate-700 font-medium">Buku tidak ditemukan.</p>
        <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau filter pencarian.</p>
      </div>
    @endif

  </main>
</body>
</html>
