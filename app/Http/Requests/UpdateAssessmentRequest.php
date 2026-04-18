<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'assessment_type_id' => ['sometimes', 'integer', 'exists:assessment_types,id'],
            'class_subject_id' => ['sometimes', 'integer', 'exists:class_subjects,id'],
            'term_id' => ['sometimes', 'integer', 'exists:terms,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'max_score' => ['sometimes', 'numeric', 'gt:0'],
            'assessment_date' => ['sometimes', 'date'],
        ];
    }
}
