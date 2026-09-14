<?php

namespace Database\Seeders;

use App\Models\PoliceStation;
use Illuminate\Database\Seeder;

class PoliceStationsSeeder extends Seeder
{
    /**
     * এগুলো DEMO/PLACEHOLDER তথ্য — নাম, ফোন নম্বর, coordinate আসল না।
     * Admin panel থেকে পরে আসল তথ্য দিয়ে edit/replace করতে হবে launch-এর আগে।
     */
    public function run(): void
    {
        $demoStations = [
            ['name' => 'Noakhali Sadar Model Thana (DEMO)', 'phone' => '01700000000', 'area' => 'Sadar', 'lat' => 22.8241, 'lng' => 91.0980],
            ['name' => 'Begumganj Thana (DEMO)', 'phone' => '01700000001', 'area' => 'Begumganj', 'lat' => 22.9167, 'lng' => 91.1167],
            ['name' => 'Companiganj Thana (DEMO)', 'phone' => '01700000002', 'area' => 'Companiganj', 'lat' => 22.8833, 'lng' => 91.2667],
            ['name' => 'Senbagh Thana (DEMO)', 'phone' => '01700000003', 'area' => 'Senbagh', 'lat' => 23.0167, 'lng' => 91.1500],
            ['name' => 'Chatkhil Thana (DEMO)', 'phone' => '01700000004', 'area' => 'Chatkhil', 'lat' => 23.0500, 'lng' => 91.0667],
        ];

        foreach ($demoStations as $station) {
            PoliceStation::updateOrCreate(
                ['name' => $station['name']],
                array_merge($station, ['district' => 'Noakhali', 'is_active' => true])
            );
        }
    }
}