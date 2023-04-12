<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(Tests\CustomTest::class, RefreshDatabase::class);

it('expired_order_before_submit_payment', function () {
    $order = $this->createOrder();

    $this->travel(2)->hours();

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])
        ->assertStatus(400)
        ->assertSee('Order was expired');

    $this->assertDatabaseMissing('payments', [
        'amount' => $order->total,
    ]);
});

it('opened_order_before_submit_payment', function () {
    $order = $this->createOrder();
    $order->model()->update([
        'name' => 'complete',
        'reason' => 'payed'
    ]);
    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])
        ->assertStatus(400)
        ->assertSee('Order was completed');

    $this->assertDatabaseMissing('payments', [
        'amount' => $order->total,
    ]);
});

it('check_paid_before_submit_payment', function () {
    $order = $this->createOrder();

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertOk();

    $order->payments->last()->model()->update([
        'name' => 'complete',
        'reason' => 'complete pay'
    ]);

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])
        ->assertStatus(400)
        ->assertSee('Order was payed');
});

it('pay_again_before_submit_payment', function () {
    $order = $this->createOrder();

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ]);

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])
        ->assertStatus(200);
});

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
