<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WablasService
{
    protected string $url;
    protected ?string $key;
    protected ?string $from;
    protected ?string $userKey;
    protected ?string $secretKey;

    public function __construct()
    {
        $this->url = config('services.wablas.url');
        $this->key = config('services.wablas.key');
        $this->secretKey = config('services.wablas.secret_key');
        $this->from = config('services.wablas.from');
        $this->userKey = config('services.wablas.user_key');
    }

    /**
     * Send a message via Wablas.
     *
     * @param string $to phone number including country code
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message, bool $forUser = false): bool
    {
        // choose appropriate credential; secret_key may be required by some endpoints
        $apiKey = $forUser && $this->userKey ? $this->userKey : $this->key;
        if (empty($apiKey) && !empty($this->secretKey)) {
            $apiKey = $this->secretKey;
        }

        if (empty($apiKey) || empty($to) || empty($this->url)) {
            return false;
        }

        $payload = [
            'phone' => $to,
            'message' => $message,
        ];

        if (!empty($this->from)) {
            $payload['sender'] = $this->from;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->key,
            'Content-Type' => 'application/json',
        ])->post($this->url, $payload);

        return $response->successful();
    }
}
