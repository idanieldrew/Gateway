<?php

namespace App\Services\Payment\v1;

use App\Repository\Payment\v1\PaymentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected function repo()
    {
        return resolve(PaymentRepository::class);
    }

    public function verifyPayment(Request $request)
    {
        $sign = $this->hashSign(floatval($request->amount), $request->ref_num, $request->card_number, $request->tracking_code);

        $res = Http::retry(2, 100)->withToken(config('paystar.gateway_id'))->post(config('paystar.verify_paystar'), [
            "ref_num" => floatval($request->ref_num),
            "amount" => $request->amount,
            "tracking_code" => $request->tracking_code,
            "sign" => $sign
        ]);
        if ($res->json('status') == -1) {
            return 'error';
        }

        $res = $this->repo()->canUpdate($res->json('data'));
        return $res ? ['success', 200] : ['fail', 400];
    }

    private function hashSign($amount, $ref_num, $card_number, $tracking_code)
    {
        return hash_hmac('SHA512',
            $amount . '#' . $ref_num . '#' . $card_number . '#' . '#' . $tracking_code,
            config('paystar.sign')
        );
    }
}
