<?php

namespace App\Observers\Order\v1;

use App\Models\Order;

class OrderObserver
{
    public function creating(Order $order)
    {
        $order->expired_at = now()->addHour();
    }

    /**
     * Handle the Post "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function created(Order $order)
    {
        $order->model()->create([
            'name' => 'pending',
            'reason' => 'need to pay'
        ]);
    }
}
