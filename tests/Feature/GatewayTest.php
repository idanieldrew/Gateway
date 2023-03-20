<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(Tests\CustomTest::class, RefreshDatabase::class);

it('register_payment_for_paystar', function () {
    $order = $this->createOrder();

    Http::fake([
        config('paystar.create_address') => Http::response([
            'data' => [
                'token' => 'test_token',
                'ref_num' => 'test_ref'
            ],
            'status' => 1
        ])
    ]);

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertOk();

    $this->assertDatabaseHas('payments', [
        'amount' => $order->total,
    ]);
});

it('register_payment_for_paystar_with_incorrect_data', function () {
    $order = $this->createOrder();

    Http::fake([
        config('paystar.create_address') => Http::response(
            [
                'status' => -1
            ])
    ]);

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertServerError();

    $this->assertDatabaseMissing('payments', [
        'amount' => $order->total,
    ]);
});
