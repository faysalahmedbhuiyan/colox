<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RideController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\Admin\AccountHoldController;
use App\Http\Controllers\Api\Admin\DriverVerificationController;
use App\Http\Controllers\Api\Driver\RideController as DriverRideController;
use App\Http\Controllers\Api\SosController;
use App\Http\Controllers\Api\Admin\SosController as AdminSosController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'colox-backend',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Authentication Routes
Route::post('/auth/register/rider', [AuthController::class, 'registerRider']);
Route::post('/auth/register/driver', [AuthController::class, 'registerDriver']);
Route::post('/auth/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| Authenticated Common Routes (Rider & Driver both)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // Driver Upgrade & Documents
    Route::post('/driver/upgrade', [AuthController::class, 'upgradeToDriver']);
    Route::post('/driver/documents', [AuthController::class, 'uploadDriverDocuments']);

    // Complaints
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/complaints/mine', [ComplaintController::class, 'myComplaints']);

    //sos
    Route::post('/sos/trigger', [SosController::class, 'trigger']);
});


/*
|--------------------------------------------------------------------------
| Rider Specific Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:rider'])->group(function () {
    Route::get('/rider/ping', function () {
        return response()->json(['message' => 'Rider access confirmed']);
    });

    Route::post('/rides/estimate-fare', [RideController::class, 'estimateFare']);
    Route::post('/rides/request', [RideController::class, 'requestRide']);
    Route::get('/rides/current', [RideController::class, 'myCurrentRide']);
    Route::post('/rides/{ride}/cancel', [RideController::class, 'cancelByRider']);
});


/*
|--------------------------------------------------------------------------
| Driver Specific Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:driver'])->prefix('driver')->group(function () {
    Route::get('/ping', function () {
        return response()->json(['message' => 'Driver access confirmed']);
    });

    Route::get('/rides/available', [DriverRideController::class, 'available']);
    Route::post('/rides/{ride}/accept', [DriverRideController::class, 'accept']);
    Route::post('/rides/{ride}/arrived', [DriverRideController::class, 'arrived']);
    Route::post('/rides/{ride}/start', [DriverRideController::class, 'start']);
    Route::post('/rides/{ride}/complete', [DriverRideController::class, 'complete']);
    Route::post('/rides/{ride}/cancel', [DriverRideController::class, 'cancel']);
});


/*
|--------------------------------------------------------------------------
| Admin Specific Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/drivers/pending', [DriverVerificationController::class, 'pending']);
    Route::post('/drivers/{driverProfile}/approve', [DriverVerificationController::class, 'approve']);
    Route::post('/drivers/{driverProfile}/reject', [DriverVerificationController::class, 'reject']);
    Route::get('/account-holds/pending', [AccountHoldController::class, 'pending']);
    Route::get('/account-holds/{accountHold}', [AccountHoldController::class, 'show']);
    Route::post('/account-holds/{accountHold}/reinstate', [AccountHoldController::class, 'reinstate']);
    Route::post('/account-holds/{accountHold}/ban', [AccountHoldController::class, 'ban']);
    Route::get('/sos/active', [AdminSosController::class, 'active']);
    Route::get('/sos/{sosIncident}', [AdminSosController::class, 'show']);
    Route::post('/sos/{sosIncident}/acknowledge', [AdminSosController::class, 'acknowledge']);

});