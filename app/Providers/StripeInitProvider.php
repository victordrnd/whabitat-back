<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe;
class StripeInitProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
