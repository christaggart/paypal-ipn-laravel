<?php namespace Digitag\PayPalIpnLaravel;

use Illuminate\Support\ServiceProvider;
use Digitag\PayPalIpnLaravel\PayPalIpn;

class PayPalIpnServiceProvider extends ServiceProvider {

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
		$this->package('digitag/paypal-ipn-laravel');
                
                // Configuration file
                $this->publishes([
                    __DIR__.'/../config/config.php' => config_path('paypal.php'),
                ]);
                
                // Migrations
                $this->publishes([
                    __DIR__.'/../migrations/' => database_path('/migrations')
                ], 'migrations');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['paypalipn'] = $this->app->share(function ($app) {
            return new PayPalIpn();
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('paypalipn');
	}

}