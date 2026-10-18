<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Profil - Perpustakaan SMP Negeri 6 Bandung</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
  <main class="mx-auto w-full max-w-lg px-4 py-6 sm:py-10">

    {{-- Header --}}
    <header class="flex items-center justify-between gap-4">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-school-800">Perpustakaan SMP Negeri 6 Bandung</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Edit Profil</h1>
      </div>
      <a href="{{ route('members.profile') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">Kembali</a>
    </header>

    @include('components.flash-message')

    {{-- Form Edit Profil --}}
    <section class="admin-card mt-6 p-5 sm:p-6">
      <h2 class="text-lg font-semibold text-slate-900">Informasi Pribadi</h2>
      <p class="mt-1 text-sm text-slate-500">Perbarui nama dan nomor HP kamu. NIS dan kelas tidak dapat diubah sendiri.</p>

      <form action="{{ route('members.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- Foto Profil --}}
        <div>
          <label class="block text-sm font-medium text-slate-700">Foto Profil</label>
          <div class="mt-3 flex items-center gap-4">
            @if ($member->photo_path)
              <img id="photo-preview" src="{{ asset('storage/' . $member->photo_path) }}" alt="Foto profil" class="h-20 w-20 rounded-2xl object-cover ring-2 ring-slate-200">
            @else
              <div id="photo-placeholder" class="flex h-20 w-20 items-center justify-center rounded-2xl bg-school-50 text-2xl font-bold text-school-700 ring-2 ring-school-100">
                {{ strtoupper(substr($member->name, 0, 1)) }}
              </div>
              <img id="photo-preview" src="" alt="Preview foto" class="hidden h-20 w-20 rounded-2xl object-cover ring-2 ring-slate-200">
            @endif
            <div class="space-y-2">
              <label for="photo" class="cursor-pointer rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Pilih Foto
              </label>
              <input id="photo" name="photo" type="file" accept="image/*" class="sr-only"
                onchange="
                  const file = this.files[0];
                  if (file) {
                    const preview = document.getElementById('photo-preview');
                    const placeholder = document.getElementById('photo-placeholder');
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                  }
                ">
              @if ($member->photo_path)
                <div class="flex items-center gap-2">
                  <input type="checkbox" id="remove_photo" name="remove_photo" value="1" class="rounded border-slate-300 text-school-700 focus:ring-school-700">
                  <label for="remove_photo" class="text-sm text-slate-600">Hapus foto saat ini</label>
                </div>
              @endif
            </div>
          </div>
          @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          <p class="mt-2 text-xs text-slate-500">Format: JPG, PNG, GIF. Maks 2 MB.</p>
        </div>

        {{-- Nama --}}
        <div>
          <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
          <input id="name" name="name" type="text" value="{{ old('name', $member->name) }}"
            class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-school-700 focus:ring-school-700 @error('name') border-red-300 @enderror"
            required>
          @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- NIS (readonly) --}}
        <div>
          <label class="block text-sm font-medium text-slate-700">NIS <span class="text-slate-400">(tidak dapat diubah)</span></label>
          <input type="text" value="{{ $member->nis }}" class="mt-2 block w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-500 shadow-sm cursor-not-allowed" readonly disabled>
        </div>

        {{-- Kelas (readonly) --}}
        <div>
          <label class="block text-sm font-medium text-slate-700">Kelas <span class="text-slate-400">(tidak dapat diubah)</span></label>
          <input type="text" value="{{ $member->class }}" class="mt-2 block w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-500 shadow-sm cursor-not-allowed" readonly disabled>
        </div>

        {{-- Nomor HP --}}
        <div>
          <label for="phone" class="block text-sm font-medium text-slate-700">Nomor HP</label>
          <input id="phone" name="phone" type="tel" value="{{ old('phone', $member->phone) }}"
            class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-school-700 focus:ring-school-700 @error('phone') border-red-300 @enderror"
            placeholder="Contoh: 08123456789">
          @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" class="rounded-lg bg-school-800 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-school-900">
            Simpan Perubahan
          </button>
          <a href="{{ route('members.profile') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Batal
          </a>
        </div>
      </form>
    </section>

    {{-- Form Ganti Password --}}
    <section class="admin-card mt-6 p-5 sm:p-6">
      <h2 class="text-lg font-semibold text-slate-900">Ganti Password</h2>
      <p class="mt-1 text-sm text-slate-500">Gunakan password yang hanya kamu ketahui.</p>

      <form action="{{ route('members.password.update') }}" method="POST" class="mt-5 space-y-4">
        @csrf
        @method('PUT')

        <div>
          <label for="current_password" class="block text-sm font-medium text-slate-700">Password Saat Ini</label>
          <input id="current_password" name="current_password" type="password"
            class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('current_password') border-red-300 @enderror"
            autocomplete="current-password" required>
          @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
            <input id="password" name="password" type="password"
              class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('password') border-red-300 @enderror"
              autocomplete="new-password" required>
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
              class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700"
              autocomplete="new-password" required>
          </div>
        </div>

        <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">
          Simpan Password Baru
        </button>
      </form>
    </section>

  </main>
</body>
</html>
