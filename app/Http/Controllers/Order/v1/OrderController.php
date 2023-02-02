<?php

namespace App\Http\Controllers\Order\v1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{
    /**
     *
     */
    public function submitOrder(Cart $cart, Request $request)
    {
        $order = $cart->order()->create([
            'user_id' => auth()->user()->id,
            'total' => $cart->total,
            'expired_at' => now()->addHour()
        ]);

        $cart->status()->update([
            'name' => 'prefect',
            'reason' => 'submit order'
        ]);

        return response()->json([
            'status' => 'success',
            'data' => route('auto-submit', $order->id)
        ], 201);
    }

}
