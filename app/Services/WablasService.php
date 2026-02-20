<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WablasService
{
    protected string $url;
    protected ?string $key;
    protected ?string $from;

    public function __construct()
    {
        $this->url = config('services.wablas.url');
        $this->key = config('services.wablas.key');
        $this->from = config('services.wablas.from');
    }

    /**
     * Send a message via Wablas.
     *
     * @param string $to phone number including country code
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        if (empty($this->key) || empty($to) || empty($this->url)) {
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
