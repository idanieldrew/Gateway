<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\CustomTest;

class OrderTest extends CustomTest
{
    use DatabaseMigrations;

    /** @test */
    public function store_order()
    {
        $cart = $this->fakeCart();
        $this->post(route('order.store'), [
            'cart' => $cart->id
        ])
            ->assertCreated();

        $this->assertDatabaseHas('orders', [
            'user_id' => auth()->user()->id,
            'total' => $cart->total
        ]);

        $this->assertDatabaseHas('statuses', [
            'name' => 'prefect'
        ]);
    }

    /** @test */
    public function cant_store_replication_order()
    {
        $cart = $this->fakeCart();
        $this->post(route('order.store'), [
            'cart' => $cart->id
        ])
            ->assertCreated();

        $this->post(route('order.store'), [
            'cart' => $cart->id
        ])
            ->assertStatus(400);
    }

    /** @test */
    public function submit_new_order_after_expire_time()
    {
        $cart = $this->fakeCart();
        $this->post(route('order.store'), [
            'cart' => $cart->id
        ])
            ->assertCreated();

        $this->travel(2)->hours();

        $this->post(route('order.store'), [
            'cart' => $cart->id
        ])
            ->assertSee(['message' => 'submit new order'])
            ->assertStatus(201);
    }
}
