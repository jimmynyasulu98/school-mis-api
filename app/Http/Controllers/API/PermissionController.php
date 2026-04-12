<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/permissions",
     *     tags={"Permissions"},
     *     summary="List permissions",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Permission collection",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PermissionResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Permission::paginate(30);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/permissions",
     *     tags={"Permissions"},
     *     summary="Create a permission",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PermissionStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission created",
     *         @OA\JsonContent(ref="#/components/schemas/PermissionResource")
     *     )
     * )
     */
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
