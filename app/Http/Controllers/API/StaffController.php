<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        return StaffResource::collection(Staff::with('user.roles')->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'employee_number' => ['required', 'string', 'max:50', 'unique:staff,employee_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', 'unique:staff,email'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:20'],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:8'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

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

    public function show(Staff $staff)
    {
        return StaffResource::make($staff->load('user.roles'));
    }

    public function update(Request $request, Staff $staff)
    {
        $payload = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', 'unique:staff,email,'.$staff->id.',id'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:20'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $roles = $payload['roles'] ?? null;
        unset($payload['roles']);

        $staff->update($payload);

        if ($roles !== null && $staff->user) {
            $staff->user->syncRoles($roles);
        }

        return StaffResource::make($staff->fresh('user.roles'));
    }

    public function destroy(Staff $staff)
    {
        $staff->update(['status' => 'INACTIVE']);
        $staff->user?->update(['is_active' => false]);

        return response()->json(['message' => 'Staff member deactivated.']);
    }
}
