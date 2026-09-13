<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fare_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_type', ['car', 'motorcycle'])->unique();
            $table->decimal('base_fare', 8, 2);
            $table->decimal('per_km_rate', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fare_settings');
    }
};