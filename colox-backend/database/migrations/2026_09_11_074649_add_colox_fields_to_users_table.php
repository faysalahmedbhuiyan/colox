<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->after('email');
            $table->string('nid_number')->unique()->nullable()->after('phone');

            // 'active' = normal, 'held' = auto-suspended pending admin review, 'banned' = admin confirmed ban
            $table->enum('account_status', ['active', 'held', 'banned'])
                ->default('active')
                ->after('nid_number')
                ->index();

            // কখনো কমে না — complaint delete হলেও এই সংখ্যা অক্ষত থাকে, hold trigger এর জন্য ব্যবহৃত হয়
            $table->unsignedInteger('complaints_against_count')->default(0)->after('account_status');

            $table->timestamp('last_login_at')->nullable()->after('complaints_against_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'nid_number', 'account_status', 'complaints_against_count', 'last_login_at']);
        });
    }
};