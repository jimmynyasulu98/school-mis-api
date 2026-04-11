<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::latest()->paginate(15);
    }

    public function store(Request $request)
    {
        return Subject::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'is_core' => ['nullable', 'boolean'],
        ]));
    }

    public function show(Subject $subject)
    {
        return $subject;
    }

    public function update(Request $request, Subject $subject)
    {
        $subject->update($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:20', 'unique:subjects,code,'.$subject->id],
            'is_core' => ['sometimes', 'boolean'],
        ]));

        return $subject->fresh();
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Subject deleted.']);
    }
}
