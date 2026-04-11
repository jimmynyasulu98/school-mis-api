<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        return Assessment::with('grades')->latest('assessment_date')->paginate(15);
    }

    public function store(Request $request)
    {
        return Assessment::create($request->validate([
            'class_subject_id' => ['required', 'integer', 'exists:class_subjects,id'],
            'term_id' => ['required', 'integer', 'exists:terms,id'],
            'assessment_type_id' => ['required', 'integer', 'exists:assessment_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'max_score' => ['required', 'numeric', 'min:0'],
            'assessment_date' => ['required', 'date'],
        ]));
    }

    public function show(Assessment $assessment)
    {
        return $assessment->load('grades');
    }

    public function update(Request $request, Assessment $assessment)
    {
        $assessment->update($request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'max_score' => ['sometimes', 'numeric', 'min:0'],
            'assessment_date' => ['sometimes', 'date'],
        ]));

        return $assessment->fresh('grades');
    }
}
