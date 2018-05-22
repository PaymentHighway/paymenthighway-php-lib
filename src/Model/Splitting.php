<?php namespace Solinor\PaymentHighway\Model;

/**
 * Class Splitting
 * Splits the payment into sub-merchant settlement and the main merchant commission. Requires separate activation.
 * @package Solinor\PaymentHighway\Model
 */
class Splitting extends JsonSerializable
{
    public $merchant_id = null;
    public $amount = null;

    /**
     * @param string $merchant_id Sub-merchant ID from the settlements provider. Not to be confused with the sph-merchant value.
     * @param int $amount The amount settled to the sub-merchant's account. The rest will be considered as the main merchant's commission. In the smallest currency unit. E.g. 99.99 € = 9999.
     */
    public function __construct($merchant_id, $amount)
    {
        $this->merchant_id = $merchant_id;
        $this->amount = $amount;
    }
}
