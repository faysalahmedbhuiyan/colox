<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SosIncident extends Model
{
    protected $fillable = [
        'incident_code', 'ride_id', 'triggered_by',
        'trigger_lat', 'trigger_lng',
        'rider_snapshot', 'driver_snapshot',
        'status', 'acknowledged_by', 'acknowledged_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (SosIncident $incident) {
            if (! $incident->incident_code) {
                $incident->incident_code = 'SOS-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    /**
     * rider_snapshot এবং driver_snapshot — দুটোই DB-তে encrypted থাকবে,
     * কিন্তু PHP-তে access করার সময় Laravel অটোমেটিক decrypt করে দেবে (এই cast-এর কারণে)
     */
    protected function casts(): array
    {
        return [
            'rider_snapshot' => 'encrypted:array',
            'driver_snapshot' => 'encrypted:array',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(SosAccessLog::class);
    }
}