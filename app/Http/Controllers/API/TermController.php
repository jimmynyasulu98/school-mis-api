<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        return Term::with('academicYear')->orderByDesc('start_date')->paginate(15);
    }

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

    public function show(Term $term)
    {
        return $term->load('academicYear');
    }

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
