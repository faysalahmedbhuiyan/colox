<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FareCalculationService;
use App\Services\RoutingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
}