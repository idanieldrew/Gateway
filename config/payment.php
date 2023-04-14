<?php

return [
    'driver' => [
        'paystar' => [
            'gateway_id' => env('GATEWAY_ID'),
            'sign' => env('SIGN_PAYSTAR'),
            'create_address' => env('CREATE_PAYSTAR'),
            'payment_address' => env('PAYMENT_PAYSTAR'),
            'verify' => env('VERIFY_PAYSTAR')
        ],
        'payir' => [
            'gateway_id' => env('GATEWAY_ID'),
            'sign' => env('SIGN_PAYIR'),
            'create_address' => env('CREATE_PAYIR'),
            'payment_address' => env('PAYMENT_PAYIR'),
            'verify' => env('VERIFY_PAYIR')
        ],
    ],

    // Payment Service
    'map' => [
        'paystar' => \App\Services\Payment\Gateways\Paystar\V1\Paystar::class,
        'payir' => \App\Services\Payment\Gateways\PayIr\V1\PayIr::class,
        'wallet' => \App\Services\Payment\Gateways\Wallet\V1\Wallet::class
    ]
];
