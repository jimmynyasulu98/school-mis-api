<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'assessment_type_id' => ['required', 'integer', 'exists:assessment_types,id'],
            'class_subject_id' => ['required', 'integer', 'exists:class_subjects,id'],
            'term_id' => ['required', 'integer', 'exists:terms,id'],
            'title' => ['required', 'string', 'max:255'],
            'max_score' => ['required', 'numeric', 'gt:0'],
            'assessment_date' => ['required', 'date'],
        ];
    }
}
