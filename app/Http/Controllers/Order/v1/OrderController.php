<?php

namespace App\Http\Controllers\Order\v1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\Order\v1\OrderService;

class OrderController extends Controller
{
    /**
     *
     */
    public function submitOrder(Cart $cart, OrderService $service)
    {
        $result = $service->submitOrder($cart);

        return response()->json([
            'status' => $result['status'],
            'data' => $result['data'],
            'message' => $result['message']
        ], $result['code']);
    }
}
