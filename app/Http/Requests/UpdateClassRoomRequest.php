<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'stream' => ['sometimes', 'nullable', 'string', 'max:50'],
            'class_teacher_id' => ['sometimes', 'nullable', 'integer', 'exists:staff,id'],
        ];
    }
}
