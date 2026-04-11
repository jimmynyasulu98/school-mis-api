<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with('staff', 'guardian')->where('username', $credentials['username'])->first();

        if (!$user || !$user->is_active || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        $token = $user->createToken('school-mis-api')->plainTextToken;
        $user->forceFill(['last_login_at' => now()])->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'context' => ['username' => $user->username],
        ]);

        return response()->json([
            'token' => $token,
            'user' => $user->load('roles', 'staff', 'guardian'),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles', 'staff', 'guardian'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
