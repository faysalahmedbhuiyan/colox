<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('complaint_count_at_hold'); // যে মুহূর্তে hold হলো তখনকার count snapshot
            $table->text('system_note')->nullable(); // auto-generated note, e.g. "Auto-held: reached 10 complaints"

            $table->enum('review_status', ['pending_review', 'reinstated', 'confirmed_ban'])
                ->default('pending_review')
                ->index();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_holds');
    }
};