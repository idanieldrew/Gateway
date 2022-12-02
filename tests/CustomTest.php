<?php

namespace Tests;

use App\Models\Product;
use App\Models\User;

class CustomTest extends TestCase
{
    protected function FakeData()
    {
        \App\Models\Product::factory()->create();
    }

    protected function CreateUser()
    {
        $this->FakeData();
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->Faking($user);
    }

    protected function Faking($user)
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
}
