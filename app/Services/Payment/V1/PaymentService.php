<?php

namespace App\Services\Payment\V1;

use App\Exceptions\DriverNotFoundException;
use App\Facades\Pay;
use App\Models\Order;
use App\Repository\Order\v1\OrderRepository;
use App\Repository\Payment\V1\PaymentRepository;
use App\Services\Service;

class PaymentService extends Service
{
    public function __construct(public PaymentRepository $repository)
    {
    }

    /**
     * Submit payment
     *
     * @param string $order
     * @return array
     */
    public function submit_payment(string $order)
    {
        try {
            $order = (new OrderRepository())->find($order);
            $this->beforePayment($order);
        } catch (\Exception $exception) {
            return $this->response('fail', null, $exception->getMessage(), 400);
        }
        try {
            $payment = $this->repository->storebeforPayment($order);

            $response = Pay::driver(request()->gateway)->create($order, $payment->id);
            if ($response['status'] == 'error') {
                return $this->response(
                    $response['status'],
                    $response['data'],
                    $response['msg'],
                    $response['code']
                );
            }

            $this->repository->updateAfterPayment($payment, $response['data']);

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

    /**
     * Operation before submit payment
     *
     * @param Order $order
     * @return void
     * @throws \Exception
     */
    protected function beforePayment(Order $order): void
    {
        (new OrderRepository())->beforePayment($order);
    }

    /**
     * Operation after pay
     *
     * @param $payment
     * @param $request
     * @return array
     */
    public function outputPay($payment, $request)
    {
        if ($request->status == -1) {
            return $this->response(
                'error',
                null,
                'unsuccessfully',
                500
            );
        }
        $this->repository->updateBeforeVerify($payment, $request->tracking_code, $request->card_number);

        $response = Pay::verify([
            'amount' => $payment->amount,
            'req' => $request
        ]);

        $this->repository->updateStatus($payment, [
            'name' => $response['status'],
            'reason' => $response['message']
        ]);

        return $this->response(
            $response['status'],
            $response['data'],
            $response['message'],
            $response['code']
        );
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
