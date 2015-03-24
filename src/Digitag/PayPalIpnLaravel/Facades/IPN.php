<?php namespace Digitag\PayPalIpnLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class IPN extends Facade {
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'paypalipn'; }

}