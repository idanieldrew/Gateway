<?php

namespace App\Services\Payment\V1;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Paystar implements Payment
{
    const ADD = "payment.driver.paystar";

    public function create(Order $order)
    {
        $response = Http::withToken(config(self::ADD . '.gateway_id'))->post(config(self::ADD . '.create_address'), [
            'amount' => $amount = 5000,
            'order_id' => $id = $order->id,
            'callback' => $callback = route('welcome'),
            'sign' => $this->hashSign($amount, $id, $callback),
        ])->json();

        if ($response['status'] == -1) {
            return [
                'status' => 'error',
                'success' => 500
            ];
        }

        return [
            'status' => 'success',
            'data' => $response['data'],
            'success' => 200
        ];
    }

    public function payment(array $data, string $method)
    {
        return [
            'url' => config(self::ADD . '.payment_address'),
            'data' => $data['token'],
            'method' => $method
        ];
    }

    public function verify()
    {
        // TODO: Implement verify() method.
    }

    /**
     * Hash sign
     *
     * @param $amount
     * @param $id
     * @param $callback
     * @return string
     */
    private function hashSign($amount, $id, $callback)
    {
        return hash_hmac('SHA512',
            floatval($amount) . '#' . $id . '#' . $callback,
            config(self::ADD . '.sign')
        );
    }
}
