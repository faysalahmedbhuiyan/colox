<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('vehicle_type', ['car', 'motorcycle']);
            $table->string('vehicle_number');
            $table->string('license_number');

            $table->string('nid_photo_path')->nullable();
            $table->string('license_photo_path')->nullable();
            $table->string('profile_photo_path')->nullable();

            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->index();
            $table->text('rejection_reason')->nullable();

            $table->boolean('is_online')->default(false);
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();

            $table->timestamps();

            $table->unique('user_id'); // একজন ইউজারের একটাই driver profile
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};