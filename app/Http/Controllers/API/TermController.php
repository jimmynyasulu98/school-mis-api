<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTermRequest;
use App\Models\Term;
use App\Services\StudentEnrollmentService;
use Illuminate\Http\Request;

class TermController extends Controller
{
    protected StudentEnrollmentService $enrollmentService;

    public function __construct(StudentEnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/terms",
     *     tags={"Terms"},
     *     summary="List terms",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Term collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TermResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Term::with('academicYear')->orderByDesc('start_date')->paginate(15);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/terms",
     *     tags={"Terms"},
     *     summary="Create a term",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TermStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Term created",
     *         @OA\JsonContent(ref="#/components/schemas/TermResource")
     *     )
     * )
     */
    public function store(StoreTermRequest $request)
    {
        $data = $request->validated();
        
        // Create the term
        $term = Term::create($data);

        // Auto-enroll students if this is the first term of the academic year
        $enrollmentSummary = null;
        if ($request->shouldAutoEnroll()) {
            $previousTerm = $term->academicYear->terms()
                ->where('id', '!=', $term->id)
                ->orderBy('start_date', 'desc')
                ->first();

            if (!$previousTerm) {
                // This is the first term - auto-enroll students
                $enrollmentSummary = $this->enrollmentService->autoEnrollStudents(
                    $term,
                    $request->getEnrollmentOptions()
                );
            } else {
                // Subsequent term - enroll active students
                $enrollmentSummary = $this->enrollmentService->enrollStudentsToTerm($term);
            }
        }

        return response()->json([
            'data' => $term->load('academicYear'),
            'enrollment_summary' => $enrollmentSummary,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/terms/{term}",
     *     tags={"Terms"},
     *     summary="Show a term",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="term", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Term detail",
     *         @OA\JsonContent(ref="#/components/schemas/TermResource")
     *     )
     * )
     */
    public function show(Term $term)
    {
        return $term->load('academicYear');
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/terms/{term}",
     *     tags={"Terms"},
     *     summary="Update a term",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="term", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TermUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Term updated",
     *         @OA\JsonContent(ref="#/components/schemas/TermResource")
     *     )
     * )
     */
    public function update(Request $request, Term $term)
    {
        $term->update($request->validate([
            'name' => ['sometimes', 'string', 'max:30'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'is_current' => ['sometimes', 'boolean'],
        ]));

        return $term->fresh('academicYear');
    }
}
