<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\DriverVerificationController;
use App\Http\Controllers\Api\RideController;
use App\Http\Controllers\Api\Driver\RideController as DriverRideController;

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
    Route::post('/driver/documents', [AuthController::class, 'uploadDriverDocuments']);
    Route::post('/rides/estimate-fare', [RideController::class, 'estimateFare']);
    
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/drivers/pending', [DriverVerificationController::class, 'pending']);
    Route::post('/drivers/{driverProfile}/approve', [DriverVerificationController::class, 'approve']);
    Route::post('/drivers/{driverProfile}/reject', [DriverVerificationController::class, 'reject']);
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
Route::post('/auth/register/driver', [AuthController::class, 'registerDriver']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/driver/upgrade', [AuthController::class, 'upgradeToDriver']);
});
Route::middleware(['auth:sanctum', 'role:rider'])->group(function () {
    Route::post('/rides/estimate-fare', [RideController::class, 'estimateFare']);
    Route::post('/rides/request', [RideController::class, 'requestRide']);
    Route::get('/rides/current', [RideController::class, 'myCurrentRide']);
    Route::post('/rides/{ride}/cancel', [RideController::class, 'cancelByRider']);
});

Route::middleware(['auth:sanctum', 'role:driver'])->prefix('driver')->group(function () {
    Route::get('/rides/available', [DriverRideController::class, 'available']);
    Route::post('/rides/{ride}/accept', [DriverRideController::class, 'accept']);
    Route::post('/rides/{ride}/arrived', [DriverRideController::class, 'arrived']);
    Route::post('/rides/{ride}/start', [DriverRideController::class, 'start']);
    Route::post('/rides/{ride}/complete', [DriverRideController::class, 'complete']);
    Route::post('/rides/{ride}/cancel', [DriverRideController::class, 'cancel']);
});