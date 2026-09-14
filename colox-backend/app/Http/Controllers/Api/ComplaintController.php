<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\ComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function __construct(private ComplaintService $complaintService) {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'against_user_id' => ['nullable', 'exists:users,id'],
            'ride_id' => ['nullable', 'exists:rides,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $complaint = $this->complaintService->file([
            'filed_by' => $request->user()->id,
            'against_user_id' => $request->against_user_id,
            'ride_id' => $request->ride_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Complaint filed successfully.',
            'complaint' => $complaint,
        ], 201);
    }

    /**
     * নিজের ফাইল করা সাম্প্রতিক complaint-গুলো (সর্বোচ্চ ১০টাই DB-তে থাকবে retention rule অনুযায়ী)
     */
    public function myComplaints(Request $request)
    {
        $complaints = Complaint::where('filed_by', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['complaints' => $complaints]);
    }
}