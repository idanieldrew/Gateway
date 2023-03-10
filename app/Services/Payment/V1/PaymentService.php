<?php

namespace App\Services\Payment\V1;

use App\Facades\Pay;
use App\Models\Order;
use App\Repository\Payment\V1\PaymentRepository;
use App\Services\Service;

class PaymentService extends Service
{
    protected function repo()
    {
        return resolve(PaymentRepository::class);
    }

    public function submit_payment(Order $order)
    {
        try {
            $response = Pay::driver(request()->gateway)->create($order);
            if ($response['success'] == 500) {
                return $this->response(
                    'error',
                    null,
                    'problem',
                    500);
            }
            $this->repo()->storeWithPayment($order, $response['data']);
            return $this->response(
                'success',
                Pay::payment($response['data'], 'post'),
                'ok',
                200);
        } catch (\Exception $exception) {
            return $this->response(
                'error',
                null,
                'problem',
                500);
        }

        /*try {
            $class = $this->checkExistGateway(request()->gateway);
            if (!$class) {
                return $this->response('fail', null, 'This gateway is not exist', 400);
            }
            $gateway = new $class($order);

            $response = $gateway->create($order);
            if ($response['success'] == 500) {
                return $this->response(
                    'error',
                    null,
                    'problem',
                    500);
            }
            $this->repo()->storeWithPayment($order, $response['data']);

            return $this->response(
                'success',
                $gateway->payment($response['data'], 'post'),
                'success',
                200);
        } catch (\Exception $exception) {
            return $this->response(
                'error',
                null,
                'problem',
                500);
        }*/
    }

    private function checkExistGateway(string $gateway)
    {
        $gateway = "App\Services\Payment\V1\\" . ucwords($gateway);

        if (!class_exists($gateway)) {
            return false;
        }
        return $gateway;
    }

    public function xxx(Payment $payment)
    {
        $payment->create();


    }

    protected function response($status, $data, $message, $code)
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }
}
