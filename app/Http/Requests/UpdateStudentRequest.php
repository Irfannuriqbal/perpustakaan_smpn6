<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('member')?->id ?? $this->route('member');

        return [
            'no'           => ['required', 'string', 'max:50', Rule::unique('students', 'no')->ignore($studentId)],
            'member_type'  => ['required', Rule::in([Student::TYPE_STUDENT, Student::TYPE_STAFF])],
            'member_code'  => ['required', 'string', 'max:50', Rule::unique('students', 'member_code')->ignore($studentId)],
            'name'         => ['required', 'string', 'max:255'],
            'gender'       => ['nullable', 'string', 'max:20'],
            'class'        => ['nullable', 'string', 'max:50'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['nullable', 'string'],
            'status'       => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }
}
