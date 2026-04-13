<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,

            // Optional eager-loaded relationships
            'classrooms' => $this->whenLoaded('classrooms', function () {
                return $this->classrooms->map(function ($classroom) {
                    return [
                        'id' => $classroom->id,
                        'class_name' => $classroom->class_name,
                        'form' => $classroom->form,
                    ];
                })->values();
            }),

            'assessments' => $this->whenLoaded('assessments', function () {
                return $this->assessments->map(function ($assessment) {
                    return [
                        'id' => $assessment->id,
                        'assessment_type_id' => $assessment->assessment_type_id,
                        'date_set' => $assessment->date_set?->toDateString(),
                    ];
                })->values();
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
