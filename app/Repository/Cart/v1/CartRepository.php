<?php

namespace App\Repository\Cart\v1;

use App\Models\Cart;
use App\Models\Product;
use App\Repository\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartRepository implements Repository
{
    public function model()
    {
        return Cart::query();
    }

    /**
     *
     */
    public function store($count, $price)
    {
        return auth()->user()->carts()->create([
            'total' => $count * $price
        ]);
    }


    /**
     *
     */
    public function update($count, $price)
    {
        auth()->user()->carts->last()->update([
            'total' => auth()->user()->carts->last()->total + ($count * $price)
        ]);
    }

    /**
     * Append to new cart
     *
     * @param float $count
     * @param Product $product
     * @return array
     */
    public function appendToNewCart(float $count, Product $product)
    {
        DB::beginTransaction();
        try {
            $cart = $this->store($count, $product->price);

            (new CartItemRepository)->store($product, $cart->id, $count);

            DB::commit();
            return [
                'status' => 'success',
                'message' => 'new cart',
                'data' => $cart,
                'code' => 200
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("happening error $exception");
            return [
                'status' => 'error',
                'message' => 'problem',
                'code' => 500
            ];
        }
    }

    /**
     * check empty cart
     *
     * @return bool
     */
    public function isEmptyCartUser()
    {
        return auth()->user()->carts->isEmpty();
    }

    /**
     * check complete status
     *
     * @return bool
     */
    public function completeStatus()
    {
        return auth()->user()->carts->last()->status->name == 'complete';
    }

    /**
     * Check repetitive product in cart
     *
     * @param string $productId
     * @return bool
     */
    public function checkRepetitiveProductInCart(string $productId)
    {
        return (new CartItemRepository)->checkRepetitiveProduct($productId);
    }

    /**
     * Append to old cart
     *
     * @param float $count
     * @param Product $product
     * @return array
     */
    public function appendToOldCart(float $count, Product $product)
    {
        DB::beginTransaction();
        try {
            $this->update($count, $product->price);

            $id = auth()->user()->carts->last()->id;
            $cartItem = (new CartItemRepository)->store($product, $id, $count);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Added to cart',
                'data' => $cartItem->cart,
                'code' => 200
            ];

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("happening error $exception");

            return [
                'status' => 'error',
                'message' => 'problem',
                'code' => 500
            ];
        }
    }
}
