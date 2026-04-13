<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AssessmentResource;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/assessments",
     *     tags={"Assessments"},
     *     summary="List assessments with pagination and optional included relationships",
     *     description="Retrieve a paginated list of assessments. Use 'includes' parameter to eager load related resources (type, subject, classroom, academicYear, term, grades).",
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
     *         description="Comma-separated list of relationships to include. Available: type, subject, classroom, academicYear, term, grades",
     *         example="type,subject,grades",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedAssessmentResponse")
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
        return AssessmentResource::collection(
            $this->applyPaginationAndIncludes(
                Assessment::query(),
                $request,
                10
            )
        );
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
     *         response=201,
     *         description="Assessment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentResource")
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
    public function store(StoreAssessmentRequest $request)
    {
        $assessment = Assessment::create($request->validated());

        return (new AssessmentResource($assessment->load('type', 'subject', 'classroom', 'academicYear', 'term')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/assessments/{assessment}",
     *     tags={"Assessments"},
     *     summary="Show an assessment details",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="assessment",
     *         in="path",
     *         description="Assessment ID (UUID)",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Not Found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     )
     * )
     */
    public function show(Assessment $assessment)
    {
        return AssessmentResource::make($assessment->load('type', 'subject', 'classroom', 'academicYear', 'term', 'grades'));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/assessments/{assessment}",
     *     tags={"Assessments"},
     *     summary="Update an assessment",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="assessment",
     *         in="path",
     *         description="Assessment ID (UUID)",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assessment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AssessmentResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Not Found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     )
     * )
     */
    public function update(UpdateAssessmentRequest $request, Assessment $assessment)
    {
        $assessment->update($request->validated());

        return AssessmentResource::make($assessment->fresh()->load('type', 'subject', 'classroom', 'academicYear', 'term'));
    }
}
