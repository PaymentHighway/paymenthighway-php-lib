<?php namespace Solinor\PaymentHighway\Model;

/**
 * Class Card
 *
 * @package Solinor\PaymentHighway\Model
 */

class Card extends JsonSerializable
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
    public function __construct( $pan, $expiry_year, $expiry_month, $cvc = null, $verification = null)
    {
        $this->pan = $pan;
        $this->expiry_year = $expiry_year;
        $this->expiry_month = $expiry_month;
        $this->cvc = $cvc;
        $this->verification = $verification;
    }
}
