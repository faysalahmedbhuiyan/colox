<?php

namespace App\Services;

use App\Models\FareSetting;
use App\Models\ServiceFeeSlab;
use RuntimeException;

class FareCalculationService
{
    public function calculate(string $vehicleType, float $distanceKm): array
    {
        $setting = FareSetting::where('vehicle_type', $vehicleType)->first();

        if (! $setting) {
            throw new RuntimeException("Fare settings not configured for vehicle type: {$vehicleType}");
        }

        $estimatedFare = round($setting->base_fare + ($setting->per_km_rate * $distanceKm), 2);
        $serviceFee = $this->resolveServiceFee($estimatedFare);

        return [
            'estimated_fare' => $estimatedFare,
            'service_fee' => $serviceFee,
            'total_payable' => $estimatedFare, // service fee platform commission, rider-কে আলাদা করে দিতে হবে না — এটা driver-এর আয় থেকে কাটা হবে (business logic পরে নিশ্চিত করা হবে trip completion-এর সময়)
        ];
    }

    private function resolveServiceFee(float $fare): float
    {
        $slab = ServiceFeeSlab::where('min_fare', '<=', $fare)
            ->where(function ($query) use ($fare) {
                $query->whereNull('max_fare')->orWhere('max_fare', '>=', $fare);
            })
            ->first();

        return $slab ? (float) $slab->fee : 0;
    }
}