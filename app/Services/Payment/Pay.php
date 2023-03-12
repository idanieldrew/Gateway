<?php

namespace App\Services\Payment;

use App\Exceptions\DriverNotFoundException;
use App\Models\Order;

class Pay
{
    protected string $driver;

    /**
     * @throws DriverNotFoundException
     */
    public function driver(string $name): Pay
    {
        $this->driver = config("payment.map.$name");
        if ($this->driver == null) {
            throw new DriverNotFoundException("$name is not support");
        }
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
