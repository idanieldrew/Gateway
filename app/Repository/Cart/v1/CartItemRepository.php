<?php

namespace App\Repository\Cart\v1;

use App\Models\CartItem;
use App\Models\Product;
use App\Repository\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartItemRepository implements Repository
{

    public function model()
    {
        return CartItem::query();
    }

    /**
     * Store cart item with product
     *
     * @param Product $product
     * @param string $cart
     * @param float $count
     * @return Model
     */
    public function store(Product $product, string $cart, float $count)
    {
        return $product->cart_items()->create([
            'quantity' => $count,
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
