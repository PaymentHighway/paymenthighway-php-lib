<?php namespace Solinor\PaymentHighway;

use \Curl\Curl;

/**
 * Class PaymentApi
 *
 * @package Solinor\PaymentHighway
 */

class PaymentApi
{
    /* Payment API headers */
    static $USER_AGENT = "PaymentHighway Php Library";
    static $METHOD_POST = "POST";
    static $METHOD_GET = "GET";

    private $serviceUrl = "";
    private $signatureKeyId = null;
    private $signatureSecret = null;
    private $account = null;
    private $merchant = null;

    /**
     * @param \Curl\Curl
     */
    private $httpClient = null;

    /**
     * Constructor
     *
     * @param string $serviceUrl
     * @param string $account
     * @param string $merchant
     * @param string $signatureKeyId
     * @param string $signatureSecret
     */
    public function __construct( $serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant) 
    {
        $this->serviceUrl = $serviceUrl;
        $this->signatureKeyId = $signatureKeyId;
        $this->signatureSecret = $signatureSecret;
        $this->account = $account;
        $this->merchant = $merchant;

        $this->httpClient = new Curl();
    }

    /**
     * TODO!
     */
    public function commitTransaction(){}

    /**
     * TODO!
     */
    public function initTransaction(){}

    /**
     * TODO!
     */
    public function debitTransaction(){}

    /**
     * TODO!
     */
    public function creditTransaction(){}

    /**
     * TODO!
     */
    public function revertTransaction(){}

    /**
     * TODO!
     */
    public function statusTransaction(){}

    /**
     * TODO!
     */
    public function tokenize(){}

    /**
     * TODO!
     */
    public function getReport(){}
}