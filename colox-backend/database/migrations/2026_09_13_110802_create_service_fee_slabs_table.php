<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_fee_slabs', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_fare', 10, 2);
            $table->decimal('max_fare', 10, 2)->nullable(); // null মানে upper limit নেই (৳500+ এর slab)
            $table->decimal('fee', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fee_slabs');
    }
};