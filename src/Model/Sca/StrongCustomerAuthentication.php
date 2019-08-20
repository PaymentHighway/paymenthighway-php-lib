<?php namespace Solinor\PaymentHighway\Model\Sca;

/**
 * Class StrongCustomerAuthentication
 *
 * @package Solinor\PaymentHighway\Model\Sca
 */

class StrongCustomerAuthentication extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $return_urls = null;
    public $customer_details = null;
    public $customer_account = null;
    public $purchase = null;
    public $billing_address = null;
    public $shipping_address = null;
    public $desired_challenge_window_size = null;
    public $exit_iframe_on_result = null;
    public $exit_iframe_on_three_d_secure = null;

    /**
     * @param ReturnUrls $return_urls
     * @param CustomerDetails $customer_details
     * @param CustomerAccount $customer_account
     * @param Purchase $purchase
     * @param Address $billing_address
     * @param Address $shipping_address
     * @param some $desired_challenge_window_size
     * @param some $exit_iframe_on_result
     * @param some $exit_iframe_on_three_d_secure
     */
    public function __construct(
        $return_urls,
        $customer_details = null,
        $customer_account = null,
        $purchase = null,
        $billing_address = null,
        $shipping_address = null,
        $desired_challenge_window_size = null,
        $exit_iframe_on_result = null,
        $exit_iframe_on_three_d_secure = null
    ) {
        $this->return_urls = $return_urls;
        $this->customer_details = $customer_details;
        $this->customer_account = $customer_account;
        $this->purchase = $purchase;
        $this->billing_address = $billing_address;
        $this->shipping_address = $shipping_address;
        $this->desired_challenge_window_size = $desired_challenge_window_size;
        $this->exit_iframe_on_result = $exit_iframe_on_result;
        $this->exit_iframe_on_three_d_secure = $exit_iframe_on_three_d_secure ;
    }


}
