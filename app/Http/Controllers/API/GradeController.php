<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/grades",
     *     tags={"Grades"},
     *     summary="List grade entries",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="student_id", in="query", required=false, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="assessment_id", in="query", required=false, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Grade collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/StudentGradeResource"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = StudentGrade::with(['student', 'assessment.classSubject.subject', 'assessment.assessmentType']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->string('student_id'));
        }

        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->string('assessment_id'));
        }

        return response()->json($query->latest('recorded_at')->paginate(20));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/grades",
     *     tags={"Grades"},
     *     summary="Record or update a student grade",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GradeStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Grade saved",
     *         @OA\JsonContent(ref="#/components/schemas/StudentGradeResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'student_id' => ['required', 'uuid', 'exists:students,id'],
            'assessment_id' => ['required', 'uuid', 'exists:assessments,id'],
            'score' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $assessment = Assessment::findOrFail($payload['assessment_id']);
        abort_if($payload['score'] > $assessment->max_score, 422, 'Score cannot exceed max score.');

        $grade = DB::transaction(function () use ($payload, $assessment, $request) {
            $user = $request->user();
            $percentage = $assessment->max_score > 0 ? ($payload['score'] / $assessment->max_score) * 100 : 0;

            return StudentGrade::updateOrCreate(
                [
                    'student_id' => $payload['student_id'],
                    'assessment_id' => $payload['assessment_id'],
                ],
                [
                    'score' => $payload['score'],
                    'grade_letter' => $this->letterGrade($percentage),
                    'remarks' => $payload['remarks'] ?? null,
                    'recorded_by' => $user?->staff_id,
                    'recorded_at' => now(),
                ]
            );
        });

        return response()->json($grade->load(['student', 'assessment']), 201);
    }

    private function letterGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }
}
