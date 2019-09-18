<?php namespace Solinor\PaymentHighway\Model\Sca;

/**
 * Class CustomerDetails
 *
 * @package Solinor\PaymentHighway\Model\Sca
 */

class CustomerDetails extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $shipping_address_matches_billing_address = null;
    public $name = null;
    public $email = null;
    public $home_phone = null;
    public $mobile_phone = null;
    public $work_phone = null;

    /**
     * @param boolean $shipping_address_matches_billing_address Does the shipping address matches the billing address
     * @param string $name Customer name. max length 45
     * @param string $email Customer email. max length 254
     * @param PhoneNumber $home_phone
     * @param PhoneNumber $mobile_phone
     * @param PhoneNumber $work_phone
     */
    public function __construct(
        $shipping_address_matches_billing_address = null,
        $name = null,
        $email = null,
        $home_phone = null,
        $mobile_phone = null,
        $work_phone = null
    ){
        $this->shipping_address_matches_billing_address = $shipping_address_matches_billing_address;
        $this->name = $name;
        $this->email = $email;
        $this->home_phone = $home_phone;
        $this->mobile_phone = $mobile_phone;
        $this->work_phone = $work_phone;
    }
}