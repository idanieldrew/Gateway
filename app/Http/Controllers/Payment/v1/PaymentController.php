<?php

namespace App\Http\Controllers\Payment\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\v1\PaymentService;

class PaymentController extends Controller
{
    /**
     * @return string
     */
    public function store(Order $order, PaymentService $service)
    {
        $result = $service->submit_payment($order);

        return response()->json([
            'status' => $result['status'],
            'data' => $result['data'],
            'message' => $result['message']
        ], $result['code']);
    }
}
