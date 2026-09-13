<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class RoutingService
{
    /**
     * দুইটা coordinate-এর মধ্যে distance (km) ও duration (minute) বের করে OSRM থেকে
     */
    public function getRoute(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $baseUrl = config('services.osrm.base_url');
        $coordinates = "{$fromLng},{$fromLat};{$toLng},{$toLat}";

        $response = Http::timeout(5)->get("{$baseUrl}/route/v1/driving/{$coordinates}", [
            'overview' => 'false',
        ]);

        if (! $response->successful() || $response->json('code') !== 'Ok') {
            throw new RuntimeException('Could not calculate route. Please try again.');
        }

        $route = $response->json('routes.0');

        return [
            'distance_km' => round($route['distance'] / 1000, 2),
            'duration_minutes' => round($route['duration'] / 60, 1),
        ];
    }
}