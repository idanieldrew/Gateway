<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\CustomTest;

class CartTest extends CustomTest
{
    use DatabaseMigrations;

    /** @test */
    public function append_new_product_to_new_cart()
    {
        $this->createUser();
        $product = $this->product();

        $this->post(route('cart.store', 'test'), [
            'count' => 1
        ])
            ->assertOk();

        $this->assertDatabaseHas('carts', [
            'total' => $product->price
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'total' => $product->price
        ]);
    }

    /** @test */
    public function append_multi_product_to_new_cart()
    {
        $count = 4;
        $this->createUser();
        $product = $this->product();

        $this->post(route('cart.store', 'test'), [
            'count' => $count
        ])
            ->assertOk();

        $this->assertDatabaseHas('carts', [
            'total' => 4 * $product->price
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'total' => 4 * $product->price
        ]);
    }

    /** @test */
    public function append_repetitive_product_to_new_cart()
    {
        $count = 4;
        $this->createUser();
        $product = $this->product();
        $cart = auth()->user()->carts()->create([
            'total' => $count * $product->price
        ]);

        $product->cart_items()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'total' => $count * $product->price
        ]);

        $this->post(route('cart.store', 'test'), [
            'count' => 1
        ])->assertStatus(400);

        $this->assertDatabaseHas('carts', [
            'total' => $count * $product->price
        ]);
        $this->assertDatabaseMissing('carts', [
            'total' => 5 * $product->price
        ]);
    }
}
