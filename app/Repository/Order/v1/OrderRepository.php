<?php

namespace App\Repository\Order\v1;

use App\Models\Order;
use App\Models\User;
use App\Repository\Repositpry;

class OrderRepository implements Repositpry
{
    public function model(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::query();
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(User $user)
    {
        return $user->orders()->create([
            'cart_id' => $user->carts->last()->id,
            'total' => $user->carts->last()->total,
            'expired_at' => now()->addHour()
        ]);
    }

    public function expiredOrder()
    {
        return $this->model()->where('expired_at', '<', now())->delete();
    }
}
