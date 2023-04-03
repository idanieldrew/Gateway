<?php

namespace App\Http\Controllers\Cart\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\v1\CartResource;
use App\Models\Product;
use App\Services\Cart\v1\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show cart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CartService $service)
    {
        $res = $service->showCart();

        return response()->json([
            'status' => 'success',
            'payload' => [
                'message' => 'ok',
                'data' => new CartResource($res)
            ]
        ], 200);
    }

    /**
     * Store product in cart
     *
     * @param Product $product
     * @param Request $request
     * @param CartService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Product $product, Request $request, CartService $service)
    {
        $response = $service->appendToCart($product, $request);

        return response()->json([
            'status' => $response['status'],
            'payload' => [
                'message' => $response['message'],
                'data' => $response['data']
            ]
        ], $response['code']);
    }
}
