<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentGrade;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/students/{student}/report-card",
     *     tags={"Reports"},
     *     summary="Get a student's report card",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="student", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="term_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Report card",
     *         @OA\JsonContent(ref="#/components/schemas/ReportCardResponse")
     *     )
     * )
     */
    public function show(Request $request, Student $student)
    {
        $grades = StudentGrade::query()
            ->with([
                'assessment.classSubject.subject',
                'assessment.assessmentType',
                'assessment.term',
            ])
            ->where('student_id', $student->id)
            ->when($request->filled('term_id'), function ($query) use ($request) {
                $query->whereHas('assessment', fn ($assessment) => $assessment->where('term_id', $request->integer('term_id')));
            })
            ->get();

        $subjects = $grades
            ->groupBy(fn (StudentGrade $grade) => $grade->assessment->classSubject->subject->name)
            ->map(function ($subjectGrades, $subjectName) {
                $average = round($subjectGrades->avg('score'), 2);

                return [
                    'subject' => $subjectName,
                    'average_score' => $average,
                    'grade_letter' => $this->letterGrade($average),
                    'assessments' => $subjectGrades->map(fn (StudentGrade $grade) => [
                        'assessment' => $grade->assessment->title,
                        'type' => $grade->assessment->assessmentType->name,
                        'score' => $grade->score,
                        'max_score' => $grade->assessment->max_score,
                        'grade_letter' => $grade->grade_letter,
                        'remarks' => $grade->remarks,
                    ])->values(),
                ];
            })->values();

        $attendance = $student->attendanceRecords()
            ->when($request->filled('term_id'), fn ($query) => $query->whereIn('class_room_id', [$student->current_class_room_id]))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $feeBalance = (float) $student->feeAccounts()->sum('balance');

        return response()->json([
            'student' => [
                'id' => $student->id,
                'admission_number' => $student->admission_number,
                'name' => trim($student->first_name.' '.$student->last_name),
                'class_room_id' => $student->current_class_room_id,
            ],
            'summary' => [
                'subject_count' => $subjects->count(),
                'overall_average' => round($subjects->avg('average_score') ?? 0, 2),
                'overall_grade' => $this->letterGrade($subjects->avg('average_score') ?? 0),
                'fee_balance' => $feeBalance,
                'attendance' => $attendance,
            ],
            'subjects' => $subjects,
            'grade_history' => $student->gradeHistories()->latest()->get(),
        ]);
    }

    private function letterGrade(float $score): string
    {
        return match (true) {
            $score >= 80 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            $score >= 50 => 'D',
            default => 'F',
        };
    }
}
