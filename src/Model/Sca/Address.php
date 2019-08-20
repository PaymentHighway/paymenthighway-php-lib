<?php namespace Solinor\PaymentHighway\Model\Sca;

/**
 * Class Address
 *
 * @package Solinor\PaymentHighway\Model\Sca
 */

class Address extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $city = null;
    public $country = null;
    public $address_line_1 = null;
    public $address_line_2 = null;
    public $address_line_3 = null;
    public $post_code = null;
    public $state = null;
    
    /**
     * @param string $city max length 50, City name
     * @param string $country 3 digits country code, 3166-1 numeric (eg. "246")
     * @param string $address_line_1 max length 50, Address line 1
     * @param string $address_line_2 max length 50, Address line 2
     * @param string $address_line_3 max length 50, Address line 3
     * @param string $post_code max length 16, Zip code
     * @param string $state String length 2, ISO 3166-2 country subdivision code (eg. "18")
     */
    public function __construct(
        $city = null,
        $country = null,
        $address_line_1 = null,
        $address_line_2 = null,
        $address_line_3 = null,
        $post_code = null,
        $state = null
    ){
        $this->city = $city;
        $this->country = $country;
        $this->address_line_1 = $address_line_1;
        $this->address_line_2 = $address_line_2;
        $this->address_line_3 = $address_line_3;
        $this->post_code = $post_code;
        $this->state = $state;
    }
}