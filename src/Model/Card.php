<?php namespace Solinor\PaymentHighway\Model;

/**
 * Class Card
 *
 * @package Solinor\PaymentHighway\Model
 */

class Card implements \JsonSerializable
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $data = get_object_vars($this);

        foreach($data as $key => $val)
            if($val === null)
                unset($data[$key]);

        return $data;
    }
}