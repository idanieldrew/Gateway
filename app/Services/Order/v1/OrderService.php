<?php

namespace App\Services\Order\v1;

use App\Models\Cart;
use App\Repository\Cart\v1\CartRepository;
use App\Repository\Order\v1\OrderRepository;
use App\Repository\Status\v1\StatusRepository;
use App\Services\Service;
use Illuminate\Support\Collection;

class OrderService extends Service
{
    public function __construct(public OrderRepository $repository)
    {
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
            if ($this->repository->lastStatus($cart->order->last(), 'pending')) {
                if ($this->repository->checkLastOrderExpire($cart->order->last(), now())) {
                    return $this->response('fail', null, 'you cant store new order,because had it', '400');
                }
                return $this->store($cart);
            }
        }

        return $this->store($cart);
    }

    /**
     * Store order for cart
     *
     * @param Cart|Collection $cart
     * @return array
     */
    protected function store(Cart $cart): array
    {
        try {
            foreach ($cart->cart_items as $item) {
                $item->product()
                    ->lockForUpdate()
                    ->decrement('quantity', $item->quantity);
            }
            $order = $this->repository->store($cart); // create order

            (new StatusRepository)
                ->updateStatus(
                    $cart,
                    'perfect',
                    'submit order'
                ); // update status

        } catch (\Exception $exception) {
            return $this->response('error', null, $exception->getMessage(), 500);
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
