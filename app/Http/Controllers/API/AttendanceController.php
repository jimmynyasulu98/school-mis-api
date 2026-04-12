<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/attendance",
     *     tags={"Attendance"},
     *     summary="List attendance records",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="student_id", in="query", required=false, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="class_room_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Attendance collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AttendanceResource"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'classRoom'])->latest('attendance_date');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->string('student_id'));
        }

        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->integer('class_room_id'));
        }

        return response()->json($query->paginate(20));
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
     *         description="Attendance saved",
     *         @OA\JsonContent(ref="#/components/schemas/AttendanceResource")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'student_id' => ['required', 'uuid', 'exists:students,id'],
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:PRESENT,ABSENT,LATE,EXCUSED'],
        ]);

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $payload['student_id'],
                'class_room_id' => $payload['class_room_id'],
                'attendance_date' => $payload['attendance_date'],
            ],
            [
                'status' => $payload['status'],
                'recorded_by' => $request->user()?->staff_id,
            ]
        );

        return response()->json($attendance->load(['student', 'classRoom']), 201);
    }
}
