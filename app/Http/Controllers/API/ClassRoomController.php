<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ClassRoomResource;
use App\Http\Requests\StoreClassRoomRequest;
use App\Http\Requests\UpdateClassRoomRequest;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/classes",
     *     tags={"Classes"},
     *     summary="List classes with pagination and optional included relationships",
     *     description="Retrieve a paginated list of classes. Use 'includes' parameter to eager load related resources (classTeacher, students, subjects, enrollments).",
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
     *         description="Comma-separated list of relationships to include. Available: classTeacher, students, subjects, enrollments",
     *         example="classTeacher,students",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedClassRoomResponse")
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
        return ClassRoomResource::collection(
            $this->applyPaginationAndIncludes(
                ClassRoom::query(),
                $request,
                10
            )
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/classes",
     *     tags={"Classes"},
     *     summary="Create a class",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Class created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomResource")
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
    public function store(StoreClassRoomRequest $request)
    {
        $classroom = ClassRoom::create($request->validated());

        return (new ClassRoomResource($classroom->load('classTeacher')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/classes/{classroom}",
     *     tags={"Classes"},
     *     summary="Show a class details",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="classroom",
     *         in="path",
     *         description="Classroom ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found",
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
    public function show(ClassRoom $classroom)
    {
        return ClassRoomResource::make($classroom->load('classTeacher', 'students', 'subjects', 'enrollments'));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/classes/{classroom}",
     *     tags={"Classes"},
     *     summary="Update a class",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="classroom",
     *         in="path",
     *         description="Classroom ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found",
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
    public function update(UpdateClassRoomRequest $request, ClassRoom $classroom)
    {
        $classroom->update($request->validated());

        return ClassRoomResource::make($classroom->fresh()->load('classTeacher'));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/classes/{classroom}",
     *     tags={"Classes"},
     *     summary="Delete a class",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="classroom",
     *         in="path",
     *         description="Classroom ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Class deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found",
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
    public function destroy(ClassRoom $classroom)
    {
        $classroom->delete();
        return response()->noContent();
    }
}
