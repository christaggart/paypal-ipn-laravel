<?php namespace Digitag\PayPalIpnLaravel\Exception;

class InvalidIpnException extends \Exception {
    
    protected $data;

    public function __construct($message, $response = null) {
        $this->data = $response;
        parent::__construct($message, 0, null);
    }
    
    final function getMessage() {
        return $this->data;
    }
    
    
}