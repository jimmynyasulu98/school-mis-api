<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        return StudentResource::collection(
            Student::with(['currentClassRoom', 'guardians'])->latest()->paginate(15)
        );
    }

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
                $student->guardians()->sync(collect($guardians)->mapWithKeys(fn (array $guardian) => [
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

    public function show(Student $student)
    {
        return StudentResource::make($student->load(['currentClassRoom', 'guardians', 'feeAccounts.payments', 'grades']));
    }

    public function update(Request $request, Student $student)
    {
        $payload = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'string', 'max:30'],
            'current_class_room_id' => ['sometimes', 'nullable', 'integer', 'exists:class_rooms,id'],
        ]);

        $student->update($payload);

        return StudentResource::make($student->fresh(['currentClassRoom', 'guardians']));
    }

    public function destroy(Student $student)
    {
        $student->update(['status' => 'INACTIVE']);

        return response()->json(['message' => 'Student deactivated.']);
    }
}
