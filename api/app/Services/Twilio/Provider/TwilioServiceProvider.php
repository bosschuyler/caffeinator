<?php

namespace App\Services\Twilio\Provider;


use Illuminate\Support\ServiceProvider;

use Twilio\Rest\Client;

use App\Services\Twilio\Handler\MessageHandler;
use App\Services\Twilio\Handler\PhoneHandler;
use App\Services\Twilio\Handler\AccountHandler;

class TwilioServiceProvider extends ServiceProvider
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

        //
        $this->app->singleton('twilio', function($app, $params) {
            $data = config('services.twilio');
            return new Client($data['account_sid'], $data['token']);            
        });

        $this->app->singleton('twilio.handler', function($app, $params) {
            return new MessageHandler($app['twilio']);
        });      

        $this->app->singleton('twilio.account.handler', function($app, $params) {
            $cacheFactory = $app['cache'];
            $store = $cacheFactory->store('tokens');

            return new AccountHandler($app['twilio'], $store);
        });

        $this->app->singleton('twilio.phone.handler', function($app, $params) {
            return new PhoneHandler($app['twilio']);
        });  
    }
}