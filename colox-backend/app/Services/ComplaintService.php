<?php

namespace App\Services;

use App\Models\AccountHold;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ComplaintService
{
    private const AUTO_HOLD_THRESHOLD = 10;

    public function file(array $data): Complaint
    {
        return DB::transaction(function () use ($data) {
            $complaint = Complaint::create($data);

            if (! empty($data['against_user_id'])) {
                $this->incrementAndCheckHold($data['against_user_id']);
            }

            return $complaint;
        });
    }

    private function incrementAndCheckHold(int $userId): void
    {
        // lockForUpdate যাতে একসাথে একাধিক complaint এলে count race condition না হয়
        $user = User::where('id', $userId)->lockForUpdate()->first();

        if (! $user) {
            return;
        }

        $user->increment('complaints_against_count');
        $user->refresh();

        if ($user->complaints_against_count >= self::AUTO_HOLD_THRESHOLD
            && $user->account_status === 'active') {
            $user->update(['account_status' => 'held']);

            AccountHold::create([
                'user_id' => $user->id,
                'complaint_count_at_hold' => $user->complaints_against_count,
                'system_note' => 'Auto-held: reached ' . self::AUTO_HOLD_THRESHOLD . ' complaints.',
                'review_status' => 'pending_review',
            ]);
        }
    }
}