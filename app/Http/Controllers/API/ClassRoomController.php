<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/classes",
     *     tags={"Classes"},
     *     summary="List classes",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Class collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ClassRoomResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return ClassRoom::with('classTeacher')->latest()->paginate(15);
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
     *         response=200,
     *         description="Class created",
     *         @OA\JsonContent(ref="#/components/schemas/ClassRoomResource")
     *     )
     * )
     */
    public function store(Request $request)
    {
        return ClassRoom::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stream' => ['nullable', 'string', 'max:20'],
            'section' => ['nullable', 'string', 'max:30'],
            'class_teacher_id' => ['nullable', 'uuid', 'exists:staff,id'],
        ]));
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
