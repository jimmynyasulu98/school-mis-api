<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\StaffResource;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/staff",
     *     tags={"Staff"},
     *     summary="List all staff members with pagination and optional included relationships",
     *     description="Retrieve a paginated list of staff members. Use 'includes' parameter to eager load related resources (user, roles).",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of records per page (default 10, max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, maximum=100, minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         description="Comma-separated list of relationships to include. Available: user, roles",
     *         example="user,roles",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff collection retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedStaffResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Insufficient permissions",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        return StaffResource::collection(
            $this->applyPaginationAndIncludes(
                Staff::query(),
                $request,
                10
            )
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/staff",
     *     tags={"Staff"},
     *     summary="Create a staff member",
     *     description="Create a new staff record with optional user account and role assignments",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StaffStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Staff member created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/StaffResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(StoreStaffRequest $request)
    {
        $payload = $request->validated();

        $staff = DB::transaction(function () use ($payload) {
            $userData = [
                'username' => $payload['username'] ?? null,
                'password' => $payload['password'] ?? null,
                'roles' => $payload['roles'] ?? [],
            ];

            unset($payload['username'], $payload['password'], $payload['roles']);

            $staff = Staff::create($payload);

            if ($userData['username']) {
                $user = User::create([
                    'username' => $userData['username'],
                    'password' => Hash::make($userData['password']),
                    'staff_id' => $staff->id,
                    'is_active' => true,
                ]);

                if ($userData['roles'] !== []) {
                    $user->syncRoles($userData['roles']);
                }
            }

            return $staff;
        });

        return StaffResource::make($staff->load('user.roles'))->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/staff/{staff}",
     *     tags={"Staff"},
     *     summary="Show a staff member",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="staff", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Staff profile",
     *         @OA\JsonContent(ref="#/components/schemas/StaffResource")
     *     )
     * )
     */
    public function show(Staff $staff)
    {
        return StaffResource::make($staff->load('user.roles'));
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/staff/{staff}",
     *     tags={"Staff"},
     *     summary="Update a staff member",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="staff", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StaffUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff member updated",
     *         @OA\JsonContent(ref="#/components/schemas/StaffResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $payload = $request->validated();

        $roles = $payload['roles'] ?? null;
        unset($payload['roles']);

        $staff->update($payload);

        if ($roles !== null && $staff->user) {
            $staff->user->syncRoles($roles);
        }

        return StaffResource::make($staff->fresh('user.roles'));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/staff/{staff}",
     *     tags={"Staff"},
     *     summary="Deactivate a staff member",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="staff", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Staff member deactivated",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     )
     * )
     */
    public function destroy(Staff $staff)
    {
        $staff->update(['status' => 'INACTIVE']);
        $staff->user?->update(['is_active' => false]);

        return response()->json(['message' => 'Staff member deactivated.']);
    }
}
