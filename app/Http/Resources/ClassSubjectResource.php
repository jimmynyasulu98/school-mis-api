<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_room_id' => $this->class_room_id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'core_teacher_id' => $this->teacher_id,
            'class_room' => $this->whenLoaded('classRoom', function () {
                return [
                    'id' => $this->classRoom->id,
                    'name' => $this->classRoom->name,
                    'stream' => $this->classRoom->stream,
                    'section' => $this->classRoom->section,
                ];
            }),
            'subject' => $this->whenLoaded('subject', function () {
                return [
                    'id' => $this->subject->id,
                    'name' => $this->subject->name,
                    'code' => $this->subject->code,
                    'is_core' => (bool) $this->subject->is_core,
                ];
            }),
            'core_teacher' => $this->whenLoaded('teacher', function () {
                if ($this->teacher === null) {
                    return null;
                }

                return [
                    'id' => $this->teacher->id,
                    'employee_number' => $this->teacher->employee_number,
                    'first_name' => $this->teacher->first_name,
                    'last_name' => $this->teacher->last_name,
                ];
            }),
            'teacher_assignments' => $this->whenLoaded('teacherAssignments', function () {
                return $this->teacherAssignments->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'teacher_id' => $assignment->teacher_id,
                        'is_core' => (bool) $assignment->is_core,
                        'teacher' => $assignment->relationLoaded('teacher') && $assignment->teacher !== null
                            ? [
                                'id' => $assignment->teacher->id,
                                'employee_number' => $assignment->teacher->employee_number,
                                'first_name' => $assignment->teacher->first_name,
                                'last_name' => $assignment->teacher->last_name,
                            ]
                            : null,
                    ];
                })->values();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
