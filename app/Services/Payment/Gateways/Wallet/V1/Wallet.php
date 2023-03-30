<?php

namespace App\Services\Payment\Gateways\Wallet\V1;

use App\Models\Order;
use App\Services\Payment\V1\Payment;
use Illuminate\Http\Response;

class Wallet implements Payment
{
    public function create(Order $order)
    {
        if (auth()->user()->wallet->balance <= $order->total) {
            return [
                'status' => 'fail',
                'data' => "Your wallet don't have balance",
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }
        $amount = auth()->user()->wallet->balance - $order->total;
        auth()->user()->wallet()->update([
            'balance' => $amount
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => "Success operation,your balance is $amount",
                'token' => 'wallet',
                'ref_num' => '1234'
            ],
            'code' => Response::HTTP_OK
        ];
    }

    public function payment(array $data)
    {
        // TODO: Implement payment() method.
    }

    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
