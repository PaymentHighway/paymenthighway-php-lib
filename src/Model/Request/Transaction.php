<?php namespace Solinor\PaymentHighway\Model\Request;

use Solinor\PaymentHighway\Model\Contracts\Request;

/**
 * Class Transaction
 * @package Solinor\PaymentHighway\Model\Request
 */

class Transaction implements Request
{
    public $amount = null;
    public $currency = null;

    public function __construct( $amount, $currency )
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function toJson()
    {
        return json_decode(
            get_object_vars($this)
        );
    }
}