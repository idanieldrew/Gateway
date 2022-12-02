<?php

namespace Tests\Feature;

use App\Models\Payment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Http;
use Tests\CustomTest;

class GatewayTest extends CustomTest
{
    use DatabaseMigrations;

    /** @test */
    public function register_order_with_payments()
    {
        $this->CreateUser();
        $this->post(route('payment.pay'))->assertOk();


        $this->assertDatabaseHas('orders', [
            'user_id' => auth()->id()
        ]);

        $this->assertDatabaseHas('payments', [
            'amount' => auth()->user()->carts->last()->total,
        ]);
    }

    /** @test */
    public function register_order_with_payments_with_correct_data()
    {
        $this->CreateUser();

        Http::fake([
            config('paystar.create_address') => Http::response([
                'data' => [
                    'token' => 'test_token',
                    'ref_num' => 'test_ref'
                ],
                'status' => 1
            ])
        ]);
        $this->post(route('payment.pay'))->assertOk();

        $this->assertDatabaseHas('payments', [
            'ref_num' => 'test_ref',
            'token' => 'test_token'
        ]);
    }

    /** @test */
    public function cant_register_order_with_payments_with_wrong_data()
    {
        $this->CreateUser();

        Http::fake([
            config('paystar.create_address') => Http::response([
                'status' => -1
            ])
        ]);
        $this->post(route('payment.pay'))->assertStatus(400);

        $this->assertDatabaseMissing('payments', [
            'ref_num' => 'test_ref',
            'token' => 'test_token'
        ]);
    }

    /** @test */
    public function correct_data_in_navigate()
    {
        $this->CreateUser();

        Http::fake([
            config('paystar.create_address') => Http::response([
                'data' => [
                    'token' => 'test_token',
                    'ref_num' => 'test_ref'
                ],
                'status' => 1
            ]),

            config('paystar.verify_paystar') => Http::response([
                'data' => [
                    'price' => 50000,
                    'ref_num' => 'test_ref'
                ],
                'status' => 1
            ]),
        ]);
        $this->post(route('payment.pay'))->assertOk();

        $this->post(route('payment.verify'), [
            'token' => Payment::first()->token,
        ])->assertOk();
    }

    /** @test */
    public function wrong_ref_num_in_navigate()
    {
        $this->CreateUser();

        Http::fake([
            config('paystar.create_address') => Http::response([
                'data' => [
                    'token' => 'test_token',
                    'ref_num' => 'test_ref'
                ],
                'status' => 1
            ]),

            config('paystar.verify_paystar') => Http::response([
                'data' => [
                    'price' => 50000,
                    'ref_num' => 'test_ref_wrong'
                ],
                'status' => 1
            ]),
        ]);
        $this->post(route('payment.pay'))->assertOk();

        $this->post(route('payment.verify'), [
            'token' => Payment::first()->token,
        ])->assertStatus(400);
    }

    /** @test */
    public function wrong_amount_in_navigate()
    {
        $this->CreateUser();

        Http::fake([
            config('paystar.create_address') => Http::response([
                'data' => [
                    'token' => 'test_token',
                    'ref_num' => 'test_ref'
                ],
                'status' => 1
            ]),

            config('paystar.verify_paystar') => Http::response([
                'data' => [
                    'price' => 10000,
                    'ref_num' => 'test_ref_wrong'
                ],
                'status' => 1
            ]),
        ]);
        $this->post(route('payment.pay'))->assertOk();

        $this->post(route('payment.verify'), [
            'token' => Payment::first()->token,
        ])->assertStatus(400);
    }
}
