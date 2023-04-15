<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Observers\CartObserver;
use App\Observers\Order\v1\OrderObserver;
use App\Observers\Payment\v1\PaymentObserver;
use App\Observers\UserObserver;
use App\Services\Payment\Pay;
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
        $this->registerService();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->observers();
    }

    protected function registerService()
    {
        //  Gateways
        $this->app->bind('Pay', Pay::class);
    }

    protected function observers()
    {
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
        Cart::observe(CartObserver::class);
        User::observe(UserObserver::class);
    }
}
