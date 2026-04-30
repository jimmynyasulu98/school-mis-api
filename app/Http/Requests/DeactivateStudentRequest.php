<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;

class DeactivateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500', 'in:' . implode(',', [
                'left_school',
                'failed_not_returning',
                'transferred_school',
                'withdrawn_by_guardian',
                'other'
            ])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Deactivation reason is required',
            'reason.in' => 'Invalid deactivation reason provided',
        ];
    }
}
