<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ride extends Model
{
    protected $fillable = [
        'ride_code', 'rider_id', 'driver_id', 'vehicle_type',
        'pickup_lat', 'pickup_lng', 'pickup_address',
        'dropoff_lat', 'dropoff_lng', 'dropoff_address',
        'estimated_fare', 'final_fare', 'service_fee',
        'status', 'payment_method', 'is_paid',
        'requested_at', 'accepted_at', 'completed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ride $ride) {
            if (! $ride->ride_code) {
                $ride->ride_code = 'COLOX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}