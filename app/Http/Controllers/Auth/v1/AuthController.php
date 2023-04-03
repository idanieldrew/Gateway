<?php

namespace App\Http\Controllers\Auth\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\AuthRequest;
use App\Models\Product;
use App\Repository\Auth\v1\AuthRepository;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    protected function repo()
    {
        return resolve(AuthRepository::class);
    }

    public function register(AuthRequest $request)
    {
        $user = $this->repo()->store($request);

        $token = $user->createToken('token')->plainTextToken;

//        $this->fakeData($user);
        return response()->json([
            'status' => 'success',
            'token' => $token
        ], Response::HTTP_CREATED);
    }

    /**
     * just for fake data,because this repo doesn't have cart or product page.
     *
     * @param $user
     * @return void
     */
    private function fakeData($user)
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
