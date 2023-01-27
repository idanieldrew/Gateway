<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Observers\CartObserver;
use App\Observers\Order\v1\OrderObserver;
use App\Observers\Payment\v1\PaymentObserver;
use App\Services\Cart\v1\CartService;
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
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
        Cart::observe(CartObserver::class);
    }

    protected function registerService()
    {
        $this->app->singleton('CartService', CartService::class);
    }
}
