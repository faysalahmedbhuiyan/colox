<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SosAccessLog;
use App\Models\SosIncident;
use Illuminate\Http\Request;

class SosController extends Controller
{
    public function active()
    {
        $incidents = SosIncident::where('status', 'active')
            ->latest()
            ->get(['id', 'incident_code', 'ride_id', 'trigger_lat', 'trigger_lng', 'status', 'created_at']);

        // এখানে ইচ্ছাকৃতভাবে sensitive snapshot ডেটা লিস্ট view-তে দেখানো হচ্ছে না,
        // শুধু individual incident দেখার সময়ই (নিচের show মেথডে) — এবং তখনই access log তৈরি হবে

        return response()->json(['incidents' => $incidents]);
    }

    /**
     * নির্দিষ্ট incident-এর সম্পূর্ণ sensitive তথ্য দেখা — এই মুহূর্তেই access log তৈরি হবে
     */
    public function show(Request $request, SosIncident $sosIncident)
    {
        SosAccessLog::create([
            'sos_incident_id' => $sosIncident->id,
            'accessed_by' => $request->user()->id,
            'action' => 'viewed',
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['incident' => $sosIncident->load('ride', 'triggeredBy')]);
    }

    public function acknowledge(Request $request, SosIncident $sosIncident)
    {
        $sosIncident->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $request->user()->id,
            'acknowledged_at' => now(),
        ]);

        SosAccessLog::create([
            'sos_incident_id' => $sosIncident->id,
            'accessed_by' => $request->user()->id,
            'action' => 'acknowledged',
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['message' => 'Incident acknowledged.', 'incident' => $sosIncident->fresh()]);
    }
}