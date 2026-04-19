<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignClassSubjectTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'uuid', 'exists:staff,id'],
            'is_core' => ['sometimes', 'boolean'],
        ];
    }
}
