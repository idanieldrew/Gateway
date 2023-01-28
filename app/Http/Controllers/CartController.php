<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart\v1\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
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
