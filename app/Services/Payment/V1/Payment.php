<?php

namespace App\Services\Payment\V1;

use App\Models\Order;

interface Payment
{
    public function create(Order $order);

    public function payment(array $data);

    public function verify();
}
