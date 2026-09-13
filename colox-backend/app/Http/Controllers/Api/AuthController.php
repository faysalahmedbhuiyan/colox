<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * রাইডার হিসেবে registration।
     * এখনো one-NID-one-account duplicate check নেই — সেটা Step 2d-এ যোগ হবে।
     */
    public function registerRider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        UserRole::create([
            'user_id' => $user->id,
            'role' => 'rider',
        ]);

        $token = $user->createToken('rider-app')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if ($user->account_status === 'held') {
            return response()->json([
                'message' => 'Your account is currently under review. Please contact support.',
                'account_status' => 'held',
            ], 403);
        }

        if ($user->account_status === 'banned') {
            return response()->json([
                'message' => 'Your account has been banned.',
                'account_status' => 'banned',
            ], 403);
        }

        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('colox-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'roles' => $user->roles()->pluck('role'),
            'token' => $token,
        ]);
    }
    
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'roles' => $user->roles()->pluck('role'),
        ]);
    }
}