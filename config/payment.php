<?php

return [
    'driver' => [
        'paystar',
        'idpay'
    ],
    'map' => [
        'paystar' => \App\Services\Payment\V1\Paystar::class,
    ]
];
