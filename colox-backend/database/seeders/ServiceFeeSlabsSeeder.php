<?php

namespace Database\Seeders;

use App\Models\ServiceFeeSlab;
use Illuminate\Database\Seeder;

class ServiceFeeSlabsSeeder extends Seeder
{
    public function run(): void
    {
        ServiceFeeSlab::query()->delete(); // পুরনো slab মুছে নতুন করে বসানো (idempotent seeding)

        ServiceFeeSlab::create(['min_fare' => 0, 'max_fare' => 100, 'fee' => 10]);
        ServiceFeeSlab::create(['min_fare' => 101, 'max_fare' => 250, 'fee' => 15]);
        ServiceFeeSlab::create(['min_fare' => 251, 'max_fare' => 499, 'fee' => 20]);
        ServiceFeeSlab::create(['min_fare' => 500, 'max_fare' => null, 'fee' => 50]);
    }
}