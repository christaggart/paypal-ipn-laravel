PayPal IPN for Laravel 4
========================

This package allows for the painless creation of a PayPal IPN listener in the Laravel 4 framework.
Originally developed by logicalgrape, it was forked an updated to cope with the POODLE vulnerability and
consequential disabling of SSLv3 by PayPal


Installation
------------

Edit `composer.json` and add:

```json
{
    "require": {
        "riccamastellone/paypal-ipn-laravel": "2.*"
    }
}
```

And install dependencies:

```bash
$ composer update
```

Usage
-----

Find the `providers` key in `app/config/app.php` and register the **PayPal IPN Service Provider**.

```php
'providers' => array(
    // ...

    'Digitag\PayPalIpnLaravel\PayPalIpnServiceProvider',
)
```

Find the `aliases` key in `app/config/app.php` and register the **PayPal IPN Facade**.

```php
'aliases' => array(
    // ...

    'IPN' => 'Digitag\PayPalIpnLaravel\Facades\IPN',
)
```


Migrations
----------

Run the migrations to create the tables to hold IPN data

```bash
$ php artisan migrate --package digitag/paypal-ipn-laravel
```


Configuration
-------------

Publish and edit the configuration file

```bash
$ php artisan config:publish digitag/paypal-ipn-laravel
```


Example
-------

Create the controller PayPal will POST to

```bash
$ php artisan controller:make IpnController --only=store
```

Open the newly created controller and add the following to the store action

```php
$order = IPN::getOrder();
```

Edit `app/routes.php` and add:

```php
Route::post('ipn', array('uses' => 'IpnController@store', 'as' => 'ipn'));
```


Resources
---------
To help with IPN testing, PayPal provides the
[PayPal IPN Simulator](https://developer.paypal.com/webapps/developer/applications/ipn_simulator).


Support
-------

[Please open an issue on GitHub](https://github.com/riccamastellone/paypal-ipn-laravel/issues)


License
-------

GeocoderLaravel is released under the MIT License. See the bundled
[LICENSE](https://github.com/riccamastellone/paypal-ipn-laravel/blob/master/LICENSE)
file for details.