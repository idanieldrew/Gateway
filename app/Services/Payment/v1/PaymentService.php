<?php

namespace App\Services\Payment\v1;

use App\Models\Order;
use App\Services\Service;
use Illuminate\Support\Facades\Http;

class PaymentService extends Service
{
    public function submit_payment(Order $order)
    {
        try {
            $response = Http::withToken(config('paystar.gateway_id'))->post(config('paystar.create_address'), [
                'amount' => $amount = 5000,
                'order_id' => $id = $order->id,
                'callback' => $callback = route('welcome'),
                'sign' => $this->hashSign($amount, $id, $callback),
            ])->json();

            if ($response['status'] == -1) {
                return 'failed';
            }

            $order->payments()->create([
                'amount' => $order->total,
                'token' => $response['data']['token'],
                'ref_num' => $response['data']['ref_num'],
                'expired_at' => now()->addMinutes(30)
            ]);
            return $this->response('success', [
                'url' => config('paystar.payment_address'),
                'token' => $response['data']['token']
            ],
                'success', 200);
        } catch (\Exception $exception) {
            return $this->response('error', null, 'problem', 500);
        }
    }

    /**
     * Hash sign
     *
     * @param $amount
     * @param $id
     * @param $callback
     * @return string
     */
    private function hashSign($amount, $id, $callback)
    {
        return hash_hmac('SHA512',
            floatval($amount) . '#' . $id . '#' . $callback,
            config('paystar.sign')
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
