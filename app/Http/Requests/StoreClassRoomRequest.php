<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'class_name' => ['required', 'string', 'max:100', 'unique:class_rooms,class_name'],
            'form' => ['required', 'integer'],
            'stream' => ['nullable', 'string', 'max:50'],
            'class_teacher_id' => ['nullable', 'integer', 'exists:staff,id'],
        ];
    }
}
