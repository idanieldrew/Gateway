<?php

namespace App\Services\Payment\V1;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PayIr implements Payment
{
    const ADD = "payment.driver.payir";

    public function create(Order $order)
    {
        $response = Http::post(config(self::ADD . '.create_address'), [
            'api' => 'test',
            'amount' => 50000,
            'redirect' => route('welcome'),
            'factorNumber' => $order->id,
        ])->json();
        if ($response['status'] == 0) {
            return [
                'success' => 'error',
                'status' => 500,
                'message' => $response['errorMessage']
            ];
        }
        return [
            'status' => 'success',
            'data' => [
                'token' => $response['token'],
                'ref_num' => 0
            ],
            'success' => 200
        ];
    }

    public function payment(array $data)
    {
        return [
            'url' => config(self::ADD . '.payment_address'),
            'data' => $data['token'],
            'method' => 'GET'
        ];
    }

    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
