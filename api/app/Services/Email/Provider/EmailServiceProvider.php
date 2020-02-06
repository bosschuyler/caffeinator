<?php

namespace App\Services\Email\Provider;


use Illuminate\Support\ServiceProvider;

use Mailgun\Mailgun;

use App\Services\Email\Handler\MailgunEmailHandler;

class EmailServiceProvider extends ServiceProvider
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
        $this->app->singleton('mailgun', function($app, $params) {
            $data = config('services.mailgun');
            return new Mailgun($data['public']);          
        });

        $this->app->singleton('mailgun.email.handler', function($app, $params) {
            return new MailgunEmailHandler($app['mailgun']);
        });

        $this->app->singleton('email.gateway', function($app, $params) {
            $gateway = new \App\Services\Email\EmailGateway();
            $gateway->addHandler('mailgun', $app['mailgun.email.handler']);
            return $gateway;
        });
    }
}