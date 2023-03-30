<?php

namespace App\Observers\Payment\v1;

use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        $payment->model()->create([
            'name' => 'pending',
            'reason' => 'need to pay'
        ]);
    }
}
