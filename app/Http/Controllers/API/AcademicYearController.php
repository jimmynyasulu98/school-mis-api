<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        return AcademicYear::with('terms')->orderByDesc('start_date')->paginate(15);
    }

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

    public function show(AcademicYear $academicYear)
    {
        return $academicYear->load('terms');
    }

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

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return response()->json(['message' => 'Academic year deleted.']);
    }
}
