<?php

namespace Tests;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CustomTest extends TestCase
{
    protected function fakeData()
    {
        \App\Models\Product::factory()->create();
    }

    protected function makeProduct()
    {
        return Product::factory(['slug' => 'test'])->create();
    }

    protected function createUser()
    {
        $this->FakeData();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
//        $this->Faking($user);
    }

    protected function faking($user)
    {
        $product = Product::first();

        $cart = $user->carts()->create([
            'total' => 2 * $product->price
        ]);

        $product->cart_items()->create([
            'cart_id' => $cart->id,
            'quantity' => 2,
            'price' => $product->price,
            'total' => 2 * $product->price
        ]);
    }

    protected function fakeCart()
    {
        $this->createUser();
        $this->makeProduct();
        $this->post(route('cart.store', 'test'), [
            'count' => 1
        ]);

        return Cart::first();
    }
}
