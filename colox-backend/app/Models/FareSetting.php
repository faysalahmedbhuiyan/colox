<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FareSetting extends Model
{
    protected $fillable = ['vehicle_type', 'base_fare', 'per_km_rate'];
}