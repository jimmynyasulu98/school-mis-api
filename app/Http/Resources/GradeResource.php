<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @property string $id
     * @property string $assessment_id
     * @property string $student_id
     * @property float $score
     * @property string|null $grade_letter
     * @property string|null $remarks
     * @property string|null $recorded_by
     * @property string|null $recorded_at
     *
     * @property \stdClass|null $assessment
     * @property \stdClass|null $student
     * @property string $created_at
     * @property string $updated_at
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_id' => $this->assessment_id,
            'student_id' => $this->student_id,
            'score' => (float) $this->score,
            'grade_letter' => $this->grade_letter,
            'remarks' => $this->remarks,
            
            // Optional eager-loaded relationships
            'assessment' => $this->whenLoaded('assessment', function () {
                return [
                    'id' => $this->assessment->id,
                    'title' => $this->assessment->title,
                    'max_score' => $this->assessment->max_score,
                    'assessment_date' => $this->assessment->assessment_date?->toDateString(),
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
