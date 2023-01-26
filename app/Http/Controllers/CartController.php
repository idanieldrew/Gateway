<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     */
    public function store(Request $request)
    {
        $product = Product::query()->where('slug', $request->slug)->first();

        if (auth()->user()->carts->isEmpty() || auth()->user()->carts->last()->status == null || auth()->user()->carts->last()->status->name == 'کامل') {
            DB::beginTransaction();
            try {
                $cart = auth()->user()->carts()->create([
                    'total' => $request->count * $product->price
                ]);

                $product->cart_items()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'total' => $request->count * $product->price
                ]);
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'new cart'
                ]);
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::error("happening error $exception");
                return response()->json([
                    'status' => 'error',
                    'message' => 'problem'
                ], 500);
            }
        }
        // update
        if (auth()->user()->carts->last()->cart_items->where('product_id', $product->id)->isNotEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'repetitive product'
            ], 400);
        }
        $total = 1 * $product->price;

        DB::beginTransaction();
        try {
            auth()->user()->carts->last()->cart_items()->create([
                'product_id' => $product->id,
                'price' => $product->price,
                'total' => $request->count * $total
            ]);
            auth()->user()->carts->last()->update([
                'total' => auth()->user()->carts->last()->total + ($request->count * $total)
            ]);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'cart created'
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("happening error $exception");

            return response()->json([
                'status' => 'error',
                'message' => 'problem'
            ], 500);
        }
    }
}
