<?php

namespace App\Http\Controllers;

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

        $user = User::query()->where('email', 'test@test.com')->first();
        if ($user->carts->last()->status->name == 'ناقص') {
            // update
//            dd($user->carts->last()->cart_items->where('product_id', $product->id)->first(), $product->id);
            if ($user->carts->last()->cart_items->where('product_id', $product->id)->first()) {
                return 'این محصول قبلا گذاشتی';
            }
            $total = 1 * $product->price;

            $user->carts->last()->cart_items()->create([
                'product_id' => $product->id,
                'price' => $product->price,
                'total' => $total
            ]);

            $user->carts->last()->update([
                'total' => $user->carts->last()->total + $total
            ]);

            return 'in mahsool ezafeh shod';
        }
        dd('generate new cart');
    }
}
