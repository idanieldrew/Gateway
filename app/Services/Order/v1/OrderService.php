<?php

namespace App\Services\Order\v1;

use App\Repository\Order\v1\OrderRepository;
use App\Repository\Payment\v1\PaymentRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OrderService
{
    protected const callback = "http://127.0.0.1:8000";

    protected function repo()
    {
        return resolve(OrderRepository::class);
    }

    /**
     * store order and payments
     *
     * @return array
     * @throws Exception
     */
    public function newStore()
    {
        $order = auth()->user()->orders;

        if ($order->last() == null || $order->last()->model->name == 'done') {
            $order = (new OrderRepository())->store(auth()->user()); // Generate order
        }

        if ($order instanceof (Collection::class)) {
            $order = $order->last();
        }

        $sign = $this->hashSign($order->total, $order->id, static::callback);

        $res = Http::withToken(config('paystar.gateway_id'))->post(config('paystar.create_address'), [
            "amount" => floatval($order->total),
            "order_id" => $order->id,
            "callback" => static::callback,
            "sign" => $sign
        ]);

        if ($res->json('status') == -1) {
            return $this->response('fail', null, 400);
        }

        $payment = (new PaymentRepository())->storeWithPayment($order, $res->json('data')); //register payment
        return $this->response('success', $payment->token, 200);
    }

    private function hashSign($amount, $id, $callback)
    {
        return hash_hmac('SHA512',
            floatval($amount) . '#' . $id . '#' . $callback,
            config('paystar.sign')
        );
    }

    private function response($status, $token, $code)
    {
        return [
            'status' => $status,
            'token' => $token,
            'code' => $code
        ];
    }
}
