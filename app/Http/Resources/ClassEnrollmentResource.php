<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassEnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @property int $id
     * @property int $student_id
     * @property int $class_room_id
     * @property string $enrolled_at
     * @property bool $is_active
     * @property \stdClass|null $student
     * @property \stdClass|null $classroom
     * @property string $created_at
     * @property string $updated_at
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'class_room_id' => $this->class_room_id,
            'enrolled_at' => $this->enrolled_at?->toDateString(),
            'is_active' => (bool) $this->is_active,

            // Optional eager-loaded relationships
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'admission_number' => $this->student->admission_number,
                    'first_name' => $this->student->first_name,
                    'last_name' => $this->student->last_name,
                ];
            }),

            'classroom' => $this->whenLoaded('classroom', function () {
                return [
                    'id' => $this->classroom->id,
                    'class_name' => $this->classroom->class_name,
                    'form' => $this->classroom->form,
                    'stream' => $this->classroom->stream,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
