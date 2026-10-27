@csrf

<div class="grid gap-5 md:grid-cols-2">
  <div>
    <label for="book_code" class="block text-sm font-medium text-slate-700">Kode Buku / Kode Label <span class="text-red-600">*</span></label>
    <input id="book_code" name="book_code" type="text" value="{{ old('book_code', $book->book_code) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('book_code') border-red-300 @enderror" required>
    @error('book_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="isbn" class="block text-sm font-medium text-slate-700">ISBN</label>
    <input id="isbn" name="isbn" type="text" value="{{ old('isbn', $book->isbn) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('isbn') border-red-300 @enderror">
    @error('isbn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="ddc" class="block text-sm font-medium text-slate-700">DDC</label>
    <input id="ddc" name="ddc" type="text" value="{{ old('ddc', $book->ddc) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('ddc') border-red-300 @enderror"
      placeholder="Contoh: 004">
    <p class="mt-1 text-xs text-slate-500">Klasifikasi Dewey Decimal</p>
    @error('ddc') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="md:col-span-2">
    <label for="title" class="block text-sm font-medium text-slate-700">Judul Buku <span class="text-red-600">*</span></label>
    <input id="title" name="title" type="text" value="{{ old('title', $book->title) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('title') border-red-300 @enderror" required>
    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="author" class="block text-sm font-medium text-slate-700">Penulis <span class="text-red-600">*</span></label>
    <input id="author" name="author" type="text" value="{{ old('author', $book->author) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('author') border-red-300 @enderror" required>
    @error('author') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="editor" class="block text-sm font-medium text-slate-700">Editor</label>
    <input id="editor" name="editor" type="text" value="{{ old('editor', $book->editor) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('editor') border-red-300 @enderror">
    @error('editor') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="publisher" class="block text-sm font-medium text-slate-700">Penerbit</label>
    <input id="publisher" name="publisher" type="text" value="{{ old('publisher', $book->publisher) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('publisher') border-red-300 @enderror">
    @error('publisher') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="publication_year" class="block text-sm font-medium text-slate-700">Tahun Terbit</label>
    <input id="publication_year" name="publication_year" type="number" min="1900" max="{{ now()->year }}"
      value="{{ old('publication_year', $book->publication_year) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('publication_year') border-red-300 @enderror">
    @error('publication_year') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="total_pages" class="block text-sm font-medium text-slate-700">Jumlah Halaman</label>
    <input id="total_pages" name="total_pages" type="text" value="{{ old('total_pages', $book->total_pages) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('total_pages') border-red-300 @enderror"
      placeholder="Contoh: 128">
    @error('total_pages') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="category_id" class="block text-sm font-medium text-slate-700">Kategori <span class="text-red-600">*</span></label>
    <select id="category_id" name="category_id"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('category_id') border-red-300 @enderror" required>
      <option value="">Pilih kategori</option>
      @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected((string) old('category_id', $book->category_id) === (string) $category->id)>{{ $category->name }}</option>
      @endforeach
    </select>
    @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="stock" class="block text-sm font-medium text-slate-700">Stok <span class="text-red-600">*</span></label>
    <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $book->stock ?? 0) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('stock') border-red-300 @enderror" required>
    @error('stock') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="available_stock" class="block text-sm font-medium text-slate-700">Stok Tersedia <span class="text-red-600">*</span></label>
    <input id="available_stock" name="available_stock" type="number" min="0" value="{{ old('available_stock', $book->available_stock ?? 0) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('available_stock') border-red-300 @enderror" required>
    @error('available_stock') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="shelf_location" class="block text-sm font-medium text-slate-700">Lokasi Rak</label>
    <input id="shelf_location" name="shelf_location" type="text" value="{{ old('shelf_location', $book->shelf_location) }}"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('shelf_location') border-red-300 @enderror"
      placeholder="Contoh: A-01">
    @error('shelf_location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div>
    <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-red-600">*</span></label>
    <select id="status" name="status"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('status') border-red-300 @enderror" required>
      <option value="aktif"    @selected(old('status', $book->status ?: 'aktif') === 'aktif')>Aktif</option>
      <option value="nonaktif" @selected(old('status', $book->status) === 'nonaktif')>Nonaktif</option>
    </select>
    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="md:col-span-2">
    <label for="notes" class="block text-sm font-medium text-slate-700">Keterangan</label>
    <textarea id="notes" name="notes" rows="3"
      class="mt-2 block w-full rounded-lg border-slate-300 text-sm focus:border-school-700 focus:ring-school-700 @error('notes') border-red-300 @enderror">{{ old('notes', $book->notes) }}</textarea>
    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
  <a href="{{ route('books.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
  <button type="submit" class="rounded-lg bg-school-800 px-4 py-2 text-sm font-semibold text-white hover:bg-school-900">Simpan</button>
</div>
