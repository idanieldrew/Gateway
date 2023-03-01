<?php

namespace App\Services\Order\v1;

use App\Repository\Cart\v1\CartRepository;
use App\Repository\Order\v1\OrderRepository;
use App\Repository\Status\v1\StatusRepositpry;
use App\Services\Service;

class OrderService extends Service
{
    private function repo()
    {
        return resolve(OrderRepository::class);
    }

    public function submitOrder($request)
    {
        $cart = (new CartRepository)->findById($request->cart);

        if (!$cart->order->isEmpty()) {
            if ($this->repo()->lastStatus($cart->order->last(), 'pending')) {
                return $this->response('fail', null, 'You had order', '400');
            }
            if (!$this->repo()->checkLastOrderExpire($cart->order->last(), now())) {
                return $this->response('fail', null, 'time out', '400');
            }
        }

        foreach ($cart->cart_items as $item) {
            $item->product()->lockForUpdate()->decrement('quantity', $item->quantity);
        }
        try {
            // create order
            $order = $this->repo()->store($cart);

            // update status
            (new StatusRepositpry)->updateCartStatus($cart);
        } catch (\Exception $exception) {
            return $this->response('error', null, 'problem', 500);
        }
        return $this->response('success',
            route('payment.port', $order->id),
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
