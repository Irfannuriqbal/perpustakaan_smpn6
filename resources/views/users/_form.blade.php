@csrf

<div class="space-y-5">
  <div>
    <label for="name" class="block text-sm font-medium text-slate-700">Nama Admin <span class="text-red-600">*</span></label>
    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('name') border-red-300 @enderror" required>
    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="email" class="block text-sm font-medium text-slate-700">Email <span class="text-red-600">*</span></label>
    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('email') border-red-300 @enderror" required>
    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="password" class="block text-sm font-medium text-slate-700">{{ $user->exists ? 'Reset Password' : 'Password' }} <span class="text-red-600">{{ $user->exists ? '' : '*' }}</span></label>
    <input id="password" name="password" type="password" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('password') border-red-300 @enderror" placeholder="{{ $user->exists ? 'Kosongkan bila tidak diubah' : 'Minimal 8 karakter' }}" autocomplete="new-password" @required(! $user->exists)>
    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password <span class="text-red-600">{{ $user->exists ? '' : '*' }}</span></label>
    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" autocomplete="new-password" @required(! $user->exists)>
  </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
  <a href="{{ route('users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
  <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Simpan</button>
</div>
