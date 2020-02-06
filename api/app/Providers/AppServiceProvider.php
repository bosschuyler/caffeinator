<?php

namespace App\Providers;

use Validator;

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
        collect()->macro('mapWithKeys_v2', function ($callback) {
            $result = [];

            foreach ($this->items as $key => $value) {
                $assoc = $callback($value, $key);

                foreach ($assoc as $mapKey => $mapValue) {
                    $result[$mapKey] = $mapValue;
                }
            }

            return new static($result);
        });
        
        //
        Validator::extend('key', function ($attribute, $value, $parameters, $validator) {
			return preg_match("/^[A-Z0-9\_]+$/", $value);
        });

        Validator::extend('key_lower', function ($attribute, $value, $parameters, $validator) {
			return preg_match("/^[a-z0-9\_]+$/", $value);
        });

        Validator::extend('key_alpha', function ($attribute, $value, $parameters, $validator) {
			return preg_match("/^[a-zA-Z0-9\_]+$/", $value);
        });

        // ^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$
        Validator::extend('has_letter', function($attribute, $value, $parameters, $validator) {
            return preg_match("/[a-zA-Z]/", $value);
        });

        Validator::extend('has_uppercase', function($attribute, $value, $parameters, $validator) {
            return preg_match("/[A-Z]/", $value);
        });

        Validator::extend('has_lowercase', function($attribute, $value, $parameters, $validator) {
            return preg_match("/[a-z]/", $value);
        });

        Validator::extend('has_number', function($attribute, $value, $parameters, $validator) {
            return preg_match("/[0-9]/", $value);
        });

        Validator::extend('has_special', function($attribute, $value, $parameters, $validator) {
            return preg_match("/[!@#$%&*+=]/", $value);
        });
    }
}
