<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return Role::with('permissions')->paginate(15);
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $payload['name'],
            'guard_name' => 'api',
            'description' => $payload['description'] ?? null,
        ]);

        $role->syncPermissions($payload['permission_ids'] ?? []);

        return $role->load('permissions');
    }

    public function show(Role $role)
    {
        return $role->load('permissions');
    }
}
