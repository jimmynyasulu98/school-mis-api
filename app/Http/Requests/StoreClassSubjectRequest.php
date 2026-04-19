<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
                Rule::unique('class_subjects')->where(fn ($query) => $query->where('class_room_id', $this->input('class_room_id'))),
            ],
            'teacher_assignments' => ['sometimes', 'array', 'min:1'],
            'teacher_assignments.*.teacher_id' => ['required', 'uuid', 'exists:staff,id', 'distinct'],
            'teacher_assignments.*.is_core' => ['sometimes', 'boolean'],
        ];
    }
}
