<?php

namespace Digitag\PayPalIpnLaravel;

use Illuminate\Support\ServiceProvider;

class PayPalIpnServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Configuration file
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('paypal.php'),
            ]);

            // Migrations
            $this->publishes([
                __DIR__.'/../../migrations/' => database_path('/migrations'),
            ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('paypalipn', function ($app) {
                return new PayPalIpn();
            });
        $this->app->alias('paypalipn', 'Digitag\PayPalIpnLaravel\PayPalIpn');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['paypalipn'];
    }
}
