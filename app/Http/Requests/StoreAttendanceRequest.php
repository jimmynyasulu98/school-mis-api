<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'uuid', 'exists:students,id'],
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'date_marked' => ['required', 'date'],
            'status' => ['required', 'string', 'in:present,absent,excused'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ];
    }
}
