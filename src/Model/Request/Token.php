<?php namespace Solinor\PaymentHighway\Model\Request;


/**
 * Class Token
 *
 * @package Solinor\PaymentHighway\Model\Request
 */

class Token extends Transaction
{
    public $id = null;
    public $cvc = null;

    /**
     * @param int $amount
     * @param string $currency
     * @param string $id
     * @param string $cvc
     */
    public function __construct( $amount, $currency, $id, $cvc = null)
    {
        $this->id = $id;
        $this->cvc = $cvc;

        parent::__construct($amount, $currency);
    }

    /**
     * @return string
     */
    public function toJson()
    {
        $token['id'] = $this->id;
        if($this->cvc !== null) $token['cvc'] = $this->cvc;

        return json_encode(
            array(
                'token'     => $token,
                'amount'    => $this->amount,
                'currency'  => $this->currency,
            )
        );
    }
}