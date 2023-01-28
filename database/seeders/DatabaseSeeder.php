<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::factory(['email' => 'test@test.com'])->create();

        $product = \App\Models\Product::factory(['slug' => 'test'])->create();
        \App\Models\Product::factory(['price' => 10, 'slug' => 'test2'])->create();
        \App\Models\Product::factory(['price' => 20, 'slug' => 'test3'])->create();
        \App\Models\Product::factory(['price' => 30, 'slug' => 'test4'])->create();
        \App\Models\Product::factory(['price' => 40, 'slug' => 'test5'])->create();

        $cart = $user->carts()->create([
            'total' => $product->price
        ]);

        $cart->status()->create([
            'name' => 'ناقص',
            'reason' => 'در انتظار سفارش'
        ]);

        $cart->cart_items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'total' => 1 * $product->price
        ]);
    }
}
