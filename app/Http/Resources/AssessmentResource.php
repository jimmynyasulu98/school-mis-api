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
            'class_subject_id' => $this->class_subject_id,
            'term_id' => $this->term_id,
            'title' => $this->title,
            'max_score' => (float) $this->max_score,
            'assessment_date' => $this->assessment_date?->toDateString(),
            'assessment_type' => $this->whenLoaded('assessmentType', function () {
                return [
                    'id' => $this->assessmentType->id,
                    'name' => $this->assessmentType->name,
                    'code' => $this->assessmentType->code,
                    'weight' => (float) $this->assessmentType->weight,
                ];
            }),
            'class_subject' => $this->whenLoaded('classSubject', function () {
                return [
                    'id' => $this->classSubject->id,
                    'class_room_id' => $this->classSubject->class_room_id,
                    'subject_id' => $this->classSubject->subject_id,
                    'teacher_id' => $this->classSubject->teacher_id,
                    'core_teacher_id' => $this->classSubject->teacher_id,
                    'class_room' => $this->when(
                        $this->classSubject->relationLoaded('classRoom') && $this->classSubject->classRoom !== null,
                        fn() => [
                            'id' => $this->classSubject->classRoom->id,
                            'name' => $this->classSubject->classRoom->name,
                            'stream' => $this->classSubject->classRoom->stream,
                            'section' => $this->classSubject->classRoom->section,
                        ]
                    ),
                    'subject' => $this->when(
                        $this->classSubject->relationLoaded('subject') && $this->classSubject->subject !== null,
                        fn() => [
                            'id' => $this->classSubject->subject->id,
                            'name' => $this->classSubject->subject->name,
                            'code' => $this->classSubject->subject->code,
                        ]
                    ),
                    'teacher_assignments' => $this->when(
                        $this->classSubject->relationLoaded('teacherAssignments'),
                        fn () => $this->classSubject->teacherAssignments->map(function ($assignment) {
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
                        })->values()
                    ),
                ];
            }),
            'term' => $this->whenLoaded('term', function () {
                return [
                    'id' => $this->term->id,
                    'name' => $this->term->name,
                    'academic_year_id' => $this->term->academic_year_id,
                    'start_date' => $this->term->start_date?->toDateString(),
                    'end_date' => $this->term->end_date?->toDateString(),
                    'academic_year' => $this->when(
                        $this->term->relationLoaded('academicYear') && $this->term->academicYear !== null,
                        fn() => [
                            'id' => $this->term->academicYear->id,
                            'name' => $this->term->academicYear->name,
                            'is_current' => (bool) $this->term->academicYear->is_current,
                        ]
                    ),
                ];
            }),
            'grades' => $this->whenLoaded('grades', function () {
                return $this->grades->map(function ($grade) {
                    return [
                        'id' => $grade->id,
                        'student_id' => $grade->student_id,
                        'score' => (float) $grade->score,
                        'grade_letter' => $grade->grade_letter,
                        'remarks' => $grade->remarks,
                    ];
                })->values();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
