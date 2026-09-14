<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountHold extends Model
{
    protected $fillable = [
        'user_id', 'complaint_count_at_hold', 'system_note',
        'review_status', 'reviewed_by', 'admin_note', 'reviewed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}