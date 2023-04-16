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

it('closed_order_before_submit_payment', function () {
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
    $this->mockForCreateAddPaystar();
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
    $this->mockForCreateAddPaystar();

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
    $this->mockForCreateAddPaystar();

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertOk();

    $this->assertDatabaseHas('payments', [
        'amount' => $order->total,
    ]);
});

it('cant_complete_register_payment_for_paystar_with_incorrect_data', function () {
    $order = $this->createOrder();

    Http::fake([
        config('payment.driver.paystar.create_address') => Http::response(
            [
                'status' => -1,
                'message' => 'fail'
            ])
    ]);

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertServerError();

    $this->assertDatabaseHas('payments', [
        'ref_num' => null,
    ]);
});

it('incorrect_data_to_callback_url', function () {
    $order = $this->createOrder();
    $this->mockForCreateAddPaystar();

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertStatus(200);

    $this->post(route('callback', ['payment' => \App\Models\Payment::first()->id]), [
        'status' => '-1',
        'order_id' => 'string',
        'ref_num' => 'string',
        'transaction_id' => 'string',
        'card_number' => 'string',
        'tracking_code' => 'string'
    ])->assertStatus(500);

    $this->assertDatabaseHas('payments', [
        'card_num' => null,
    ]);
});

it('correct_verify', function () {
    $order = $this->createOrder();
    $this->mockForCreateAddPaystar();

    Http::fake([
        config('payment.driver.paystar.verify') => Http::response([
            'data' => 'test',
            'message' => 'success',
            'status' => 1
        ])
    ]);

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertStatus(200);

    $this->post(route('callback', ['payment' => \App\Models\Payment::first()->id]), [
        'status' => '1',
        'order_id' => 'string',
        'ref_num' => 'string',
        'transaction_id' => 'string',
        'card_number' => 'string',
        'tracking_code' => 'string'
    ])->assertStatus(200);

    $this->assertDatabaseHas('payments', [
        'track_id' => 'string'
    ]);
    $this->assertDatabaseHas('statuses', [
        'name' => 'success'
    ]);
});

it('incorrect_verify', function () {
    $order = $this->createOrder();
    $this->mockForCreateAddPaystar();

    Http::fake([
        config('payment.driver.paystar.verify') => Http::response([
            'data' => '',
            'message' => 'fail',
            'status' => '-1'
        ])
    ]);

    $this->post(route('payment.port'), [
        'order' => $order->id,
        'gateway' => 'paystar'
    ])->assertStatus(200);

    $this->post(route('callback', ['payment' => \App\Models\Payment::first()->id]), [
        'status' => '1',
        'order_id' => 'string',
        'ref_num' => 'string',
        'transaction_id' => 'string',
        'card_number' => 'string',
        'tracking_code' => 'string'
    ])->assertStatus(400);
});

