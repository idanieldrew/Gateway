<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
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
            'name' => 'perfect'
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
            ->assertStatus(400)
            ->assertSee('you cant store new order,because had it');
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

    /** @test */
    public function submit_new_order_after_payment()
    {
        $cart = $this->fakeCart();
        $this->post(route('order.store'), [
            'cart' => $cart->id
        ])
            ->assertCreated();

        // update status order
        $order = Order::first();
        $order->model()->update([
            'name' => 'payment',
            'reason' => 'complete order'
        ]);

        $this->post(route('order.store'), [
            'cart' => $cart->id
        ])
            ->assertSee(['message' => 'submit new order'])
            ->assertStatus(201);
    }

    /** @test */
    public function not_found_cart()
    {
        $this->fakeCart();
        $this->post(route('order.store'), [
            'cart' => Str::uuid()
        ])
            ->assertNotFound();
    }
}
