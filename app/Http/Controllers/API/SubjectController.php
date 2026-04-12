<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/subjects",
     *     tags={"Subjects"},
     *     summary="List subjects",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Subject collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SubjectResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Subject::latest()->paginate(15);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/subjects",
     *     tags={"Subjects"},
     *     summary="Create a subject",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SubjectStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subject created",
     *         @OA\JsonContent(ref="#/components/schemas/SubjectResource")
     *     )
     * )
     */
    public function store(Request $request)
    {
        return Subject::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'is_core' => ['nullable', 'boolean'],
        ]));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/subjects/{subject}",
     *     tags={"Subjects"},
     *     summary="Show a subject",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="subject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Subject detail",
     *         @OA\JsonContent(ref="#/components/schemas/SubjectResource")
     *     )
     * )
     */
    public function show(Subject $subject)
    {
        return $subject;
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/subjects/{subject}",
     *     tags={"Subjects"},
     *     summary="Update a subject",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="subject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SubjectUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subject updated",
     *         @OA\JsonContent(ref="#/components/schemas/SubjectResource")
     *     )
     * )
     */
    public function update(Request $request, Subject $subject)
    {
        $subject->update($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:20', 'unique:subjects,code,'.$subject->id],
            'is_core' => ['sometimes', 'boolean'],
        ]));

        return $subject->fresh();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/subjects/{subject}",
     *     tags={"Subjects"},
     *     summary="Delete a subject",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="subject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Subject deleted",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     )
     * )
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Subject deleted.']);
    }
}
