<?php

namespace App\Http\Controllers\Payment\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Services\Payment\V1\PaymentService;

class PaymentController extends Controller
{
    /**
     * @return string
     */
    public function store(PaymentRequest $request, PaymentService $service)
    {
        $result = $service->submit_payment($request->order);

        return response()->json([
            'status' => $result['status'],
            'data' => $result['data'],
            'message' => $result['message']
        ], $result['code']);
    }
}
