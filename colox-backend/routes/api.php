<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'colox-backend',
        'timestamp' => now()->toIso8601String(),
    ]);
});
use App\Http\Controllers\Api\AuthController;

Route::post('/auth/register/rider', [AuthController::class, 'registerRider']);