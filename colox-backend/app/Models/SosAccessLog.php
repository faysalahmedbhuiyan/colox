<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosAccessLog extends Model
{
    protected $fillable = ['sos_incident_id', 'accessed_by', 'action', 'ip_address'];

    public function accessedBy()
    {
        return $this->belongsTo(User::class, 'accessed_by');
    }
}