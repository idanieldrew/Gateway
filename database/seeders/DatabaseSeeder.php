<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Sanctum\Sanctum;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create();

        \App\Models\Product::factory()->create();

        /*$cart = $user->cart()->create([
            'total' => 2 * $product->price
        ]);

        $cart->cart_items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
            'total' => 2 * $product->price
        ]);*/
    }
}
