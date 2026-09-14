<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PoliceStation;
use App\Models\Ride;
use App\Models\SosIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $nearestStation = $this->findNearestPoliceStation($request->lat, $request->lng);

        // TODO (পরের sub-step): এখানে admin panel-এ real-time WebSocket alert পাঠানো হবে (Reverb দিয়ে)

        return response()->json([
            'message' => 'SOS triggered. Help is being alerted.',
            'incident_code' => $incident->incident_code,
            'nearest_police_station' => $nearestStation,
        ], 201);
    }

    /**
     * Haversine formula দিয়ে সবচেয়ে কাছের active থানা খুঁজে বের করে।
     * থানার সংখ্যা কম (কয়েকশোর মধ্যে) থাকবে বলে raw SQL distance calculation যথেষ্ট দ্রুত —
     * PostGIS spatial index এখনই দরকার নেই।
     */
    private function findNearestPoliceStation(float $lat, float $lng): ?array
    {
        $station = DB::table('police_stations')
            ->select('id', 'name', 'phone', 'area', 'lat', 'lng', DB::raw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(lat)) *
                    cos(radians(lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(lat))
                )) AS distance_km
            "))
            ->addBinding([$lat, $lng, $lat], 'select')
            ->where('is_active', true)
            ->orderBy('distance_km')
            ->first();

        if (! $station) {
            return null;
        }

        return [
            'name' => $station->name,
            'phone' => $station->phone,
            'area' => $station->area,
            'distance_km' => round($station->distance_km, 2),
        ];
    }
}