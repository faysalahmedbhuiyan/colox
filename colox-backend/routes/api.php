<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'colox-backend',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::post('/auth/register/rider', [AuthController::class, 'registerRider']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
});

Route::middleware(['auth:sanctum', 'role:rider'])->group(function () {
    Route::get('/rider/ping', function () {
        return response()->json(['message' => 'Rider access confirmed']);
    });
});

Route::middleware(['auth:sanctum', 'role:driver'])->group(function () {
    Route::get('/driver/ping', function () {
        return response()->json(['message' => 'Driver access confirmed']);
    });
});