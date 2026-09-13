<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverVerificationController extends Controller
{
    /**
     * Pending verification-এ থাকা সব driver profile লিস্ট
     */
    public function pending()
    {
        $profiles = DriverProfile::with('user')
            ->where('verification_status', 'pending')
            ->get();

        return response()->json(['drivers' => $profiles]);
    }

    public function approve(DriverProfile $driverProfile)
    {
        $driverProfile->update([
            'verification_status' => 'approved',
            'rejection_reason' => null,
        ]);

        return response()->json([
            'message' => 'Driver approved.',
            'driver_profile' => $driverProfile->fresh(),
        ]);
    }

    public function reject(Request $request, DriverProfile $driverProfile)
    {
        $validator = Validator::make($request->all(), [
            'reason' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $driverProfile->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Driver rejected.',
            'driver_profile' => $driverProfile->fresh(),
        ]);
    }
}