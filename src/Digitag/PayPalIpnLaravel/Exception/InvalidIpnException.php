<?php

namespace Digitag\PayPalIpnLaravel\Exception;

class InvalidIpnException extends \Exception
{
    /**
     * PayPal IPN reponse.
     *
     * @var array
     */
    protected $data = [];

    /**
     * PayPal Report.
     *
     * @var string
     */
    protected $report = null;

    public function __construct($message, $response = null, $report = null)
    {
        $this->data = $response;
        $this->report = $report;
        parent::__construct($message, 0, null);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getReport()
    {
        return $this->report;
    }
}
