<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Payment;
use App\Observers\Order\v1\OrderObserver;
use App\Observers\Payment\v1\PaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
