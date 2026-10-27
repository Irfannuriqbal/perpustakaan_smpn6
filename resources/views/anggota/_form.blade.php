@csrf

<div class="grid gap-5 md:grid-cols-2">

  {{-- No. Anggota --}}
  <div>
    <label for="no" class="block text-sm font-medium text-slate-700">No. Anggota <span class="text-red-600">*</span></label>
    <input id="no" name="no" type="text" value="{{ old('no', $member->no) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('no') border-red-300 @enderror"
      placeholder="Contoh: 252607001" required>
    @error('no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Kode Anggota (untuk QR) --}}
  <div>
    <label for="member_code" class="block text-sm font-medium text-slate-700">Kode Anggota <span class="text-red-600">*</span></label>
    <input id="member_code" name="member_code" type="text" value="{{ old('member_code', $member->member_code) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('member_code') border-red-300 @enderror"
      placeholder="Contoh: AGT-2026-001" required>
    <p class="mt-1 text-xs text-slate-500">Kode ini digunakan untuk scan QR di perpustakaan.</p>
    @error('member_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Jenis Anggota --}}
  <div>
    <label for="member_type" class="block text-sm font-medium text-slate-700">Jenis Anggota <span class="text-red-600">*</span></label>
    <select id="member_type" name="member_type"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('member_type') border-red-300 @enderror"
      required onchange="toggleClassField(this.value)">
      <option value="">Pilih jenis anggota</option>
      <option value="Siswa/i"   @selected(old('member_type', $member->member_type ?? 'Siswa/i') === 'Siswa/i')>Siswa/i</option>
      <option value="Guru/Staf" @selected(old('member_type', $member->member_type) === 'Guru/Staf')>Guru/Staf</option>
    </select>
    @error('member_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Nama --}}
  <div class="md:col-span-2">
    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap <span class="text-red-600">*</span></label>
    <input id="name" name="name" type="text" value="{{ old('name', $member->name) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('name') border-red-300 @enderror" required>
    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Jenis Kelamin --}}
  <div>
    <label for="gender" class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
    <select id="gender" name="gender"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('gender') border-red-300 @enderror">
      <option value="">Pilih jenis kelamin</option>
      <option value="Laki-laki"  @selected(old('gender', $member->gender) === 'Laki-laki')>Laki-laki</option>
      <option value="Perempuan"  @selected(old('gender', $member->gender) === 'Perempuan')>Perempuan</option>
    </select>
    @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Kelas (only for Siswa/i) --}}
  <div id="field-class" class="{{ (old('member_type', $member->member_type ?? 'Siswa/i') === 'Guru/Staf') ? 'hidden' : '' }}">
    <label for="class" class="block text-sm font-medium text-slate-700">Kelas</label>
    <input id="class" name="class" type="text" value="{{ old('class', $member->class) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('class') border-red-300 @enderror"
      placeholder="Contoh: VII-A">
    @error('class') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Nomor HP --}}
  <div>
    <label for="phone" class="block text-sm font-medium text-slate-700">Nomor HP</label>
    <input id="phone" name="phone" type="text" value="{{ old('phone', $member->phone) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('phone') border-red-300 @enderror">
    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Status --}}
  <div>
    <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-red-600">*</span></label>
    <select id="status" name="status"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('status') border-red-300 @enderror" required>
      <option value="aktif"    @selected(old('status', $member->status ?: 'aktif') === 'aktif')>Aktif</option>
      <option value="nonaktif" @selected(old('status', $member->status) === 'nonaktif')>Nonaktif</option>
    </select>
    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Foto --}}
  <div class="md:col-span-2">
    <label for="photo" class="block text-sm font-medium text-slate-700">Foto Anggota</label>
    <div class="mt-2 flex flex-wrap items-center gap-4">
      @if ($member->photo_path)
        <img src="{{ asset('storage/'.$member->photo_path) }}" alt="Foto {{ $member->name }}"
          class="h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200">
      @else
        <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-slate-100 text-xl font-bold text-slate-500 ring-1 ring-slate-200">
          {{ strtoupper(substr($member->name ?: 'A', 0, 1)) }}
        </div>
      @endif
      <div class="min-w-0 flex-1">
        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp"
          class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-school-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-school-800 hover:file:bg-school-100">
        <p class="mt-1 text-xs text-slate-500">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
        @if ($member->photo_path)
          <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="remove_photo" value="1" class="rounded border-slate-300 text-school-800 focus:ring-school-700">
            Hapus foto saat ini
          </label>
        @endif
      </div>
    </div>
    @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  {{-- Password --}}
  <div>
    <label for="password" class="block text-sm font-medium text-slate-700">
      {{ $member->exists ? 'Reset Password Portal' : 'Password Portal' }}
      <span class="text-red-600">{{ $member->exists ? '' : '*' }}</span>
    </label>
    <input id="password" name="password" type="password"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('password') border-red-300 @enderror"
      placeholder="{{ $member->exists ? 'Kosongkan bila tidak diubah' : 'Minimal 8 karakter' }}"
      autocomplete="new-password" @required(! $member->exists)>
    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">
      Konfirmasi Password <span class="text-red-600">{{ $member->exists ? '' : '*' }}</span>
    </label>
    <input id="password_confirmation" name="password_confirmation" type="password"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700"
      placeholder="Ulangi password portal" autocomplete="new-password" @required(! $member->exists)>
  </div>

  {{-- Alamat --}}
  <div class="md:col-span-2">
    <label for="address" class="block text-sm font-medium text-slate-700">Alamat</label>
    <textarea id="address" name="address" rows="3"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('address') border-red-300 @enderror">{{ old('address', $member->address) }}</textarea>
    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
  <a href="{{ route('members.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
  <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Simpan</button>
</div>

<script>
function toggleClassField(type) {
  const field = document.getElementById('field-class');
  if (type === 'Guru/Staf') {
    field.classList.add('hidden');
    field.querySelector('input').value = '';
  } else {
    field.classList.remove('hidden');
  }
}
</script>
