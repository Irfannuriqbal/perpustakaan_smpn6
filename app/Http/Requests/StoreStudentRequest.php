<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no'           => ['required', 'string', 'max:50', 'unique:students,no'],
            'member_type'  => ['required', Rule::in([Student::TYPE_STUDENT, Student::TYPE_STAFF])],
            'member_code'  => ['required', 'string', 'max:50', 'unique:students,member_code'],
            'name'         => ['required', 'string', 'max:255'],
            'gender'       => ['nullable', 'string', 'max:20'],
            'class'        => ['nullable', 'string', 'max:50'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['nullable', 'string'],
            'status'       => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
