@csrf

<div class="grid gap-5 md:grid-cols-2">
  <div>
    <label for="nis" class="block text-sm font-medium text-slate-700">NIS <span class="text-red-600">*</span></label>
    <input id="nis" name="nis" type="text" value="{{ old('nis', $student->nis) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('nis') border-red-300 @enderror" required>
    @error('nis') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="member_code" class="block text-sm font-medium text-slate-700">Kode Anggota <span class="text-red-600">*</span></label>
    <input id="member_code" name="member_code" type="text" value="{{ old('member_code', $student->member_code) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('member_code') border-red-300 @enderror" required>
    @error('member_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="md:col-span-2">
    <label for="name" class="block text-sm font-medium text-slate-700">Nama Siswa <span class="text-red-600">*</span></label>
    <input id="name" name="name" type="text" value="{{ old('name', $student->name) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('name') border-red-300 @enderror" required>
    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="gender" class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
    <select id="gender" name="gender" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('gender') border-red-300 @enderror">
      <option value="">Pilih jenis kelamin</option>
      <option value="Laki-laki" @selected(old('gender', $student->gender) === 'Laki-laki')>Laki-laki</option>
      <option value="Perempuan" @selected(old('gender', $student->gender) === 'Perempuan')>Perempuan</option>
    </select>
    @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="class" class="block text-sm font-medium text-slate-700">Kelas <span class="text-red-600">*</span></label>
    <input id="class" name="class" type="text" value="{{ old('class', $student->class) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('class') border-red-300 @enderror" placeholder="Contoh: VII-A" required>
    @error('class') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="phone" class="block text-sm font-medium text-slate-700">Nomor HP</label>
    <input id="phone" name="phone" type="text" value="{{ old('phone', $student->phone) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('phone') border-red-300 @enderror">
    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-red-600">*</span></label>
    <select id="status" name="status" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('status') border-red-300 @enderror" required>
      <option value="aktif" @selected(old('status', $student->status ?: 'aktif') === 'aktif')>Aktif</option>
      <option value="nonaktif" @selected(old('status', $student->status) === 'nonaktif')>Nonaktif</option>
    </select>
    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="md:col-span-2">
    <label for="photo" class="block text-sm font-medium text-slate-700">Foto Anggota</label>
    <div class="mt-2 flex flex-wrap items-center gap-4">
      @if ($student->photo_path)
        <img src="{{ asset('storage/'.$student->photo_path) }}" alt="Foto {{ $student->name }}" class="h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200">
      @else
        <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-slate-100 text-xl font-bold text-slate-500 ring-1 ring-slate-200">{{ strtoupper(substr($student->name ?: 'A', 0, 1)) }}</div>
      @endif
      <div class="min-w-0 flex-1">
        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-school-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-school-800 hover:file:bg-school-100">
        <p class="mt-1 text-xs text-slate-500">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
        @if ($student->photo_path)
          <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="remove_photo" value="1" class="rounded border-slate-300 text-school-800 focus:ring-school-700"> Hapus foto saat ini</label>
        @endif
      </div>
    </div>
    @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="password" class="block text-sm font-medium text-slate-700">{{ $student->exists ? 'Reset Password Portal' : 'Password Portal' }} <span class="text-red-600">{{ $student->exists ? '' : '*' }}</span></label>
    <input id="password" name="password" type="password" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('password') border-red-300 @enderror" placeholder="{{ $student->exists ? 'Kosongkan bila tidak diubah' : 'Minimal 8 karakter' }}" autocomplete="new-password" @required(! $student->exists)>
    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password <span class="text-red-600">{{ $student->exists ? '' : '*' }}</span></label>
    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700" placeholder="Ulangi password portal" autocomplete="new-password" @required(! $student->exists)>
  </div>

  <div class="md:col-span-2">
    <label for="address" class="block text-sm font-medium text-slate-700">Alamat</label>
    <textarea id="address" name="address" rows="3" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('address') border-red-300 @enderror">{{ old('address', $student->address) }}</textarea>
    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
  <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
  <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Simpan</button>
</div>
