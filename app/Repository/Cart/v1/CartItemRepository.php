<?php

namespace App\Repository\Cart\v1;

use App\Models\CartItem;
use App\Models\Product;
use App\Repository\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartItemRepository implements Repository
{

    public function model()
    {
        return CartItem::query();
    }

    /**
     *
     */
    public function store(Product $product, $cart, $count)
    {
        $product->cart_items()->create([
            'cart_id' => $cart,
            'price' => $product->price,
            'total' => $count * $product->price
        ]);
    }

    public function checkRepetitiveProduct($product)
    {
        return auth()->user()->carts->last()->cart_items->where('product_id', $product)->isNotEmpty();
    }
}
