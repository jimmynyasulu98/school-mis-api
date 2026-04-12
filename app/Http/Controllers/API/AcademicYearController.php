<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/academic-years",
     *     tags={"Academic Years"},
     *     summary="List academic years",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Academic year collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AcademicYearResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return AcademicYear::with('terms')->orderByDesc('start_date')->paginate(15);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/academic-years",
     *     tags={"Academic Years"},
     *     summary="Create an academic year",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AcademicYearStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Academic year created",
     *         @OA\JsonContent(ref="#/components/schemas/AcademicYearResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:20', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if (($payload['is_current'] ?? false) === true) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        return AcademicYear::create($payload);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/academic-years/{academicYear}",
     *     tags={"Academic Years"},
     *     summary="Show an academic year",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="academicYear", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Academic year detail",
     *         @OA\JsonContent(ref="#/components/schemas/AcademicYearResource")
     *     )
     * )
     */
    public function show(AcademicYear $academicYear)
    {
        return $academicYear->load('terms');
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/academic-years/{academicYear}",
     *     tags={"Academic Years"},
     *     summary="Update an academic year",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="academicYear", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AcademicYearUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Academic year updated",
     *         @OA\JsonContent(ref="#/components/schemas/AcademicYearResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'string', 'max:20', 'unique:academic_years,name,'.$academicYear->id],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'is_current' => ['sometimes', 'boolean'],
        ]);

        if (($payload['is_current'] ?? false) === true) {
            AcademicYear::query()->whereKeyNot($academicYear->id)->update(['is_current' => false]);
        }

        $academicYear->update($payload);

        return $academicYear->fresh('terms');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/academic-years/{academicYear}",
     *     tags={"Academic Years"},
     *     summary="Delete an academic year",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="academicYear", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Academic year deleted",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     )
     * )
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return response()->json(['message' => 'Academic year deleted.']);
    }
}
