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
     * @param Cart $cart
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Cart $cart)
    {
        return $cart->order()->create([
            'user_id' => auth()->user()->id,
            'total' => $cart->total,
            'expired_at' => now()->addHour()
        ]);
    }

    public function expiredOrder()
    {
        return $this->model()->where('expired_at', '<', now())->delete();
    }
}
