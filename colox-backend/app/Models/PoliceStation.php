<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliceStation extends Model
{
    protected $fillable = ['name', 'phone', 'district', 'area', 'lat', 'lng', 'is_active'];
}