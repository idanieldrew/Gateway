<?php

return [
    'driver' => [
        'paystar' => [
            'gateway_id' => env('GATEWAY_ID'),
            'sign' => env('SIGN_PAYSTAR'),
            'create_address' => env('CREATE_PAYSTAR'),
            'payment_address' => env('PAYMENT_PAYSTAR'),
            'verify_paystar' => env('VERIFY_PAYSTAR')
        ],
    ],

    // Payment Service
    'map' => [
        'paystar' => \App\Services\Payment\V1\Paystar::class,
    ]
];
