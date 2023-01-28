<?php

namespace App\Services\Cart\v1;

use App\Http\Resources\Cart\v1\CartItemResource;
use App\Http\Resources\Cart\v1\CartResource;
use App\Models\Product;
use App\Repository\Cart\v1\CartRepository;
use App\Services\Service;
use Illuminate\Http\Request;

class CartService extends Service
{
    private function repository()
    {
        return resolve(CartRepository::class);
    }

    /**
     * Append to cart
     *
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function appendToCart(Product $product, Request $request)
    {
        // check exist cart
        if ($this->repository()->isEmptyCartUser() || $this->repository()->completeStatus()) {
            $resultNewCart = $this->repository()->appendToNewCart($request->count, $product);
            return $this->response(
                $resultNewCart['status'],
                new CartResource($resultNewCart['data']),
                $resultNewCart['message'],
                $resultNewCart['code']
            );
        }

        // check repetitive product in cart
        if ($this->repository()->checkRepetitiveProductInCart($product->id)) {
            return $this->response(
                'fail',
                null,
                'repetitive product',
                400
            );
        }

        $resultOldCart = $this->repository()->appendToOldCart($request->count, $product);
        return $this->response(
            $resultOldCart['status'],
            new CartResource($resultOldCart['data']),
            $resultOldCart['message'],
            $resultOldCart['code']
        );
    }

    protected function response($status, $data, $message, $code): array
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }
}
