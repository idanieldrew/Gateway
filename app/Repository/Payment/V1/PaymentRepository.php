<?php

namespace App\Repository\Payment\V1;

use App\Models\Order;
use App\Models\Payment;
use App\Repository\Repository;
use Illuminate\Http\Request;

class PaymentRepository implements Repository
{
    public function model()
    {
        return Payment::query();
    }

    /**
     * store payment with order relation
     *
     * @param Order $order
     * @param mixed $res
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeWithPayment(Order $order, array $res)
    {
        return $order->payments()->create([
            'amount' => $order->total,
            'token' => $res['token'],
            'ref_num' => $res['ref_num'],
            'expired_at' => now()->addMinutes(30)
        ]);
    }

    /**
     * check expire payment and order
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function checkExpired(Request $request)
    {
        return $this->model()
            ->with('order')
            ->where('token', $request->token)
            ->where('expired_at', '>', now())
            ->whereHas('order', function ($query) {
                $query->where('expired_at', '>', now());
            })->first();
    }

    /**
     * update it,if information is correct
     *
     * @param array $res
     * @return bool
     */
    public function canUpdate(array $res)
    {
        $payment = $this->model()->where('amount', $res['price'])
            ->where('ref_num', $res['ref_num'])->first();

        if ($payment) {
            $payment->model()->update([
                'name' => 'done',
                'reason' => 'payed'
            ]);
            $payment->order->model->update([
                'name' => 'done',
                'reason' => 'payed'
            ]);
            return true;
        }
        return false;
    }
}
