<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_code' => ['required', 'string', 'max:50', 'unique:books,book_code'],
            'isbn' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:'.now()->year],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'available_stock' => ['required', 'integer', 'min:0', 'lte:stock'],
            'shelf_location' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }
}
