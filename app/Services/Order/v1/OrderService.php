<?php

namespace App\Services\Order\v1;

use App\Models\Cart;
use App\Repository\Cart\v1\CartRepository;
use App\Repository\Order\v1\OrderRepository;
use App\Repository\Status\v1\StatusRepositpry;
use App\Services\Service;
use Illuminate\Support\Collection;

class OrderService extends Service
{
    private function repo()
    {
        return resolve(OrderRepository::class);
    }

    /**
     * Submit order
     *
     * @param $request
     * @return array
     */
    public function submitOrder($request): array
    {
        $cart = (new CartRepository)->findById($request->cart);

        if (!$cart->order->isEmpty()) {
            if ($this->repo()->lastStatus($cart->order->last(), 'pending')) {
                if ($this->repo()->checkLastOrderExpire($cart->order->last(), now())) {
                    return $this->response('fail', null, 'you had time for payment for this order', '400');
                }
                return $this->store($cart);
            }
        }

        return $this->store($cart);
    }

    /**
     * Store order
     *
     * @param Cart|Collection $cart
     * @return array
     */
    protected function store(Cart $cart): array
    {
        try {
            foreach ($cart->cart_items as $item) {
                $item->product()->lockForUpdate()->decrement('quantity', $item->quantity);
            }
            // create order
            $order = $this->repo()->store($cart);

            // update status
            (new StatusRepositpry)->updateCartStatus($cart);
        } catch (\Exception $exception) {
            return $this->response('error', null, 'problem', 500);
        }
        return $this->response('success',
            route('payment.port', $order->id),
            'submit new order',
            '201'
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
