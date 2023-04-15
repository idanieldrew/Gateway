<?php

namespace App\Services\Cart\v1;

use App\Http\Resources\Cart\v1\CartResource;
use App\Models\Product;
use App\Repository\Cart\v1\CartRepository;
use Illuminate\Http\Request;

class CartService
{
    public function __construct(public CartRepository $repository)
    {
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
        if ($this->repository->isEmptyCartUser() || $this->repository->completeStatus()) {
            $resultNewCart = $this->repository->appendToNewCart($request->count, $product);

            return $this->response(
                $resultNewCart['status'],
                new CartResource($resultNewCart['data']),
                $resultNewCart['message'],
                $resultNewCart['code']
            );
        }

        // check repetitive product in cart
        if ($this->repository->checkRepetitiveProductInCart($product->id)) {
            return $this->response('fail', null, 'repetitive product', 400
            );
        }

        $resultOldCart = $this->repository->appendToOldCart($request->count, $product);
        return $this->response(
            $resultOldCart['status'],
            new CartResource($resultOldCart['data']),
            $resultOldCart['message'],
            $resultOldCart['code']
        );
    }

    public function showCart()
    {
        return $this->repository->show();
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
