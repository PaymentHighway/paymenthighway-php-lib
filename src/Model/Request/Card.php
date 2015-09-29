<?php namespace Solinor\PaymentHighway\Model\Request;

/**
 * Class Card
 *
 * @package Solinor\PaymentHighway\Model\Request
 */

class Card extends Transaction
{
    public $pan = null;
    public $expiry_year = null;
    public $expiry_month = null;
    public $cvc = null;
    public $verification = null;

    /**
     * @param string $pan
     * @param string $expiry_year
     * @param string $expiry_month
     * @param string $cvc
     * @param string $verification
     */
    public function __construct( $amount, $currency, $pan, $expiry_year, $expiry_month, $cvc = null, $verification = null)
    {
        $this->pan = $pan;
        $this->expiry_year = $expiry_year;
        $this->expiry_month = $expiry_month;
        $this->cvc = $cvc;
        $this->verification = $verification;

        parent::__construct($amount, $currency);
    }

    /**
     * @return string
     */
    public function toJson()
    {
        $card['pan'] = $this->pan;
        $card['expiry_year'] = $this->expiry_year;
        $card['expiry_month'] = $this->expiry_month;

        if($this->cvc !== null)
            $card['cvc'] = $this->cvc;

        if($this->verification !== null)
            $card['verifcation'] = $this->verification;

        return json_encode(
            array(
                'card'     => $card,
                'amount'    => $this->amount,
                'currency'  => $this->currency,
            )
        );
    }
}