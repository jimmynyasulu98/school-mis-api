<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        return ClassRoom::with('classTeacher')->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        return ClassRoom::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stream' => ['nullable', 'string', 'max:20'],
            'section' => ['nullable', 'string', 'max:30'],
            'class_teacher_id' => ['nullable', 'uuid', 'exists:staff,id'],
        ]));
    }

    public function show(ClassRoom $class)
    {
        return $class->load('classTeacher');
    }

    public function update(Request $request, ClassRoom $class)
    {
        $class->update($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'stream' => ['sometimes', 'nullable', 'string', 'max:20'],
            'section' => ['sometimes', 'nullable', 'string', 'max:30'],
            'class_teacher_id' => ['sometimes', 'nullable', 'uuid', 'exists:staff,id'],
        ]));

        return $class->fresh('classTeacher');
    }

    public function destroy(ClassRoom $class)
    {
        $class->delete();
        return response()->json(['message' => 'Class deleted.']);
    }
}
