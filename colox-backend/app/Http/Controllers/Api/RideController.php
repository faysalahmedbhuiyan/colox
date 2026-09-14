<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FareCalculationService;
use App\Services\RoutingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Ride;
use Illuminate\Support\Facades\DB;

class RideController extends Controller
{
    public function __construct(
        private RoutingService $routingService,
        private FareCalculationService $fareService,
    ) {}

    public function estimateFare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup_lng' => ['required', 'numeric', 'between:-180,180'],
            'dropoff_lat' => ['required', 'numeric', 'between:-90,90'],
            'dropoff_lng' => ['required', 'numeric', 'between:-180,180'],
            'vehicle_type' => ['required', 'in:car,motorcycle'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $route = $this->routingService->getRoute(
                $request->pickup_lat,
                $request->pickup_lng,
                $request->dropoff_lat,
                $request->dropoff_lng,
            );

            $fare = $this->fareService->calculate($request->vehicle_type, $route['distance_km']);

            return response()->json([
                'distance_km' => $route['distance_km'],
                'duration_minutes' => $route['duration_minutes'],
                'estimated_fare' => $fare['estimated_fare'],
                'service_fee' => $fare['service_fee'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
        /**
     * Rider নতুন ride request করবে (fare estimate করেই, একই logic reuse)
     */
    public function requestRide(Request $request)
    {
        $rider = $request->user();

        // rider-এর ইতিমধ্যে কোনো active ride থাকলে নতুন request করতে পারবে না
        $hasActiveRide = Ride::where('rider_id', $rider->id)
            ->whereIn('status', ['requested', 'accepted', 'arrived', 'in_progress'])
            ->exists();

        if ($hasActiveRide) {
            return response()->json([
                'message' => 'You already have an active ride request or ongoing trip.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'pickup_lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup_lng' => ['required', 'numeric', 'between:-180,180'],
            'pickup_address' => ['nullable', 'string'],
            'dropoff_lat' => ['required', 'numeric', 'between:-90,90'],
            'dropoff_lng' => ['required', 'numeric', 'between:-180,180'],
            'dropoff_address' => ['nullable', 'string'],
            'vehicle_type' => ['required', 'in:car,motorcycle'],
            'payment_method' => ['required', 'in:cash,wallet'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $route = $this->routingService->getRoute(
                $request->pickup_lat,
                $request->pickup_lng,
                $request->dropoff_lat,
                $request->dropoff_lng,
            );

            $fare = $this->fareService->calculate($request->vehicle_type, $route['distance_km']);

            $ride = Ride::create([
                'rider_id' => $rider->id,
                'vehicle_type' => $request->vehicle_type,
                'pickup_lat' => $request->pickup_lat,
                'pickup_lng' => $request->pickup_lng,
                'pickup_address' => $request->pickup_address,
                'dropoff_lat' => $request->dropoff_lat,
                'dropoff_lng' => $request->dropoff_lng,
                'dropoff_address' => $request->dropoff_address,
                'estimated_fare' => $fare['estimated_fare'],
                'service_fee' => $fare['service_fee'],
                'status' => 'requested',
                'payment_method' => $request->payment_method,
                'requested_at' => now(),
            ]);

            return response()->json([
                'message' => 'Ride requested. Waiting for a driver to accept.',
                'ride' => $ride,
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Rider নিজের চলমান/সাম্প্রতিক ride-এর status দেখবে (polling-এর জন্য, real-time push পরে আসবে)
     */
    public function myCurrentRide(Request $request)
    {
        $ride = Ride::where('rider_id', $request->user()->id)
            ->whereIn('status', ['requested', 'accepted', 'arrived', 'in_progress'])
            ->latest()
            ->first();

        if (! $ride) {
            return response()->json(['message' => 'No active ride.'], 404);
        }

        return response()->json(['ride' => $ride->load('driver.driverProfile')]);
    }

    /**
     * Rider নিজের ride বাতিল করবে (শুধু requested/accepted অবস্থায়, driver already picked up হয়ে গেলে বাতিল করা যাবে না)
     */
    public function cancelByRider(Request $request, Ride $ride)
    {
        if ($ride->rider_id !== $request->user()->id) {
            return response()->json(['message' => 'This is not your ride.'], 403);
        }

        if (! in_array($ride->status, ['requested', 'accepted'])) {
            return response()->json(['message' => 'This ride can no longer be cancelled.'], 422);
        }

        $ride->update(['status' => 'cancelled_by_rider']);

        return response()->json(['message' => 'Ride cancelled.', 'ride' => $ride->fresh()]);
    }
}