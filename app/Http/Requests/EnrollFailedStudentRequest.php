<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollFailedStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'uuid', 'exists:students,id'],
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'term_id' => ['required', 'uuid', 'exists:terms,id'],
            'enrollment_type' => ['required', 'string', 'in:REPEAT,TRANSFER'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'enrollment_type.in' => 'Enrollment type must be REPEAT or TRANSFER',
        ];
    }
}
