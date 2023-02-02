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
        $this->post(route('order.store', $cart->id))
            ->assertCreated();

        $this->assertDatabaseHas('orders', [
            'user_id' => auth()->user()->id,
            'total' => $cart->total
        ]);

        $this->assertDatabaseHas('statuses', [
            'name' => 'prefect'
        ]);
    }
}
