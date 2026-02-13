<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    protected $apiKey;
    protected $privateKey;
    protected $merchantCode;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tripay.api_key');
        $this->privateKey = config('services.tripay.private_key');
        $this->merchantCode = config('services.tripay.merchant_code');
        $this->baseUrl = config('services.tripay.base_url', 'https://tripay.co.id/api-sandbox/');
    }

    public function getPaymentChannels()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . 'merchant/payment-channel');

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::error('Tripay getPaymentChannels failed: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Tripay getPaymentChannels error: ' . $e->getMessage());
            return [];
        }
    }

    public function createTransaction($paymentData)
    {
        try {
            $payload = [
                'method' => $paymentData['method'],
                'merchant_ref' => $paymentData['merchant_ref'],
                'amount' => $paymentData['amount'],
                'customer_name' => $paymentData['customer_name'],
                'customer_email' => $paymentData['customer_email'],
                'customer_phone' => $paymentData['customer_phone'] ?? null,
                'order_items' => $paymentData['order_items'],
                'callback_url' => $paymentData['callback_url'] ?? null,
                'return_url' => $paymentData['return_url'] ?? null,
                'expired_time' => $paymentData['expired_time'] ?? (time() + (24 * 60 * 60)), // 24 hours
                'signature' => $this->generateSignature($paymentData),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . 'transaction/create', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Tripay createTransaction failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Tripay createTransaction error: ' . $e->getMessage());
            return null;
        }
    }

    public function verifySignature($data)
    {
        if (!isset($data['signature']) || !isset($data['merchant_ref']) || !isset($data['amount'])) {
            return false;
        }

        $signature = $this->generateSignature([
            'merchant_ref' => $data['merchant_ref'],
            'amount' => $data['amount'],
        ]);

        return hash_equals($signature, $data['signature']);
    }

    private function generateSignature($data)
    {
        $merchantRef = $data['merchant_ref'];
        $amount = $data['amount'];

        $signature = hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);

        return $signature;
    }
}
