<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $categories = Category::withCount('books')
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('categories.create', ['category' => new Category()]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        try {
            Category::create($request->validated());

            return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Kategori gagal ditambahkan. Periksa kembali data yang diisi.');
        }
    }

    public function show(Category $category): View
    {
        $category->loadCount('books');

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $category->update($request->validated());

            return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Kategori gagal diperbarui. Periksa kembali data yang diisi.');
        }
    }

    public function destroy(Category $category): RedirectResponse
    {
        try {
            $category->delete();

            return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (Throwable) {
            return redirect()->route('categories.index')->with('error', 'Kategori gagal dihapus karena masih digunakan oleh data buku.');
        }
    }
}
