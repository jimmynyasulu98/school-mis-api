<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:30'],
            'current_class_room_id' => ['nullable', 'integer', 'exists:class_rooms,id'],
            'guardians' => ['array'],
            'guardians.*.id' => ['required_with:guardians', 'uuid', 'exists:guardians,id'],
            'guardians.*.is_primary' => ['nullable', 'boolean'],
            'guardians.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
