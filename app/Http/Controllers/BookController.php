<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Services\CodeImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $books = Book::with('category')
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('book_code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('books.index', compact('books', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.create', ['book' => new Book(), 'categories' => $categories]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        try {
            Book::create($request->validated());

            return redirect()->route('books.index')->with('success', 'Data buku berhasil ditambahkan.');
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Data buku gagal ditambahkan. Periksa kembali data yang diisi.');
        }
    }

    public function show(Book $book): View
    {
        $book->load('category');

        return view('books.show', compact('book'));
    }

    public function printBarcodes(Request $request, CodeImageService $codes): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $books = Book::with('category')
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('book_code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('title')
            ->orderBy('book_code')
            ->get();

        $barcodes = $books
            ->mapWithKeys(fn (Book $book): array => [$book->id => $codes->barcodeSvg($book->book_code)]);

        return view('books.print-barcodes', compact('books', 'barcodes'));
    }

    public function printBarcode(Book $book, CodeImageService $codes): View
    {
        $book->load('category');

        $books = collect([$book]);
        $barcodes = collect([$book->id => $codes->barcodeSvg($book->book_code)]);

        return view('books.print-barcodes', compact('books', 'barcodes'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        try {
            $book->update($request->validated());

            return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui.');
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Data buku gagal diperbarui. Periksa kembali data yang diisi.');
        }
    }

    public function destroy(Book $book): RedirectResponse
    {
        try {
            $book->delete();

            return redirect()->route('books.index')->with('success', 'Data buku berhasil dihapus.');
        } catch (Throwable) {
            return redirect()->route('books.index')->with('error', 'Data buku gagal dihapus karena masih terhubung dengan data peminjaman.');
        }
    }
}
