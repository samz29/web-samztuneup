<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.biteship.api_key');
        // Use production URL for now since sandbox domain doesn't resolve
        $this->baseUrl = 'https://api.biteship.com/v1';
    }

    /**
     * Get shipping rates/couriers with flexible location data
     * Supports: coordinates, postal codes, area id, mix, by type
     */
    public function getShippingRates($originData, $destinationData, $items = [], $options = [])
    {
        try {
            // Prepare origin data - support multiple formats
            $origin = $this->prepareLocationData($originData, 'origin');

            // Prepare destination data - support multiple formats
            $destination = $this->prepareLocationData($destinationData, 'destination');

            // Default couriers if not specified
            $couriers = $options['couriers'] ?? ['jne', 'tiki', 'pos', 'wahana', 'sicepat', 'anteraja', 'sap', 'jet', 'dse', 'first', 'ninja', 'lion', 'rex', 'ide', 'sentral', 'pandu'];

            // Prepare request payload
            $payload = [
                'origin' => $origin,
                'destination' => $destination,
                'couriers' => $couriers,
                'items' => $items,
            ];

            // Add optional parameters
            if (isset($options['courier_type'])) {
                $payload['courier_type'] = $options['courier_type'];
            }

            if (isset($options['courier_service_type'])) {
                $payload['courier_service_type'] = $options['courier_service_type'];
            }

            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for local development
            ])->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/rates/couriers', $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Log successful response for debugging
                Log::info('Biteship API Success', [
                    'origin' => $origin,
                    'destination' => $destination,
                    'items_count' => count($items),
                    'couriers_count' => count($couriers),
                    'rates_count' => isset($data['pricing']) ? count($data['pricing']) : 0
                ]);

                return $data;
            }

            // Log detailed error information
            $errorData = $response->json();
            Log::error('Biteship API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
                'url' => $this->baseUrl . '/rates/couriers'
            ]);

            // Return error information for specific handling
            return [
                'error' => true,
                'status' => $response->status(),
                'error_code' => $errorData['code'] ?? null,
                'message' => $errorData['error'] ?? 'Unknown error',
                'body' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Biteship Service Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prepare location data for Biteship API
     * Supports coordinates, postal codes, area id, and mixed formats
     */
    private function prepareLocationData($locationData, $type = 'origin')
    {
        $location = [];

        // Handle coordinates (latitude/longitude)
        if (isset($locationData['coordinate'])) {
            $location['coordinate'] = [
                'latitude' => $locationData['coordinate']['latitude'] ?? $locationData['latitude'] ?? null,
                'longitude' => $locationData['coordinate']['longitude'] ?? $locationData['longitude'] ?? null,
            ];
        }

        // Handle postal code
        if (isset($locationData['postal_code'])) {
            $location['postal_code'] = $locationData['postal_code'];
        }

        // Handle area id
        if (isset($locationData['area_id'])) {
            $location['area_id'] = $locationData['area_id'];
        }

        // Handle address components
        if (isset($locationData['address'])) {
            $location['address'] = $locationData['address'];
        }

        if (isset($locationData['country'])) {
            $location['country'] = $locationData['country'];
        }

        if (isset($locationData['province'])) {
            $location['province'] = $locationData['province'];
        }

        if (isset($locationData['city'])) {
            $location['city'] = $locationData['city'];
        }

        if (isset($locationData['district'])) {
            $location['district'] = $locationData['district'];
        }

        if (isset($locationData['subdistrict'])) {
            $location['subdistrict'] = $locationData['subdistrict'];
        }

        // For origin, add contact info if available
        if ($type === 'origin') {
            if (isset($locationData['contact_name'])) {
                $location['contact_name'] = $locationData['contact_name'];
            }
            if (isset($locationData['contact_phone'])) {
                $location['contact_phone'] = $locationData['contact_phone'];
            }
            if (isset($locationData['contact_email'])) {
                $location['contact_email'] = $locationData['contact_email'];
            }
            if (isset($locationData['organization'])) {
                $location['organization'] = $locationData['organization'];
            }
        }

        return $location;
    }

    /**
     * Get shipping rates by coordinates only
     */
    public function getShippingRatesByCoordinates($originLat, $originLng, $destLat, $destLng, $items = [])
    {
        $origin = [
            'coordinate' => [
                'latitude' => $originLat,
                'longitude' => $originLng,
            ]
        ];

        $destination = [
            'coordinate' => [
                'latitude' => $destLat,
                'longitude' => $destLng,
            ]
        ];

        return $this->getShippingRates($origin, $destination, $items);
    }

    /**
     * Get shipping rates by postal codes only
     */
    public function getShippingRatesByPostalCodes($originPostalCode, $destPostalCode, $items = [])
    {
        $origin = [
            'postal_code' => $originPostalCode,
        ];

        $destination = [
            'postal_code' => $destPostalCode,
        ];

        return $this->getShippingRates($origin, $destination, $items);
    }

    /**
     * Get shipping rates by area IDs
     */
    public function getShippingRatesByAreaIds($originAreaId, $destAreaId, $items = [])
    {
        $origin = [
            'area_id' => $originAreaId,
        ];

        $destination = [
            'area_id' => $destAreaId,
        ];

        return $this->getShippingRates($origin, $destination, $items);
    }

    /**
     * Get shipping rates by specific courier type
     */
    public function getShippingRatesByType($originData, $destinationData, $courierType, $items = [])
    {
        $options = [
            'courier_type' => $courierType,
        ];

        return $this->getShippingRates($originData, $destinationData, $items, $options);
    }

    /**
     * Create shipping order
     */
    public function createShippingOrder($orderData)
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/orders', $orderData);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Biteship Create Order Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Biteship Create Order Service Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Track shipping
     */
    public function trackShipping($waybillId)
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/trackings/' . $waybillId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Biteship Track Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Biteship Track Service Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Test API connection and validate credentials
     */
    public function testConnection()
    {
        try {
            // Check if API key is set
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'message' => 'API key is not set',
                    'api_key_length' => 0
                ];
            }

            Log::info('Biteship API Key length: ' . strlen($this->apiKey));
            Log::info('Biteship Base URL: ' . $this->baseUrl);
            // Try to get area information first
            $areaResponse = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/maps/areas', [
                'input' => 'Jakarta',
                'type' => 'origin'
            ]);

            Log::info('Area search response', [
                'status' => $areaResponse->status(),
                'body' => $areaResponse->body()
            ]);

            // Try a different endpoint to test API key validity
            $couriersResponse = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/couriers');

            Log::info('Couriers response', [
                'status' => $couriersResponse->status(),
                'body' => $couriersResponse->body()
            ]);

            // Try to replicate the exact payload from calculateShipping method
            $origin = [
                'postal_code' => config('services.samztune.postal_code'),
                'coordinate' => [
                    'latitude' => config('services.samztune.base_latitude'),
                    'longitude' => config('services.samztune.base_longitude'),
                ],
                'address' => config('services.samztune.address'),
                'city' => config('services.samztune.city'),
                'contact_name' => 'SamzTune-Up',
                'contact_phone' => '081234567890',
            ];

            $destination = [
                'postal_code' => '40111',
                'address' => 'Jl. Test Address, Bandung',
                'city' => 'Bandung',
            ];

            $items = [
                [
                    'name' => 'Test Part',
                    'description' => 'Test part description',
                    'price' => 10000,
                    'length' => 30,
                    'width' => 20,
                    'height' => 10,
                    'weight' => 1000,
                    'quantity' => 1,
                ]
            ];

            // Prepare the payload the same way as getShippingRates method
            $testPayload = [
                'origin' => $this->prepareLocationData($origin, 'origin'),
                'destination' => $this->prepareLocationData($destination, 'destination'),
                'couriers' => 'jne',
                'items' => $items,
            ];

            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/rates/couriers', $testPayload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'API connection successful',
                    'data' => $response->json()
                ];
            }

            // Check for specific error codes
            $errorData = $response->json();
            if (isset($errorData['code'])) {
                if ($errorData['code'] == 40001001) {
                    return [
                        'success' => false,
                        'message' => 'Rates endpoint not available - possibly subscription or API plan issue. Couriers endpoint works but rates calculation is not accessible.',
                        'status' => $response->status(),
                        'error_code' => $errorData['code'],
                        'body' => $response->body()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'API connection failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }
}
