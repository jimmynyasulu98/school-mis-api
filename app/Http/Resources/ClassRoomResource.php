<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_name' => $this->class_name,
            'form' => $this->form,
            'stream' => $this->stream,
            'class_teacher_id' => $this->class_teacher_id,

            // Optional eager-loaded relationships
            'class_teacher' => $this->whenLoaded('classTeacher', function () {
                return [
                    'id' => $this->classTeacher->id,
                    'employee_number' => $this->classTeacher->employee_number,
                    'first_name' => $this->classTeacher->first_name,
                    'last_name' => $this->classTeacher->last_name,
                ];
            }),

            'students' => $this->whenLoaded('students', function () {
                return $this->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'admission_number' => $student->admission_number,
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                    ];
                })->values();
            }),

            'subjects' => $this->whenLoaded('subjects', function () {
                return $this->subjects->map(function ($subject) {
                    return [
                        'id' => $subject->id,
                        'code' => $subject->code,
                        'name' => $subject->name,
                    ];
                })->values();
            }),

            'enrollments' => $this->whenLoaded('enrollments', function () {
                return $this->enrollments->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->id,
                        'student_id' => $enrollment->student_id,
                        'enrolled_at' => $enrollment->enrolled_at?->toDateString(),
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
