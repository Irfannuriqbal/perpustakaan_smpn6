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
    <label for="address" class="block text-sm font-medium text-slate-700">Alamat</label>
    <textarea id="address" name="address" rows="3" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('address') border-red-300 @enderror">{{ old('address', $student->address) }}</textarea>
    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
  <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
  <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Simpan</button>
</div>
