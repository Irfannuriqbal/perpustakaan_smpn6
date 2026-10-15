@csrf

<div class="space-y-5">
  <div>
    <label for="name" class="block text-sm font-medium text-slate-700">Nama Kategori <span class="text-red-600">*</span></label>
    <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('name') border-red-300 @enderror" required>
    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
    <textarea id="description" name="description" rows="4" class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('description') border-red-300 @enderror">{{ old('description', $category->description) }}</textarea>
    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
  <a href="{{ route('categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
  <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Simpan</button>
</div>
