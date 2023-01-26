<?php

namespace App\Observers;

use App\Models\Cart;

class CartObserver
{
    public function created(Cart $cart)
    {
        $cart->status()->create([
            'name' => 'ناقص',
            'reason' => 'در انتظار سفارش'
        ]);
    }
}
