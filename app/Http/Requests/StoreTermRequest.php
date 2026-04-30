<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'uuid', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
            // Auto-enrollment options (only for first term)
            'auto_enroll_students' => ['nullable', 'boolean'],
            'skip_final_year' => ['nullable', 'boolean'],
            'include_failed_students' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after' => 'End date must be after start date',
        ];
    }

    public function getEnrollmentOptions(): array
    {
        return [
            'skip_final_year' => $this->boolean('skip_final_year', true),
            'include_failed_students' => $this->boolean('include_failed_students', false),
        ];
    }

    public function shouldAutoEnroll(): bool
    {
        return $this->boolean('auto_enroll_students', true);
    }
}
