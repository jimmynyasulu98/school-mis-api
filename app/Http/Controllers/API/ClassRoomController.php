<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ClassRoomResource;
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
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/per_page"),
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
    public function store(Request $request)
    {
        $classroom = ClassRoom::create($request->validate([
            'class_name' => ['required', 'string', 'max:100', 'unique:class_rooms,class_name'],
            'form' => ['required', 'integer'],
            'stream' => ['nullable', 'string', 'max:50'],
            'class_teacher_id' => ['nullable', 'integer', 'exists:staff,id'],
        ]));

        return (new ClassRoomResource($classroom->load('classTeacher')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/classes/{class}",
     *     tags={"Classes"},
     *     summary="Show a class",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="class", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Class detail",
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomResource")
     *     )
     * )
     */
    public function show(ClassRoom $class)
    {
        return $class->load('classTeacher');
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/classes/{class}",
     *     tags={"Classes"},
     *     summary="Update a class",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="class", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class updated",
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomResource")
     *     )
     * )
     */
    public function update(Request $request, ClassRoom $class)
    {
        $class->update($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'stream' => ['sometimes', 'nullable', 'string', 'max:20'],
            'section' => ['sometimes', 'nullable', 'string', 'max:30'],
            'class_teacher_id' => ['sometimes', 'nullable', 'uuid', 'exists:staff,id'],
        ]));

        return $class->fresh('classTeacher');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/classes/{class}",
     *     tags={"Classes"},
     *     summary="Delete a class",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="class", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Class deleted",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     )
     * )
     */
    public function destroy(ClassRoom $class)
    {
        $class->delete();
        return response()->json(['message' => 'Class deleted.']);
    }
}
