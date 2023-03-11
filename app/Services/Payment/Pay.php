<?php

namespace App\Services\Payment;

use App\Exceptions\DriverNotFoundException;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class Pay
{
    protected $driver;

    public function driver(string $name): Pay
    {
        $this->driver = config("payment.map.$name");
            !$this->driver ?? throw new DriverNotFoundException("");
        return $this;
    }

    public function create(Order $order)
    {
        return (new $this->driver)->create($order);
    }

    public function payment(array $data, string $token)
    {
        return (new $this->driver)->payment($data, $token);
    }
}
