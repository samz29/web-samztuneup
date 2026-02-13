<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    protected $apiKey;
    protected $provider;

    public function __construct()
    {
        $this->apiKey = config('services.maps.api_key');
        $this->provider = config('services.maps.provider', 'here');
    }

    // Ambil koordinat dari alamat menggunakan Maps API (HERE atau Google)
    public function getCoordinatesFromAddress($address)
    {
        try {
            if ($this->provider === 'here') {
                return $this->getCoordinatesFromHereMaps($address);
            } else {
                return $this->getCoordinatesFromGoogleMaps($address);
            }
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    // HERE Maps Geocoding
    protected function getCoordinatesFromHereMaps($address)
    {
        $response = Http::get('https://geocode.search.hereapi.com/v1/geocode', [
            'q' => $address,
            'apiKey' => $this->apiKey,
        ]);

        $data = $response->json();

        if (!empty($data['items'])) {
            $item = $data['items'][0];
            return [
                'latitude' => $item['position']['lat'],
                'longitude' => $item['position']['lng'],
                'formatted_address' => $item['title'],
            ];
        }

        return null;
    }

    // Google Maps Geocoding (fallback)
    protected function getCoordinatesFromGoogleMaps($address)
    {
        $googleApiKey = config('services.google.maps_api_key');

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $googleApiKey,
        ]);

        $data = $response->json();

        if ($data['status'] === 'OK' && !empty($data['results'])) {
            $location = $data['results'][0]['geometry']['location'];
            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
                'formatted_address' => $data['results'][0]['formatted_address'],
            ];
        }

        return null;
    }

    // Hitung jarak antara dua titik koordinat (Haversine formula)
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    // Hitung jarak dari base workshop ke alamat customer
    public function calculateDistanceFromBase($customerAddress)
    {
        // Base workshop location from settings
        $baseLat = AppSetting::getValue('workshop_latitude', config('services.samztune.base_latitude', -6.2088));
        $baseLng = AppSetting::getValue('workshop_longitude', config('services.samztune.base_longitude', 106.8456));

        $coords = $this->getCoordinatesFromAddress($customerAddress);

        if (!$coords) {
            return null;
        }

        $distance = $this->calculateDistance($baseLat, $baseLng, $coords['latitude'], $coords['longitude']);

        return [
            'distance_km' => $distance,
            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],
            'formatted_address' => $coords['formatted_address'],
        ];
    }
}
