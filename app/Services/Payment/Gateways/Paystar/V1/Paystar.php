<?php

namespace App\Services\Payment\Gateways\Paystar\V1;

use App\Models\Order;
use App\Services\Payment\V1\Payment;
use Illuminate\Support\Facades\Http;

class Paystar implements Payment
{
    const ADD = "payment.driver.paystar";


    public function create(Order $order)
    {
        $response = Http::withToken(config(self::ADD . '.gateway_id'))
            ->post(config(self::ADD . '.create_address'), [
                'amount' => $amount = $order->total,
                'order_id' => $id = $order->id,
                'callback' => $callback = route('welcome'),
                'sign' => $this->hashSign($amount, $id, $callback),
            ])->json();

        if ($response['status'] == -1) {
            return [
                'status' => 'error',
                'data' => $response['data'],
                'msg' => $response['message'],
                'code' => 400
            ];
        }

        return [
            'status' => 'success',
            'data' => $response['data'],
            'code' => 200
        ];
    }

    public function payment(array $data)
    {
        return [
            'url' => config(self::ADD . '.payment_address'),
            'data' => $data['token'],
            'method' => 'POST'
        ];
    }

    public function verify()
    {
        $response = Http::withToken(config(self::ADD . '.gateway_id'))
            ->post(config(self::ADD . '.create_address'), [
                'ref_num' => $refNum = 5000,
                'amount' => $amount = $order->id,
                'sign' => $this->hashSign($amount, $refNum, $callback)
            ])->json();
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
