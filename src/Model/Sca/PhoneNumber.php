<?php namespace Solinor\PaymentHighway\Model\Sca;

/**
 * Class PhoneNumber
 *
 * @package Solinor\PaymentHighway\Model\Sca
 */

class PhoneNumber extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $country_code = null;
    public $number = null;
    
    /**
     * @param string $country_code 1-3 digits country code (ITU-E.164)
     * @param string $number 1-15 digits phone number 
     */
    public function __construct($country_code, $number){
        $this->country_code = $country_code;
        $this->number = $number;
    }
}