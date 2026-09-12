<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'nid_photo_path',
        'license_photo_path',
        'profile_photo_path',
        'verification_status',
        'rejection_reason',
        'is_online',
        'current_lat',
        'current_lng',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}