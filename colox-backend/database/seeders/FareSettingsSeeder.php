<?php

namespace Database\Seeders;

use App\Models\FareSetting;
use Illuminate\Database\Seeder;

class FareSettingsSeeder extends Seeder
{
    public function run(): void
    {
        FareSetting::updateOrCreate(
            ['vehicle_type' => 'car'],
            ['base_fare' => 50, 'per_km_rate' => 30]
        );

        FareSetting::updateOrCreate(
            ['vehicle_type' => 'motorcycle'],
            ['base_fare' => 25, 'per_km_rate' => 15]
        );
    }
}