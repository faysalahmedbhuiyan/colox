<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'nid_number',
        'account_status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * এই ইউজারের সব role (rider/driver)
     */
    public function roles()
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * নির্দিষ্ট role আছে কিনা চেক করার হেল্পার
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }

    /**
     * Driver profile (যদি driver role থাকে)
     */
    public function driverProfile()
    {
        return $this->hasOne(DriverProfile::class);
    }
}