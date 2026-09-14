<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Console\Command;

class CleanupOldComplaints extends Command
{
    protected $signature = 'complaints:cleanup';
    protected $description = 'প্রতিটা ইউজারের নামে থাকা complaint-এর সর্বশেষ ১০টা রেখে বাকিগুলো ডিলিট করে (persistent count অক্ষত থাকে)';

    public function handle(): void
    {
        $userIds = Complaint::whereNotNull('against_user_id')
            ->distinct()
            ->pluck('against_user_id');

        $totalDeleted = 0;

        foreach ($userIds as $userId) {
            $idsToKeep = Complaint::where('against_user_id', $userId)
                ->latest()
                ->take(10)
                ->pluck('id');

            $deleted = Complaint::where('against_user_id', $userId)
                ->whereNotIn('id', $idsToKeep)
                ->delete();

            $totalDeleted += $deleted;
        }

        $this->info("Cleanup complete. {$totalDeleted} old complaint record(s) deleted.");
    }
}