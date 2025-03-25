<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('greater_than_zero', function ($attribute, $value, $parameters, $validator) {
            return intval($value) > 0;
        });

        Validator::extend('no_spaces', function ($attribute, $value, $parameters, $validator) {
            // Use a regular expression to check if there are only spaces in the value
            return !preg_match('/^(&nbsp;|\s)*$/', $value);
        });
    
    }
}
