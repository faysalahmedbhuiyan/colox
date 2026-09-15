<?php

namespace App\Events;

use App\Models\SosIncident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SosTriggered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SosIncident $incident) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.sos-alerts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sos.triggered';
    }

    /**
     * Broadcast-এ কী তথ্য যাবে — ইচ্ছাকৃতভাবে sensitive rider/driver snapshot বাদ দিচ্ছি,
     * শুধু admin-কে alert করার জন্য যা দরকার (incident code, location, ride id) তাই পাঠাচ্ছি।
     * সম্পূর্ণ sensitive তথ্য admin আলাদাভাবে GET /admin/sos/{id} দিয়ে আনবে (যেটা access log তৈরি করে)।
     */
    public function broadcastWith(): array
    {
        return [
            'incident_code' => $this->incident->incident_code,
            'ride_id' => $this->incident->ride_id,
            'trigger_lat' => $this->incident->trigger_lat,
            'trigger_lng' => $this->incident->trigger_lng,
            'triggered_at' => $this->incident->created_at->toIso8601String(),
        ];
    }
}