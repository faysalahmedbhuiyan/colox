<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RideController extends Controller
{
    /**
     * এই driver-এর vehicle_type-এর সাথে ম্যাচ করা pending ride request-গুলো
     */
    public function available(Request $request)
    {
        $driver = $request->user();
        $profile = $driver->driverProfile;

        if (! $profile || $profile->verification_status !== 'approved') {
            return response()->json(['message' => 'Your driver account is not yet verified.'], 403);
        }

        $rides = Ride::where('status', 'requested')
            ->where('vehicle_type', $profile->vehicle_type)
            ->latest()
            ->get();

        return response()->json(['rides' => $rides]);
    }

    public function accept(Request $request, Ride $ride)
    {
        $driver = $request->user();
        $profile = $driver->driverProfile;

        if (! $profile || $profile->verification_status !== 'approved') {
            return response()->json(['message' => 'Your driver account is not yet verified.'], 403);
        }

        $hasActiveRide = Ride::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arrived', 'in_progress'])
            ->exists();

        if ($hasActiveRide) {
            return response()->json(['message' => 'You already have an ongoing ride.'], 422);
        }

        // race condition protection: দুইজন driver একসাথে একই ride accept করার চেষ্টা করলে
        $accepted = DB::transaction(function () use ($ride, $driver) {
            $fresh = Ride::where('id', $ride->id)->lockForUpdate()->first();

            if ($fresh->status !== 'requested') {
                return null;
            }

            $fresh->update([
                'driver_id' => $driver->id,
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            return $fresh;
        });

        if (! $accepted) {
            return response()->json(['message' => 'This ride has already been accepted by another driver.'], 409);
        }

        return response()->json(['message' => 'Ride accepted.', 'ride' => $accepted->fresh()]);
    }

    /**
     * Driver pickup location-এ পৌঁছে গেছে
     */
    public function arrived(Request $request, Ride $ride)
    {
        $this->authorizeDriverRide($request, $ride);

        if ($ride->status !== 'accepted') {
            return response()->json(['message' => 'Ride is not in accepted state.'], 422);
        }

        $ride->update(['status' => 'arrived']);

        return response()->json(['message' => 'Marked as arrived.', 'ride' => $ride->fresh()]);
    }

    /**
     * Trip শুরু হলো (rider গাড়িতে উঠেছে)
     */
    public function start(Request $request, Ride $ride)
    {
        $this->authorizeDriverRide($request, $ride);

        if ($ride->status !== 'arrived') {
            return response()->json(['message' => 'Ride is not in arrived state.'], 422);
        }

        $ride->update(['status' => 'in_progress']);

        return response()->json(['message' => 'Trip started.', 'ride' => $ride->fresh()]);
    }

    /**
     * Trip সম্পন্ন — fare চূড়ান্ত হবে (এখন bidding নেই, তাই estimated_fare-ই final_fare)
     */
    public function complete(Request $request, Ride $ride)
    {
        $this->authorizeDriverRide($request, $ride);

        if ($ride->status !== 'in_progress') {
            return response()->json(['message' => 'Ride is not in progress.'], 422);
        }

        $ride->update([
            'status' => 'completed',
            'final_fare' => $ride->estimated_fare,
            'completed_at' => now(),
        ]);

        return response()->json(['message' => 'Trip completed.', 'ride' => $ride->fresh()]);
    }

    /**
     * Driver নিজের চলমান ride বাতিল করবে (pickup-এর আগে)
     */
    public function cancel(Request $request, Ride $ride)
    {
        $this->authorizeDriverRide($request, $ride);

        if (! in_array($ride->status, ['accepted', 'arrived'])) {
            return response()->json(['message' => 'This ride can no longer be cancelled.'], 422);
        }

        $ride->update(['status' => 'cancelled_by_driver']);

        return response()->json(['message' => 'Ride cancelled.', 'ride' => $ride->fresh()]);
    }

    private function authorizeDriverRide(Request $request, Ride $ride): void
    {
        abort_if($ride->driver_id !== $request->user()->id, 403, 'This is not your ride.');
    }
}