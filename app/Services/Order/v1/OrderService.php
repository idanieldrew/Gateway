<?php

namespace App\Services\Order\v1;

use App\Models\Cart;
use App\Repository\Order\v1\OrderRepository;
use App\Repository\Status\v1\StatusRepositpry;
use App\Services\Service;

class OrderService extends Service
{
    private function repo()
    {
        return resolve(OrderRepository::class);
    }

    public function submitOrder(Cart $cart)
    {
        try {
            $order = $this->repo()->store($cart);

            (new StatusRepositpry)->updateCartStatus($cart);
        } catch (\Exception $exception) {
            return $this->response('error', null, 'problem', 500);
        }
        return $this->response('success',
            route('auto-submit', $order->id),
            'ok',
            '200'
        );
    }

    protected function response($status, $data, $message, $code)
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }
}
