<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    protected $fillable = [
        'complaint_code', 'filed_by', 'against_user_id', 'ride_id',
        'subject', 'description', 'status',
        'admin_resolution_note', 'resolved_by', 'resolved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Complaint $complaint) {
            if (! $complaint->complaint_code) {
                $complaint->complaint_code = 'CMP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function filedBy()
    {
        return $this->belongsTo(User::class, 'filed_by');
    }

    public function againstUser()
    {
        return $this->belongsTo(User::class, 'against_user_id');
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
}