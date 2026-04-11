<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::paginate(30);
    }

    public function store(Request $request)
    {
        return Permission::create([
            'name' => $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
                'description' => ['nullable', 'string'],
            ])['name'],
            'guard_name' => 'api',
            'description' => $request->input('description'),
        ]);
    }
}
