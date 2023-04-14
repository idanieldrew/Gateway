<?php

namespace App\Http\Controllers\Payment\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\PayStarCallBackRequest;
use App\Models\Payment;
use App\Services\Payment\V1\PaymentService;

class PaymentController extends Controller
{
    /**
     * Store payment
     *
     * @param PaymentRequest $request
     * @param PaymentService $service
     * @return \Illuminate\Http\JsonResponse
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

    public function callback(Payment $payment, PayStarCallBackRequest $request, PaymentService $service)
    {
        $res = $service->outputPay($payment, $request);

        return response()->json([
            'status' => $res['status'],
            'data' => $res['data'],
            'message' => $res['message']
        ], $res['code']);
    }
}
