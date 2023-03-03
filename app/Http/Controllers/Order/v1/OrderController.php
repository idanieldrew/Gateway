<?php

namespace App\Http\Controllers\Order\v1;

use App\Http\Controllers\Controller;
use App\Services\Order\v1\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Submit order
     *
     * @param Request $request
     * @param OrderService $service
     * @return JsonResponse
     */
    public function submitOrder(Request $request, OrderService $service)
    {
        $result = $service->submitOrder($request);

        return response()->json([
            'status' => $result['status'],
            'data' => $result['data'],
            'message' => $result['message']
        ], $result['code']);
    }
}
