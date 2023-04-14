<?php

namespace App\Services\Payment;

use App\Exceptions\DriverNotFoundException;
use App\Models\Order;

class Pay
{
    protected string|null $driver;

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

    public function create(Order $order, string $payment)
    {
        return (new $this->driver)->create($order, $payment);
    }

    public function payment(array $data)
    {
        return (new $this->driver)->payment($data);
    }

    public function verify(array $data)
    {
        return (new $this->driver)->verify($data);
    }
}
