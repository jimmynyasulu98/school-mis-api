<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\AssignClassSubjectTeacherRequest;
use App\Http\Requests\StoreClassSubjectRequest;
use App\Http\Resources\ClassSubjectResource;
use App\Models\ClassSubject;
use App\Models\ClassSubjectTeacher;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassSubjectController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/class-subjects",
     *     tags={"Class Subjects"},
     *     summary="List class subjects and teacher assignments",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Class subject collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedClassSubjectResponse")
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = ClassSubject::query()->with($this->defaultIncludes());

        return ClassSubjectResource::collection(
            $this->applyPaginationAndIncludes($query, $request, 10)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/class-subjects",
     *     tags={"Class Subjects"},
     *     summary="Create a class subject with optional teacher assignments",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ClassSubjectStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Class subject created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassSubjectResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(StoreClassSubjectRequest $request)
    {
        $payload = $request->validated();
        $teacherAssignments = $payload['teacher_assignments'] ?? [];

        $classSubject = DB::transaction(function () use ($payload, $teacherAssignments) {
            $classSubject = ClassSubject::create([
                'class_room_id' => $payload['class_room_id'],
                'subject_id' => $payload['subject_id'],
            ]);

            $this->syncTeacherAssignments($classSubject, $teacherAssignments);

            return $classSubject;
        });

        return (new ClassSubjectResource($classSubject->fresh()->load($this->defaultIncludes())))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/class-subjects/{classSubject}",
     *     tags={"Class Subjects"},
     *     summary="Show a class subject and all teacher assignments",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="classSubject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Class subject retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassSubjectResource")
     *     )
     * )
     */
    public function show(ClassSubject $classSubject)
    {
        return ClassSubjectResource::make($classSubject->load($this->defaultIncludes()));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/class-subjects/{classSubject}/teachers",
     *     tags={"Class Subjects"},
     *     summary="Assign or update a teacher on a class subject",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="classSubject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ClassSubjectTeacherAssignRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Teacher assignment saved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassSubjectResource")
     *     )
     * )
     */
    public function assignTeacher(AssignClassSubjectTeacherRequest $request, ClassSubject $classSubject)
    {
        $payload = $request->validated();

        DB::transaction(function () use ($classSubject, $payload) {
            $this->syncTeacherAssignments($classSubject, [[
                'teacher_id' => $payload['teacher_id'],
                'is_core' => $payload['is_core'] ?? false,
            ]], true);
        });

        return ClassSubjectResource::make($classSubject->fresh()->load($this->defaultIncludes()));
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/class-subjects/{classSubject}/teachers/{teacher}/core",
     *     tags={"Class Subjects"},
     *     summary="Switch the core teacher for a class subject",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="classSubject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="teacher", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Core teacher switched successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassSubjectResource")
     *     )
     * )
     */
    public function switchCoreTeacher(ClassSubject $classSubject, Staff $teacher)
    {
        DB::transaction(function () use ($classSubject, $teacher) {
            $assignment = $classSubject->teacherAssignments()
                ->where('teacher_id', $teacher->id)
                ->first();

            if ($assignment === null) {
                throw ValidationException::withMessages([
                    'teacher_id' => ['The selected teacher is not assigned to this class subject.'],
                ]);
            }

            $classSubject->teacherAssignments()->update(['is_core' => false]);
            $assignment->forceFill(['is_core' => true])->save();
            $classSubject->update(['teacher_id' => $teacher->id]);
        });

        return ClassSubjectResource::make($classSubject->fresh()->load($this->defaultIncludes()));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/class-subjects/{classSubject}/teachers/{teacher}",
     *     tags={"Class Subjects"},
     *     summary="Unassign a teacher from a class subject",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="classSubject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="teacher", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Teacher unassigned successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassSubjectResource")
     *     )
     * )
     */
    public function unassignTeacher(ClassSubject $classSubject, Staff $teacher)
    {
        DB::transaction(function () use ($classSubject, $teacher) {
            $assignment = $classSubject->teacherAssignments()
                ->where('teacher_id', $teacher->id)
                ->first();

            if ($assignment === null) {
                throw ValidationException::withMessages([
                    'teacher_id' => ['The selected teacher is not assigned to this class subject.'],
                ]);
            }

            $assignment->delete();

            $remaining = $classSubject->teacherAssignments()->orderBy('id')->get();
            $newCoreTeacherId = null;

            if ($remaining->isNotEmpty()) {
                $coreAssignment = $remaining->firstWhere('is_core', true) ?? $remaining->first();

                $classSubject->teacherAssignments()
                    ->where('class_subject_id', $classSubject->id)
                    ->update(['is_core' => false]);

                $coreAssignment->forceFill(['is_core' => true])->save();
                $newCoreTeacherId = $coreAssignment->teacher_id;
            }

            $classSubject->update(['teacher_id' => $newCoreTeacherId]);
        });

        return ClassSubjectResource::make($classSubject->fresh()->load($this->defaultIncludes()));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/class-subjects/{classSubject}",
     *     tags={"Class Subjects"},
     *     summary="Delete a class subject",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="classSubject", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Class subject deleted successfully")
     * )
     */
    public function destroy(ClassSubject $classSubject)
    {
        $classSubject->delete();

        return response()->noContent();
    }

    protected function defaultIncludes(): array
    {
        return [
            'classRoom',
            'subject',
            'teacher',
            'teacherAssignments.teacher',
        ];
    }

    /**
     * @param array<int, array{teacher_id:string,is_core?:bool}> $teacherAssignments
     */
    private function syncTeacherAssignments(ClassSubject $classSubject, array $teacherAssignments, bool $merge = false): void
    {
        if ($teacherAssignments === []) {
            if (! $merge) {
                $classSubject->teacherAssignments()->delete();
                $classSubject->update(['teacher_id' => null]);
            }

            return;
        }

        $incomingAssignments = collect($teacherAssignments)
            ->map(fn (array $assignment) => [
                'teacher_id' => $assignment['teacher_id'],
                'is_core' => (bool) ($assignment['is_core'] ?? false),
            ])
            ->unique('teacher_id')
            ->values();

        if ($incomingAssignments->isEmpty()) {
            if (! $merge) {
                $classSubject->teacherAssignments()->delete();
                $classSubject->update(['teacher_id' => null]);
            }

            return;
        }

        if ($incomingAssignments->where('is_core', true)->count() > 1) {
            throw ValidationException::withMessages([
                'teacher_assignments' => ['Only one core teacher can be assigned to a class subject.'],
            ]);
        }

        $normalizedAssignments = $incomingAssignments;

        if ($merge) {
            $normalizedAssignments = $classSubject->teacherAssignments()
                ->get()
                ->mapWithKeys(fn (ClassSubjectTeacher $assignment) => [$assignment->teacher_id => [
                    'teacher_id' => $assignment->teacher_id,
                    'is_core' => (bool) $assignment->is_core,
                ]]);

            foreach ($incomingAssignments as $assignment) {
                $normalizedAssignments->put($assignment['teacher_id'], $assignment);
            }

            $normalizedAssignments = $normalizedAssignments->values();
        }

        $designatedCoreTeacherId = $incomingAssignments->firstWhere('is_core', true)['teacher_id'] ?? null;

        if ($designatedCoreTeacherId === null) {
            $designatedCoreTeacherId = $merge ? $classSubject->teacher_id : null;
        }

        if ($designatedCoreTeacherId !== null && ! $normalizedAssignments->contains(fn (array $assignment) => $assignment['teacher_id'] === $designatedCoreTeacherId)) {
            $designatedCoreTeacherId = null;
        }

        if ($designatedCoreTeacherId === null) {
            $designatedCoreTeacherId = $normalizedAssignments->first()['teacher_id'];
        }

        $normalizedAssignments = $normalizedAssignments
            ->map(function (array $assignment) use ($designatedCoreTeacherId) {
                $assignment['is_core'] = $assignment['teacher_id'] === $designatedCoreTeacherId;

                return $assignment;
            })
            ->values();

        if (! $merge) {
            $classSubject->teacherAssignments()->delete();
        } else {
            $classSubject->teacherAssignments()->update(['is_core' => false]);
        }

        foreach ($normalizedAssignments as $assignment) {
            $classSubject->teacherAssignments()->updateOrCreate(
                ['teacher_id' => $assignment['teacher_id']],
                ['is_core' => $assignment['is_core']]
            );
        }

        $classSubject->update(['teacher_id' => $designatedCoreTeacherId]);
    }
}
