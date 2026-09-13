<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceFeeSlab extends Model
{
    protected $fillable = ['min_fare', 'max_fare', 'fee'];
}