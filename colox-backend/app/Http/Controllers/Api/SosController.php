<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\SosIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SosController extends Controller
{
    /**
     * SOS ট্রিগার — শুধু active ride (accepted/arrived/in_progress) অবস্থায় করা যাবে,
     * কারণ SOS-এর মূল উদ্দেশ্যই হলো rider-driver উভয়ের তথ্য snapshot নেওয়া।
     */
    public function trigger(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ride_id' => ['required', 'exists:rides,id'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $ride = Ride::with(['rider', 'driver.driverProfile'])->findOrFail($request->ride_id);

        $isPartOfRide = $ride->rider_id === $user->id || $ride->driver_id === $user->id;

        if (! $isPartOfRide) {
            return response()->json(['message' => 'You are not part of this ride.'], 403);
        }

        if (! in_array($ride->status, ['accepted', 'arrived', 'in_progress'])) {
            return response()->json(['message' => 'SOS can only be triggered during an active ride.'], 422);
        }

        $riderSnapshot = [
            'name' => $ride->rider->name,
            'phone' => $ride->rider->phone,
            'nid_number' => $ride->rider->nid_number,
        ];

        $driverSnapshot = null;
        if ($ride->driver) {
            $driverSnapshot = [
                'name' => $ride->driver->name,
                'phone' => $ride->driver->phone,
                'license_number' => $ride->driver->driverProfile?->license_number,
                'vehicle_number' => $ride->driver->driverProfile?->vehicle_number,
                'nid_number' => $ride->driver->nid_number,
            ];
        }

        $incident = SosIncident::create([
            'ride_id' => $ride->id,
            'triggered_by' => $user->id,
            'trigger_lat' => $request->lat,
            'trigger_lng' => $request->lng,
            'rider_snapshot' => $riderSnapshot,
            'driver_snapshot' => $driverSnapshot,
            'status' => 'active',
        ]);

        // TODO (পরের sub-step): এখানে admin panel-এ real-time WebSocket alert পাঠানো হবে (Reverb দিয়ে)
        // TODO (পরের sub-step): এখানে নিকটতম থানার তথ্য response-এ যোগ হবে

        return response()->json([
            'message' => 'SOS triggered. Help is being alerted.',
            'incident_code' => $incident->incident_code,
        ], 201);
    }
}