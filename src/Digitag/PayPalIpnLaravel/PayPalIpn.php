<?php

namespace Digitag\PayPalIpnLaravel;

use Digitag\PayPalIpnLaravel\Exception\InvalidIpnException;
use Digitag\PayPalIpnLaravel\Models\IpnOrder;
use Digitag\PayPalIpnLaravel\Models\IpnOrderItem;
use Digitag\PayPalIpnLaravel\Models\IpnOrderItemOption;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\InputStreamListenerBuilder as ListenerBuilder;

/**
 * Class PayPalIpn.
 */
class PayPalIpn
{
    /**
     * Order object.
     *
     * @var IpnOrder
     */
    protected $order = null;

    /**
     * Listens for and stores PayPal IPN requests.
     *
     * @throws InvalidIpnException
     *
     * @return IpnOrder
     */
    public function getOrder()
    {
        $ipnMessage = null;
        $listenerBuilder = new ListenerBuilder();

        if ($this->getEnvironment() == 'sandbox') {
            $listenerBuilder->useSandbox(); // use PayPal sandbox
        }
        $listener = $listenerBuilder->build();

        $listener->onVerified(function (MessageVerifiedEvent $event) {
            $ipnMessage = $event->getMessage();
            Log::info('IPN message verified - '.$ipnMessage);
            $this->order = $this->store($ipnMessage);
        });

        $listener->onInvalid(function (MessageInvalidEvent $event) {
            $report = $event->getMessage();
            Log::warning('Paypal returned invalid for '.$report);
        });

        $listener->onVerificationFailure(function (MessageVerificationFailureEvent $event) {
            $error = $event->getError();

            // Something bad happend when trying to communicate with PayPal!
            Log::error('Paypal verification error - '.$error);
        });

        $listener->listen();

        return $this->order;
    }

    /**
     * Get the PayPal environment configuration value.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return Config::get('paypal-ipn-laravel::environment', 'sandbox');
    }

    /**
     * Set the PayPal environment runtime configuration value.
     *
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        Config::set('paypal-ipn-laravel::environment', $environment);
    }

    /**
     * Stores the IPN contents and returns the IpnOrder object.
     *
     * @param Mdb\PayPal\Ipn\Message $data
     *
     * @return IpnOrder
     */
    private function store(\Mdb\PayPal\Ipn\Message $data)
    {
        if ($data->get('txn_id')) {
            $order = IpnOrder::firstOrNew(['txn_id' => $data->get('txn_id')]);
            $order->fill($data->getAll());
        } else {
            $order = new IpnOrder($data->getAll());
        }
        $order->full_ipn = json_encode(Input::all());
        $order->save();

        $this->storeOrderItems($order, $data->getAll());

        return $order;
    }

    /**
     * Stores the order items from the IPN contents.
     *
     * @param IpnOrder $order
     * @param array    $data
     */
    private function storeOrderItems($order, $data)
    {
        $cart = isset($data[ 'num_cart_items' ]);
        $numItems = (isset($data[ 'num_cart_items' ])) ? $data[ 'num_cart_items' ] : 1;

        for ($i = 0; $i < $numItems; $i++) {
            $suffix = ($numItems > 1 || $cart) ? ($i + 1) : '';
            $suffixUnderscore = ($numItems > 1 || $cart) ? '_'.$suffix : $suffix;

            $item = new IpnOrderItem();
            if (isset($data[ 'item_name'.$suffix ])) {
                $item->item_name = $data[ 'item_name'.$suffix ];
            }
            if (isset($data[ 'item_number'.$suffix ])) {
                $item->item_number = $data[ 'item_number'.$suffix ];
            }
            if (isset($data[ 'quantity'.$suffix ])) {
                $item->quantity = $data[ 'quantity'.$suffix ];
            }
            if (isset($data[ 'mc_gross'.$suffixUnderscore ])) {
                $item->mc_gross = $data[ 'mc_gross'.$suffixUnderscore ];
            }
            if (isset($data[ 'mc_handling'.$suffix ])) {
                $item->mc_handling = $data[ 'mc_handling'.$suffix ];
            }
            if (isset($data[ 'mc_shipping'.$suffix ])) {
                $item->mc_shipping = $data[ 'mc_shipping'.$suffix ];
            }
            if (isset($data[ 'tax'.$suffix ])) {
                $item->tax = $data[ 'tax'.$suffix ];
            }

            $order->items()->save($item);

            // Set the order item options if any
            // $count = 7 because PayPal allows you to set a maximum of 7 options per item
            // Reference: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_html_Appx_websitestandard_htmlvariables
            for ($ii = 1, $count = 7; $ii < $count; $ii++) {
                if (isset($data[ 'option_name'.$ii.'_'.$suffix ])) {
                    $option = new IpnOrderItemOption();
                    $option->option_name = $data[ 'option_name'.$ii.'_'.$suffix ];
                    if (isset($data[ 'option_selection'.$ii.'_'.$suffix ])) {
                        $option->option_selection = $data[ 'option_selection'.$ii.'_'.$suffix ];
                    }
                    $item->options()->save($option);
                }
            }
        }
    }
}
