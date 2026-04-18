<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\GradeResource;
use App\Http\Requests\StoreGradeRequest;
use App\Models\Assessment;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/grades",
     *     tags={"Grades"},
     *     summary="List grade entries with pagination and optional included relationships",
     *     description="Retrieve a paginated list of grade entries. Use 'includes' parameter to eager load related resources (assessment, student).",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of records per page (default 10, max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, maximum=100, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         description="Comma-separated list of relationships to include. Available: assessment, student",
     *         example="assessment,student",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         description="Filter by student ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="assessment_id",
     *         in="query",
     *         description="Filter by assessment ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Grade collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedGradeResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Insufficient permissions",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = StudentGrade::query();

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->string('student_id'));
        }

        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->string('assessment_id'));
        }

        return GradeResource::collection(
            $this->applyPaginationAndIncludes($query, $request, 10)
        );
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
     *         description="Grade recorded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/GradeResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     )
     * )
     */
    public function store(StoreGradeRequest $request)
    {
        $payload = $request->validated();
        $assessment = Assessment::findOrFail($payload['assessment_id']);

        $grade = DB::transaction(function () use ($payload, $assessment) {
            return StudentGrade::updateOrCreate(
                [
                    'student_id' => $payload['student_id'],
                    'assessment_id' => $payload['assessment_id'],
                ],
                [
                    'score' => $payload['marks_obtained'],
                    'grade_letter' => $this->letterGrade($payload['marks_obtained'], $assessment->max_score),
                    'remarks' => $payload['remarks'] ?? null,
                ]
            );
        });

        return (new GradeResource($grade->load('assessment', 'student')))
            ->response()
            ->setStatusCode(201);
    }

    private function letterGrade(float $score, float $maxScore): string
    {
        $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
        
        return match (true) {
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }
}
