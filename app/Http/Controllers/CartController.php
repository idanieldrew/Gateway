<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     *
     */
    public function store(Request $request)
    {
        $product = Product::query()->where('slug', 'test2')->first();

        if (auth()->user()->carts->isEmpty() || auth()->user()->carts->last()->status == null || auth()->user()->carts->last()->status->name == 'کامل') {
            $cart = auth()->user()->carts()->create([
                'total' => $product->price
            ]);

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'total' => $product->price
            ]);

            return 'cart is create';
        }

        // update
        if (auth()->user()->carts->last()->cart_items->where('product_id', $product->id)->first()) {
            return 'این محصول قبلا گذاشتی';
        }
        $total = 1 * $product->price;

        auth()->user()->carts->last()->cart_items()->create([
            'product_id' => $product->id,
            'price' => $product->price,
            'total' => $total
        ]);

        auth()->user()->carts->last()->update([
            'total' => auth()->user()->carts->last()->total + $total
        ]);

        return 'cart created';
    }
}
