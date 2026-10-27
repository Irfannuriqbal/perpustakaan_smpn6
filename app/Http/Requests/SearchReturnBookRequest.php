<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchReturnBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_code' => ['required', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'book_code' => 'kode buku',
        ];
    }
}
