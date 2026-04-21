<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\AssignClassSubjectTeacherRequest;
use App\Http\Requests\EndClassSubjectTeacherAssignmentRequest;
use App\Http\Requests\StoreClassSubjectRequest;
use App\Http\Resources\ClassSubjectResource;
use App\Models\ClassSubject;
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
                'starts_on' => $payload['starts_on'] ?? now()->toDateString(),
                'ends_on' => $payload['ends_on'] ?? null,
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
                ->current()
                ->where('teacher_id', $teacher->id)
                ->first();

            if ($assignment === null) {
                throw ValidationException::withMessages([
                    'teacher_id' => ['The selected teacher is not assigned to this class subject.'],
                ]);
            }

            $classSubject->teacherAssignments()->current()->update(['is_core' => false]);
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
    public function unassignTeacher(EndClassSubjectTeacherAssignmentRequest $request, ClassSubject $classSubject, Staff $teacher)
    {
        $payload = $request->validated();

        DB::transaction(function () use ($classSubject, $teacher, $payload) {
            $effectiveEndDate = $payload['ends_on'] ?? now()->toDateString();
            $assignment = $classSubject->teacherAssignments()
                ->current($effectiveEndDate)
                ->where('teacher_id', $teacher->id)
                ->first();

            if ($assignment === null) {
                throw ValidationException::withMessages([
                    'teacher_id' => ['The selected teacher does not have a current assignment for this class subject on the provided end date.'],
                ]);
            }

            if ($assignment->starts_on !== null && $assignment->starts_on->toDateString() > $effectiveEndDate) {
                throw ValidationException::withMessages([
                    'ends_on' => ['The end date cannot be before the assignment start date.'],
                ]);
            }

            $assignment->forceFill(['ends_on' => $effectiveEndDate])->save();

            $this->syncCurrentCoreTeacher($classSubject);
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
     * @param array<int, array{teacher_id:string,is_core?:bool,starts_on?:string,ends_on?:?string}> $teacherAssignments
     */
    private function syncTeacherAssignments(ClassSubject $classSubject, array $teacherAssignments, bool $merge = false): void
    {
        if ($teacherAssignments === []) {
            if (! $merge) {
                $classSubject->teacherAssignments()->current()->update(['ends_on' => now()->toDateString()]);
                $classSubject->update(['teacher_id' => null]);
            }

            return;
        }

        $incomingAssignments = collect($teacherAssignments)
            ->map(fn (array $assignment) => [
                'teacher_id' => $assignment['teacher_id'],
                'is_core' => (bool) ($assignment['is_core'] ?? false),
                'starts_on' => $assignment['starts_on'] ?? now()->toDateString(),
                'ends_on' => $assignment['ends_on'] ?? null,
            ])
            ->unique('teacher_id')
            ->values();

        if ($incomingAssignments->isEmpty()) {
            if (! $merge) {
                $classSubject->teacherAssignments()->current()->update(['ends_on' => now()->toDateString()]);
                $classSubject->update(['teacher_id' => null]);
            }

            return;
        }

        if ($incomingAssignments->where('is_core', true)->count() > 1) {
            throw ValidationException::withMessages([
                'teacher_assignments' => ['Only one core teacher can be assigned to a class subject.'],
            ]);
        }

        $designatedCoreTeacherId = $incomingAssignments->firstWhere('is_core', true)['teacher_id'] ?? null;

        foreach ($incomingAssignments as $assignment) {
            $this->ensureTeacherAssignmentDoesNotOverlap($classSubject, $assignment);
        }

        if (! $merge) {
            $replacementStartDate = $incomingAssignments->min('starts_on');
            $classSubject->teacherAssignments()
                ->current($replacementStartDate)
                ->update(['ends_on' => $replacementStartDate]);
        }

        foreach ($incomingAssignments as $assignment) {
            $classSubject->teacherAssignments()->create([
                'teacher_id' => $assignment['teacher_id'],
                'is_core' => false,
                'starts_on' => $assignment['starts_on'],
                'ends_on' => $assignment['ends_on'],
            ]);
        }

        $this->syncCurrentCoreTeacher($classSubject, now()->toDateString(), $designatedCoreTeacherId);
    }

    /**
     * @param array{teacher_id:string,is_core:bool,starts_on:string,ends_on:?string} $assignment
     */
    private function ensureTeacherAssignmentDoesNotOverlap(ClassSubject $classSubject, array $assignment): void
    {
        $startsOn = $assignment['starts_on'];
        $endsOn = $assignment['ends_on'];

        if ($endsOn !== null && $endsOn < $startsOn) {
            throw ValidationException::withMessages([
                'teacher_assignments' => ['The assignment end date cannot be earlier than the start date.'],
            ]);
        }

        $overlapExists = $classSubject->teacherAssignments()
            ->where('teacher_id', $assignment['teacher_id'])
            ->where(function ($query) use ($startsOn, $endsOn) {
                $query->whereNull('ends_on')
                    ->orWhereDate('ends_on', '>', $startsOn);
            })
            ->when($endsOn !== null, function ($query) use ($endsOn) {
                $query->whereDate('starts_on', '<', $endsOn);
            })
            ->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'teacher_assignments' => ['A teacher cannot have overlapping assignment periods for the same class subject.'],
            ]);
        }
    }

    private function syncCurrentCoreTeacher(ClassSubject $classSubject, ?string $effectiveDate = null, ?string $preferredTeacherId = null): void
    {
        $date = $effectiveDate ?? now()->toDateString();
        $currentAssignments = $classSubject->teacherAssignments()
            ->current($date)
            ->orderBy('starts_on')
            ->orderBy('id')
            ->get();

        if ($currentAssignments->isEmpty()) {
            $classSubject->update(['teacher_id' => null]);

            return;
        }

        $coreAssignment = null;

        if ($preferredTeacherId !== null) {
            $coreAssignment = $currentAssignments->firstWhere('teacher_id', $preferredTeacherId);
        }

        if ($coreAssignment === null && $classSubject->teacher_id !== null) {
            $coreAssignment = $currentAssignments->firstWhere('teacher_id', $classSubject->teacher_id);
        }

        if ($coreAssignment === null) {
            $coreAssignment = $currentAssignments->firstWhere('is_core', true) ?? $currentAssignments->first();
        }

        $classSubject->teacherAssignments()->current($date)->update(['is_core' => false]);
        $coreAssignment->forceFill(['is_core' => true])->save();
        $classSubject->update(['teacher_id' => $coreAssignment->teacher_id]);
    }
}
