<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Horizon::auth(function () {
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('APP_ENV') === 'production') {
            $this->app->alias('bugsnag.logger', Log::class);
            $this->app->alias('bugsnag.logger', LoggerInterface::class);
        }
    }
}
