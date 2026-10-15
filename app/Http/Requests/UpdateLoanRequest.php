<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $loanId = $this->route('loan')?->id ?? $this->route('loan');

        return [
            'loan_code' => ['required', 'string', 'max:50', Rule::unique('loans', 'loan_code')->ignore($loanId)],
            'student_id' => ['required', 'exists:students,id'],
            'user_id' => ['required', 'exists:users,id'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'returned_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['dipinjam', 'terlambat', 'dikembalikan'])],
            'extension_count' => ['nullable', 'integer', 'min:0', 'max:255'],
            'book_ids' => ['nullable', 'array'],
            'book_ids.*' => ['integer', 'exists:books,id'],
        ];
    }
}
