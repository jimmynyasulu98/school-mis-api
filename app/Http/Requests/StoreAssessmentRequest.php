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
            'assessment_type_id' => ['required', 'uuid', 'exists:assessment_types,id'],
            'subject_id' => ['required', 'uuid', 'exists:subjects,id'],
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'academic_year_id' => ['required', 'uuid', 'exists:academic_years,id'],
            'term_id' => ['required', 'uuid', 'exists:terms,id'],
            'date_set' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
