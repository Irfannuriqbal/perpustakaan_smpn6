@if (session('success'))
  <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
@endif

@if (session('error'))
  <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
@endif

@if (session('status'))
  <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('status') }}</div>
@endif

@if ($errors->any())
  <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
    <p class="font-medium">Data belum valid.</p>
    <ul class="mt-2 list-inside list-disc space-y-1">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
