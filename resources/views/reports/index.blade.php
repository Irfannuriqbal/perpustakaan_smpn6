@extends('layouts.app', ['title' => 'Laporan'])

@section('content')
  <div class="space-y-6">
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h2 class="page-title">Laporan</h2>
        <p class="page-subtitle">Tampilkan dan export laporan perpustakaan tanpa mengubah data.</p>
      </div>
      <div class="flex flex-col gap-2 sm:flex-row">
        <a href="{{ route('reports.export.pdf', ['type' => $type] + request()->except('page')) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Export PDF</a>
        <a href="{{ route('reports.export.excel', ['type' => $type] + request()->except('page')) }}" class="rounded-lg bg-school-800 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-school-900">Export Excel</a>
      </div>
    </section>

    @include('components.flash-message')

    <section class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
      @foreach ($types as $reportType => $reportMeta)
        <a href="{{ route('reports.index', ['type' => $reportType]) }}" class="rounded-xl border p-4 transition {{ $type === $reportType ? 'border-school-200 bg-school-50 text-school-900' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
          <span class="text-sm font-semibold">{{ $reportMeta['title'] }}</span>
        </a>
      @endforeach
    </section>

    <section class="admin-card">
      <div class="border-b border-slate-200 px-5 py-4 text-center">
        <p class="text-base font-bold uppercase tracking-wide text-slate-900">SMP Negeri 6 Bandung</p>
        <p class="mt-1 text-sm font-medium text-slate-700">Sistem Informasi Perpustakaan</p>
        <p class="mt-3 text-sm text-slate-600">{{ $meta['title'] }} · {{ $period }} · Dicetak {{ now()->format('d M Y H:i') }}</p>
      </div>

      <form method="GET" action="{{ route('reports.index') }}" class="grid gap-3 border-b border-slate-200 px-5 py-4 lg:grid-cols-6">
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="lg:col-span-2">
          <label for="search" class="sr-only">Pencarian</label>
          <input id="search" name="search" type="search" value="{{ $filters['search'] }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Cari data laporan">
        </div>

        @if ($meta['filters']['date'])
          <div>
            <label for="start_date" class="sr-only">Tanggal awal</label>
            <input id="start_date" name="start_date" type="date" value="{{ $filters['start_date'] }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
          </div>
          <div>
            <label for="end_date" class="sr-only">Tanggal akhir</label>
            <input id="end_date" name="end_date" type="date" value="{{ $filters['end_date'] }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
          </div>
        @endif

        @if ($meta['filters']['class'])
          <div>
            <label for="class" class="sr-only">Filter kelas</label>
            <select id="class" name="class" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
              <option value="">Semua kelas</option>
              @foreach ($classes as $classOption)
                <option value="{{ $classOption }}" @selected($filters['class'] === $classOption)>{{ $classOption }}</option>
              @endforeach
            </select>
          </div>
        @endif

        @if ($meta['filters']['status'])
          <div>
            <label for="status" class="sr-only">Filter status</label>
            <select id="status" name="status" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
              <option value="">Semua status</option>
              @foreach ($meta['statuses'] as $statusValue => $statusLabel)
                <option value="{{ $statusValue }}" @selected($filters['status'] === $statusValue)>{{ $statusLabel }}</option>
              @endforeach
            </select>
          </div>
        @endif

        @if ($meta['filters']['category'])
          <div>
            <label for="category_id" class="sr-only">Filter kategori</label>
            <select id="category_id" name="category_id" class="block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700">
              <option value="">Semua kategori</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
        @endif

        <div class="flex gap-2 lg:col-span-2">
          <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Filter</button>
          <a href="{{ route('reports.index', ['type' => $type]) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
      </form>

      <div class="grid gap-4 border-b border-slate-200 px-5 py-4 md:grid-cols-3">
        <div>
          <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jenis Laporan</p>
          <p class="mt-1 text-base font-semibold text-slate-900">{{ $meta['title'] }}</p>
        </div>
        <div>
          <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Periode</p>
          <p class="mt-1 text-base font-semibold text-slate-900">{{ $period }}</p>
        </div>
        <div>
          <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jumlah Total Data</p>
          <p class="mt-1 text-base font-semibold text-slate-900">{{ number_format($total, 0, ',', '.') }} data</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="table-head">
            <tr>
              @foreach ($meta['columns'] as $label)
                <th class="px-4 py-3">{{ $label }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($rows as $row)
              <tr>
                @foreach (array_keys($meta['columns']) as $key)
                  <td class="table-cell {{ $loop->first ? 'font-medium text-slate-900' : '' }}">{{ $row[$key] ?? '-' }}</td>
                @endforeach
              </tr>
            @empty
              <tr>
                <td colspan="{{ count($meta['columns']) }}" class="px-4 py-10 text-center">
                  <p class="text-sm font-medium text-slate-700">Data laporan tidak ditemukan.</p>
                  <p class="mt-1 text-sm text-slate-500">Ubah filter pencarian atau periode laporan.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($rows->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
          {{ $rows->links() }}
        </div>
      @endif
    </section>
  </div>
@endsection
