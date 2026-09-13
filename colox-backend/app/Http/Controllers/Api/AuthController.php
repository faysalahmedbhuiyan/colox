<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * একদম নতুন কেউ সরাসরি driver হিসেবে সাইনআপ করছে।
     * dual-role rule অনুযায়ী rider role-ও automatically পাবে।
     */
    public function registerDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8'],
            'nid_number' => ['required', 'string', 'unique:users,nid_number'],
            'vehicle_type' => ['required', 'in:car,motorcycle'],
            'vehicle_number' => ['required', 'string'],
            'license_number' => ['required', 'string'],
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
            'nid_number' => $request->nid_number,
        ]);

        // dual-role: driver হিসেবে সাইনআপ করলেও rider mode স্বয়ংক্রিয়ভাবে থাকবে
        UserRole::create(['user_id' => $user->id, 'role' => 'rider']);
        UserRole::create(['user_id' => $user->id, 'role' => 'driver']);

        DriverProfile::create([
            'user_id' => $user->id,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_number' => $request->vehicle_number,
            'license_number' => $request->license_number,
            'verification_status' => 'pending',
        ]);

        $token = $user->createToken('driver-app')->plainTextToken;

        return response()->json([
            'message' => 'Driver registration submitted. Awaiting admin verification.',
            'user' => $user->fresh(),
            'roles' => $user->roles()->pluck('role'),
            'token' => $token,
        ], 201);
    }

    /**
     * আগে থেকে rider থাকা কেউ driver হতে চাইছে (upgrade)।
     * login করা থাকতে হবে (auth:sanctum middleware দিয়ে protected)।
     */
    public function upgradeToDriver(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('driver')) {
            return response()->json([
                'message' => 'You already have a driver account.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'nid_number' => ['required', 'string', 'unique:users,nid_number,' . $user->id],
            'vehicle_type' => ['required', 'in:car,motorcycle'],
            'vehicle_number' => ['required', 'string'],
            'license_number' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // যদি আগে থেকে NID না থাকে, এখন যোগ হবে; থাকলে অপরিবর্তিত থাকবে
        if (! $user->nid_number) {
            $user->update(['nid_number' => $request->nid_number]);
        }

        UserRole::create(['user_id' => $user->id, 'role' => 'driver']);

        DriverProfile::create([
            'user_id' => $user->id,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_number' => $request->vehicle_number,
            'license_number' => $request->license_number,
            'verification_status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Driver upgrade submitted. Awaiting admin verification.',
            'user' => $user->fresh(),
            'roles' => $user->roles()->pluck('role'),
        ], 201);
    }

    /**
     * রাইডার হিসেবে registration।
     */
    public function registerRider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8'],
            'nid_number' => ['nullable', 'string', 'unique:users,nid_number'],
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
            'nid_number' => $request->nid_number,
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