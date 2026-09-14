<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountHoldController extends Controller
{
    public function pending()
    {
        $holds = AccountHold::with('user')
            ->where('review_status', 'pending_review')
            ->latest()
            ->get();

        return response()->json(['holds' => $holds]);
    }

    /**
     * ইউজারের complaint history দেখা (admin decision নেওয়ার জন্য)
     */
    public function show(AccountHold $accountHold)
    {
        $accountHold->load('user');

        $complaints = \App\Models\Complaint::where('against_user_id', $accountHold->user_id)
            ->latest()
            ->get();

        return response()->json([
            'hold' => $accountHold,
            'recent_complaints' => $complaints,
        ]);
    }

    public function reinstate(Request $request, AccountHold $accountHold)
    {
        $accountHold->user->update(['account_status' => 'active']);

        $accountHold->update([
            'review_status' => 'reinstated',
            'reviewed_by' => $request->user()->id,
            'admin_note' => $request->admin_note,
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Account reinstated.', 'hold' => $accountHold->fresh()]);
    }

    public function ban(Request $request, AccountHold $accountHold)
    {
        $validator = Validator::make($request->all(), [
            'admin_note' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $accountHold->user->update(['account_status' => 'banned']);

        $accountHold->update([
            'review_status' => 'confirmed_ban',
            'reviewed_by' => $request->user()->id,
            'admin_note' => $request->admin_note,
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Account banned.', 'hold' => $accountHold->fresh()]);
    }
}