<?php

namespace App\Repository\Status\v1;

use App\Models\Cart;
use App\Models\Status;
use App\Repository\Repository;

class StatusRepositpry implements Repository
{
    public function model()
    {
        return Status::query();
    }

    /**
     *
     */
    public function updateCartStatus(Cart $cart)
    {
        $cart->status()->update([
            'name' => 'prefect',
            'reason' => 'submit order'
        ]);
    }
}
