<?php

namespace App\Http\Controllers\Gateway\v1;

use App\Http\Controllers\Controller;
use App\Repository\Order\v1\OrderRepository;
use App\Repository\Payment\v1\PaymentRepository;
use App\Services\Order\v1\OrderService;
use App\Services\Payment\v1\PaymentService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    protected function service()
    {
        return resolve(OrderService::class);
    }

    protected function repo()
    {
        return resolve(OrderRepository::class);
    }

    public function pay()
    {
        $res = $this->service()->newStore();

        // if was api,we must send token as json to client,for ex
        return response()->json([
            'status' => $res['status'],
            'token' => $res['token'],
        ], $res['code']);
    }

    public function navigate(Request $request)
    {
        $payment = (new PaymentRepository())->checkExpired($request);

        if ($payment) {
            return response()->json([
                'status' => 'success',
                'payment_address' => config('paystar.payment_address')
            ], 200);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'expired'
        ], 400);
        /* For web use it
           return view('navigate', compact(['token' => $payment->token]));*/
    }

    public function verify(Request $request)
    {
        $res = (new PaymentService)->verifyPayment($request);

        return response()->json([
            'status' => $res[0],
        ], $res[1]);
    }
}
