<?php

namespace App\Services\Phone\Provider;


use Illuminate\Support\ServiceProvider;

use Mailgun\Mailgun;

class PhoneServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {}

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('phone.gateway', function($app, $params) {
            $gateway = new \App\Services\Phone\PhoneGateway();
            $gateway->addHandler('twilio', $app['twilio.phone.handler']);
            return $gateway;
        });
    }
}