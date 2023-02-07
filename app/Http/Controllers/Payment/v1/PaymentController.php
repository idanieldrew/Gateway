<?php

namespace App\Http\Controllers\Payment\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * @return string
     */
    public function port(Order $order)
    {
        $response = Http::withToken(config('paystar.gateway_id'))->post(config('paystar.create_address'), [
            'amount' => $amount = 5000,
            'order_id' => $id = $order->id,
            'callback' => $callback = route('welcome'),
            'sign' => $this->hashSign($amount, $id, $callback),
        ])->json();

        if ($response['status'] == -1) {
            return 'failed';
        }

        $order->payments()->create([
            'amount' => $order->total,
            'token' => $response['data']['token'],
            'ref_num' => $response['data']['ref_num'],
            'expired_at' => now()->addMinutes(30)
        ]);

        $result = Http::get(config('paystar.payment_address', [
            'token' => $response['data']['token']
        ]));
    }

    private function hashSign($amount, $id, $callback)
    {
        return hash_hmac('SHA512',
            floatval($amount) . '#' . $id . '#' . $callback,
            config('paystar.sign')
        );
    }
}
