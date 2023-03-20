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
    public function register_payment_for_paystar()
    {
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
    }

    /** @test */
    public function register_payment_for_paystar_with_incorrect_data()
    {
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
    }
}
