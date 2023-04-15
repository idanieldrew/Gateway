<?php

namespace App\Repository\Payment\V1;

use App\Models\Order;
use App\Models\Payment;
use App\Repository\Repository;

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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storebeforPayment(Order $order)
    {
        return $order->payments()->create([
            'amount' => $order->total,
            'expired_at' => now()->addMinutes(30)
        ]);
    }

    /**
     * store payment with order relation
     *
     * @param Payment $payment
     * @param array $data
     * @return bool
     */
    public function updateAfterPayment(Payment $payment, array $data)
    {
        return $payment->update([
            'ref_num' => $data['ref_num'],
            'token' => $data['token'],
        ]);
    }

    /**
     * Update payment after return payment
     *
     * @param Payment $payment
     * @param string $track_id
     * @param string $card_num
     * @return bool
     */
    public function updateBeforeVerify(Payment $payment, string $track_id, string $card_num): bool
    {
        return $payment->update([
            'track_id' => $track_id,
            'card_num' => $card_num
        ]);
    }

    /**
     * Update status
     *
     * @param Payment $payment
     * @param array $details
     * @return int
     */
    public function updateStatus(Payment $payment, array $details)
    {
        return $payment->model()->update([
            'name' => $details['name'],
            'reason' => $details['reason']
        ]);
    }

    /**
     * Delete records
     *
     * @param int $day
     * @return mixed
     */
    public function deleteExpired(int $day)
    {
        $daysAgo = now()->subDays($day)->toDateString();

        return $this->model()
            ->whereDate('expired_at', '>=', $daysAgo)
            ->delete();
    }
}
