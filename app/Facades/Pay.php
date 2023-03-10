<?php

namespace App\Facades;

use App\Models\Order;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Pay driver(string $name)
 * @method static array create(Order $order)
 * @method static array payment(array $data, string $token)
 */
class Pay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Pay';
    }
}
