<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/assessments",
     *     tags={"Assessments"},
     *     summary="List assessments",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Assessment collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AssessmentResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Assessment::with('grades')->latest('assessment_date')->paginate(15);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/assessments",
     *     tags={"Assessments"},
     *     summary="Create an assessment",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment created",
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentResource")
     *     )
     * )
     */
    public function store(Request $request)
    {
        return Assessment::create($request->validate([
            'class_subject_id' => ['required', 'integer', 'exists:class_subjects,id'],
            'term_id' => ['required', 'integer', 'exists:terms,id'],
            'assessment_type_id' => ['required', 'integer', 'exists:assessment_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'max_score' => ['required', 'numeric', 'min:0'],
            'assessment_date' => ['required', 'date'],
        ]));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/assessments/{assessment}",
     *     tags={"Assessments"},
     *     summary="Show an assessment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="assessment", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment detail",
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentResource")
     *     )
     * )
     */
    public function show(Assessment $assessment)
    {
        return $assessment->load('grades');
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/assessments/{assessment}",
     *     tags={"Assessments"},
     *     summary="Update an assessment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="assessment", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment updated",
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentResource")
     *     )
     * )
     */
    public function update(Request $request, Assessment $assessment)
    {
        $assessment->update($request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'max_score' => ['sometimes', 'numeric', 'min:0'],
            'assessment_date' => ['sometimes', 'date'],
        ]));

        return $assessment->fresh('grades');
    }
}
