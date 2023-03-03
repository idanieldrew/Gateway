<?php

namespace App\Repository\Order\v1;

use App\Models\Cart;
use App\Models\Order;
use App\Repository\Repository;

class OrderRepository implements Repository
{
    public function model(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::query();
    }

    /**
     * Store order
     *
     * @param Cart $cart
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Cart $cart)
    {
        return $cart->order()->create([
            'user_id' => auth()->user()->id,
            'total' => $cart->total
        ]);
    }

    /**
     * Check expire order
     *
     * @return mixed
     */
    public function expiredOrder()
    {
        return $this->model()
            ->where('expired_at', '<', now())
            ->delete();
    }

    /**
     * Check last status
     *
     * @param Order $order
     * @param string $name
     * @return bool
     */
    public function lastStatus(Order $order, string $name): bool
    {
        return $order->model->name == $name;
    }

    /**
     * Check last order expire
     *
     * @param Order $order
     * @param object $date
     * @return bool
     */
    public function checkLastOrderExpire(Order $order, object $date): bool
    {
        return $order->expired_at > $date;
    }
}
