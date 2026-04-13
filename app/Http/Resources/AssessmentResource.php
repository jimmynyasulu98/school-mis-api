<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_type_id' => $this->assessment_type_id,
            'subject_id' => $this->subject_id,
            'class_room_id' => $this->class_room_id,
            'academic_year_id' => $this->academic_year_id,
            'term_id' => $this->term_id,
            'date_set' => $this->date_set?->toDateString(),
            'description' => $this->description,
            
            // Optional eager-loaded relationships
            'type' => $this->whenLoaded('type', function () {
                return [
                    'id' => $this->type->id,
                    'name' => $this->type->name,
                    'description' => $this->type->description,
                ];
            }),
            
            'subject' => $this->whenLoaded('subject', function () {
                return [
                    'id' => $this->subject->id,
                    'code' => $this->subject->code,
                    'name' => $this->subject->name,
                ];
            }),
            
            'classroom' => $this->whenLoaded('classroom', function () {
                return [
                    'id' => $this->classroom->id,
                    'class_name' => $this->classroom->class_name,
                    'form' => $this->classroom->form,
                ];
            }),
            
            'academic_year' => $this->whenLoaded('academicYear', function () {
                return [
                    'id' => $this->academicYear->id,
                    'year' => $this->academicYear->year,
                ];
            }),
            
            'term' => $this->whenLoaded('term', function () {
                return [
                    'id' => $this->term->id,
                    'name' => $this->term->name,
                    'term_number' => $this->term->term_number,
                ];
            }),
            
            'grades' => $this->whenLoaded('grades', function () {
                return $this->grades->map(function ($grade) {
                    return [
                        'id' => $grade->id,
                        'student_id' => $grade->student_id,
                        'marks_obtained' => (float) $grade->marks_obtained,
                        'percentage' => (float) $grade->percentage,
                        'grade' => $grade->grade,
                        'remarks' => $grade->remarks,
                    ];
                })->values();
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
