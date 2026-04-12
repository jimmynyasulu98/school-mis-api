<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/terms",
     *     tags={"Terms"},
     *     summary="List terms",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Term collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TermResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Term::with('academicYear')->orderByDesc('start_date')->paginate(15);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/terms",
     *     tags={"Terms"},
     *     summary="Create a term",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TermStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Term created",
     *         @OA\JsonContent(ref="#/components/schemas/TermResource")
     *     )
     * )
     */
    public function store(Request $request)
    {
        return Term::create($request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:30'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/terms/{term}",
     *     tags={"Terms"},
     *     summary="Show a term",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="term", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Term detail",
     *         @OA\JsonContent(ref="#/components/schemas/TermResource")
     *     )
     * )
     */
    public function show(Term $term)
    {
        return $term->load('academicYear');
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/terms/{term}",
     *     tags={"Terms"},
     *     summary="Update a term",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="term", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TermUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Term updated",
     *         @OA\JsonContent(ref="#/components/schemas/TermResource")
     *     )
     * )
     */
    public function update(Request $request, Term $term)
    {
        $term->update($request->validate([
            'name' => ['sometimes', 'string', 'max:30'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'is_current' => ['sometimes', 'boolean'],
        ]));

        return $term->fresh('academicYear');
    }
}
