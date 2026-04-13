<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_id' => $this->assessment_id,
            'student_id' => $this->student_id,
            'marks_obtained' => (float) $this->marks_obtained,
            'percentage' => (float) $this->percentage,
            'grade' => $this->grade,
            'remarks' => $this->remarks,
            
            // Optional eager-loaded relationships
            'assessment' => $this->whenLoaded('assessment', function () {
                return [
                    'id' => $this->assessment->id,
                    'date_set' => $this->assessment->date_set?->toDateString(),
                    'description' => $this->assessment->description,
                    
                    // Nested relationships
                    'type' => $this->assessment->whenLoaded('type', function () {
                        return [
                            'id' => $this->assessment->type->id,
                            'name' => $this->assessment->type->name,
                        ];
                    }),
                    
                    'subject' => $this->assessment->whenLoaded('subject', function () {
                        return [
                            'id' => $this->assessment->subject->id,
                            'code' => $this->assessment->subject->code,
                            'name' => $this->assessment->subject->name,
                        ];
                    }),
                ];
            }),
            
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'admission_number' => $this->student->admission_number,
                    'first_name' => $this->student->first_name,
                    'last_name' => $this->student->last_name,
                ];
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
