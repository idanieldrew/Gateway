<?php

namespace App\Observers\Order\v1;

use App\Models\Order;

class OrderObserver
{
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
