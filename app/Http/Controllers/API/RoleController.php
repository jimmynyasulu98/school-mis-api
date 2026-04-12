<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/roles",
     *     tags={"Roles"},
     *     summary="List roles",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Role collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/RoleResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Role::with('permissions')->paginate(15);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/roles",
     *     tags={"Roles"},
     *     summary="Create a role",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoleStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role created",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/roles/{role}",
     *     tags={"Roles"},
     *     summary="Show a role",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Role detail",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     )
     * )
     */
    public function show(Role $role)
    {
        return $role->load('permissions');
    }
}
