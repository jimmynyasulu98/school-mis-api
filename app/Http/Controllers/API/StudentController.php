<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/students",
     *     tags={"Students"},
     *     summary="List all students with pagination and optional included relationships",
     *     description="Retrieve a paginated list of students. Use 'includes' parameter to eager load related resources (currentClassRoom, guardians, enrollments, feeAccounts).",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/per_page"),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         description="Comma-separated list of relationships to include. Available: currentClassRoom, guardians, enrollments, feeAccounts",
     *         example="currentClassRoom,guardians",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedStudentResponse")
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
        return StudentResource::collection(
            $this->applyPaginationAndIncludes(
                Student::query(),
                $request,
                10
            )
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/students",
     *     tags={"Students"},
     *     summary="Create a new student",
     *     description="Create a new student record with optional guardian associations",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Student data to create",
     *         @OA\JsonContent(ref="#/components/schemas/StudentStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/StudentResource")
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
        $payload = $request->validate([
            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:30'],
            'current_class_room_id' => ['nullable', 'integer', 'exists:class_rooms,id'],
            'guardians' => ['array'],
            'guardians.*.id' => ['required_with:guardians', 'uuid', 'exists:guardians,id'],
            'guardians.*.is_primary' => ['nullable', 'boolean'],
            'guardians.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $student = DB::transaction(function () use ($payload) {
            $guardians = $payload['guardians'] ?? [];
            unset($payload['guardians']);

            $student = Student::create($payload);

            if ($guardians !== []) {
                $student->guardians()->sync(collect($guardians)->mapWithKeys(fn(array $guardian) => [
                    $guardian['id'] => [
                        'is_primary' => $guardian['is_primary'] ?? false,
                        'notes' => $guardian['notes'] ?? null,
                    ],
                ]));
            }

            return $student->load(['currentClassRoom', 'guardians']);
        });

        return StudentResource::make($student)->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/students/{student}",
     *     tags={"Students"},
     *     summary="Show a specific student",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         description="Student ID (UUID)",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/StudentResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
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
    public function show(Student $student)
    {
        return StudentResource::make($student->load(['currentClassRoom', 'guardians']));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/students/{student}",
     *     tags={"Students"},
     *     summary="Update a student",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         description="Student ID (UUID)",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StudentUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/StudentResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
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
    public function update(Request $request, Student $student)
    {
        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:30'],
            'current_class_room_id' => ['nullable', 'integer', 'exists:class_rooms,id'],
        ]);

        $student->update($payload);

        return StudentResource::make($student->load(['currentClassRoom', 'guardians']));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/students/{student}",
     *     tags={"Students"},
     *     summary="Delete a student",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         description="Student ID (UUID)",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Student deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
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
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->noContent();
    }
}
