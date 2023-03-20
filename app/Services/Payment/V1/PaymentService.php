<?php

namespace App\Services\Payment\V1;

use App\Facades\Pay;
use App\Repository\Order\v1\OrderRepository;
use App\Repository\Payment\V1\PaymentRepository;
use App\Services\Service;

class PaymentService extends Service
{
    protected function repo()
    {
        return resolve(PaymentRepository::class);
    }

    /**
     * Submit payment
     *
     * @param string $order
     * @return array
     */
    public function submit_payment(string $order)
    {
        $order = (new OrderRepository())->show($order);
        try {
            $response = Pay::driver(request()->gateway)->create($order);
            if ($response['status'] == 500) {
                return $this->response(
                    $response['success'],
                    null,
                    $response['message'],
                    $response['status']);
            }

            $this->repo()->storeWithOrder($order, $response['data']);

            return $this->response(
                'success',
                Pay::payment($response['data']),
                'ok',
                200);
        } catch
        (\Exception $exception) {
            return $this->response(
                'error',
                null,
                $exception->getMessage(),
                500);
        }
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
