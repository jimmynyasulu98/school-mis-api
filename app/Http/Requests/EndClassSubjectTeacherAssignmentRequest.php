<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndClassSubjectTeacherAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ends_on' => ['sometimes', 'date'],
        ];
    }
}
