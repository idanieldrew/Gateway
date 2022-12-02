<?php

namespace App\Services\Order\v1;

use App\Repository\Order\v1\OrderRepository;
use App\Repository\Payment\v1\PaymentRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderService
{
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
        DB::beginTransaction();

        try {
            if ($order->last() == null || $order->last()->model->name == 'done') {
                $order = (new OrderRepository())->store(auth()->user());
            }

            if ($order instanceof (Collection::class)) {
                $order = $order->last();
            }

            $sign = $this->hashSign($order->total, $order->id, route('welcome'));

            $res = Http::retry(2, 100)->withToken(config('paystar.gateway_id'))->post(config('paystar.create_address'), [
                "amount" => floatval($order->total),
                "order_id" => $order->id,
                "callback" => route('welcome'),
                "sign" => $sign
            ]);

            if ($res->json('status') == -1) {
                return ['fail', null, 400];
            }
            $payment = (new PaymentRepository())->storeWithPayment($order, $res->json('data'));
            DB::commit();
            return ['success', $payment->token, 200];

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function hashSign($amount, $id, $callback)
    {
        return hash_hmac('SHA512',
            floatval($amount) . '#' . $id . '#' . $callback,
            config('paystar.sign')
        );
    }
}
