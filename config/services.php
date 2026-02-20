<?php

return [
    // ... existing services

    'maps' => [
        'api_key' => env('HERE_MAPS_API_KEY'),
        'provider' => env('MAPS_PROVIDER', 'here'), // 'google' or 'here'
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'tripay' => [
        'api_key' => env('TRIPAY_API_KEY'),
        'private_key' => env('TRIPAY_PRIVATE_KEY'),
        'merchant_code' => env('TRIPAY_MERCHANT_CODE'),
        'production' => env('TRIPAY_PRODUCTION', false),
    ],

    'biteship' => [
        'api_key' => env('BITESHIP_API_KEY'),
        'production' => env('BITESHIP_PRODUCTION', false),
    ],

    'samztune' => [
        'base_latitude' => env('SAMZTUNE_BASE_LATITUDE', -6.2088),
        'base_longitude' => env('SAMZTUNE_BASE_LONGITUDE', 106.8456),
        'postal_code' => env('SAMZTUNE_POSTAL_CODE', '10110'),
        'address' => env('SAMZTUNE_ADDRESS', 'Jl. Workshop SamzTune-Up'),
        'city' => env('SAMZTUNE_CITY', 'Jakarta'),
    ],

    'wablas' => [
        'url' => env('WABLAS_URL'),            // example: https://post.chilema.link/send
        'key' => env('WABLAS_KEY'),
        'user_key' => env('WABLAS_USER_KEY'),  // optional separate key for user notifications
        'from' => env('WABLAS_FROM'),          // optional sender/number
        'admin_phone' => env('WABLAS_ADMIN_PHONE'),
    ],
];
