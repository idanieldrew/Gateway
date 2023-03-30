<?php

namespace App\Services\Payment\V1;

use App\Exceptions\DriverNotFoundException;
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
                    $response['data'],
                    $response['code']);
            }

            // store payment with order info
            $this->repo()->storeWithOrder($order, $response['data']);

            return $this->response(
                'success',
                Pay::payment($response['data']),
                $response['data']['message'] ?? 'ok',
                200);
        } catch
        (\Exception $exception) {
            if ($exception instanceof DriverNotFoundException) {
                return $this->response('fail', null, $exception->getMessage(), 400);
            }
            return $this->response('error', null, $exception->getMessage(), 500);
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
