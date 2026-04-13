<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AttendanceResource;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/attendance",
     *     tags={"Attendance"},
     *     summary="List attendance records with pagination and optional included relationships",
     *     description="Retrieve a paginated list of attendance records. Use 'includes' parameter to eager load related resources (student, classroom).",
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
     *         description="Comma-separated list of relationships to include. Available: student, classroom",
     *         example="student,classroom",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         description="Filter by student ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="class_room_id",
     *         in="query",
     *         description="Filter by classroom ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attendance collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedAttendanceResponse")
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
        $query = Attendance::query();

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->string('student_id'));
        }

        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->integer('class_room_id'));
        }

        return AttendanceResource::collection(
            $this->applyPaginationAndIncludes($query, $request, 10)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/attendance",
     *     tags={"Attendance"},
     *     summary="Record attendance for a student",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AttendanceStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Attendance recorded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AttendanceResource")
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
    public function store(StoreAttendanceRequest $request)
    {
        $payload = $request->validated();

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $payload['student_id'],
                'class_room_id' => $payload['class_room_id'],
                'date_marked' => $payload['date_marked'],
            ],
            $payload
        );

        return (new AttendanceResource($attendance->load('student', 'classroom')))
            ->response()
            ->setStatusCode(201);
    }
}
