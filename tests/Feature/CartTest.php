<?php

uses(\Tests\CustomTest::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

it('append_new_product_to_new_cart', function () {
    $this->createUser();
    $product = $this->makeProduct();

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
});

it('append_multi_product_to_new_cart', function () {
    $count = 4;
    $this->createUser();
    $product = $this->makeProduct();

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
});

it('append_repetitive_product_to_new_cart', function () {
    $count = 4;
    $this->createUser();
    $product = $this->makeProduct();
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
    ])
        ->assertStatus(400)
        ->assertSee("fail");

    $this->assertDatabaseHas('carts', [
        'total' => $count * $product->price
    ]);
    $this->assertDatabaseMissing('carts', [
        'total' => 5 * $product->price
    ]);
});

it('exist_cart', function () {
    $this->fakeCart();

    $this->get(route('cart.show'))
        ->assertStatus(200);
});

it('not_exist_cart', function () {
    $this->createOrder();

    $this->get(route('cart.show'))
        ->assertStatus(404);
});
