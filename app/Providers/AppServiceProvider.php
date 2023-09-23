<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\PaymentGatewayInterface;
use App\Http\Services\Payment\StripePaymentService;
use App\Http\Services\Payment\PaystackPaymentService;
use App\Http\Services\Payment\FlutterwavePaymentService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
