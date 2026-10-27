<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FindLoanStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_code' => ['required', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'member_code' => 'kode anggota',
        ];
    }
}
