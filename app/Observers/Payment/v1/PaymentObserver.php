<?php

namespace App\Observers\Payment\v1;

use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        $payment->model()->create([
            'name' => 'not pay',
            'reason' => 'need to pay'
        ]);
    }
}
