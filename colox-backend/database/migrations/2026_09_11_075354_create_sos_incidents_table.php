<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sos_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code')->unique();

            $table->foreignId('ride_id')->constrained('rides')->cascadeOnDelete();
            $table->foreignId('triggered_by')->constrained('users')->cascadeOnDelete();

            // Trigger মুহূর্তের live location
            $table->decimal('trigger_lat', 10, 7);
            $table->decimal('trigger_lng', 10, 7);

            // দুই পক্ষের সম্পূর্ণ তথ্যের snapshot — encrypted, ride/user data পরে বদলে গেলেও
            // incident-এর সময়কার তথ্য অক্ষত থাকতে হবে (legal record হিসেবে)
            $table->text('rider_snapshot');  // encrypted JSON: name, phone, NID, photo path
            $table->text('driver_snapshot'); // encrypted JSON: name, phone, license, photo, vehicle number

            $table->enum('status', ['active', 'acknowledged', 'resolved'])
                ->default('active')
                ->index();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sos_incidents');
    }
};