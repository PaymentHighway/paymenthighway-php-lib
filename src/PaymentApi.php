<?php namespace Solinor\PaymentHighway;

use Httpful\Request;
use Solinor\PaymentHighway\Model\SecureSigner;
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


    /* Custom SPH Headers */
    static $SPH_ACCOUNT = "sph-account";
    static $SPH_MERCHANT = "sph-merchant";
    static $SPH_AMOUNT = "sph-amount";
    static $SPH_CURRENCY = "sph-currency";
    static $SPH_ORDER = "sph-order";
    static $SPH_SUCCESS_URL = "sph-success-url";
    static $SPH_FAILURE_URL = "sph-failure-url";
    static $SPH_CANCEL_URL = "sph-cancel-url";
    static $SPH_REQUEST_ID = "sph-request-id";
    static $SPH_TIMESTAMP = "sph-timestamp";
    static $LANGUAGE = "language";
    static $DESCRIPTION = "description";
    static $SIGNATURE = "signature";

    /* private variables */
    private $serviceUrl = "";
    private $signatureKeyId = null;
    private $signatureSecret = null;
    private $account = null;
    private $merchant = null;

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
    }

    /**
     * Init transaction handle
     *
     * @return \Httpful\Response
     */
    public function initTransaction()
    {
        $headers = $this->createNameValuePairs();
        $uri = '/transaction';

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        $response = Request::post($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->send();

        return $response;
    }

    /**
     * TODO!
     */
    public function commitFormTransaction(){}


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

    /**
     * Create name value pairs
     *
     * @return array
     */
    private function createNameValuePairs() {

        $nameValuePairs = array(
            "sph-account" => $this->account,
            "sph-merchant" => $this->merchant,
            "sph-timestamp" => PaymentHighwayUtility::getDate(),
            "sph-request-id" => PaymentHighwayUtility::createRequestId(),
        );

        return $nameValuePairs;
    }

    /**
     * @param string $uri
     * @param array $sphNameValuePairs
     * @return string formatted signature
     */
    private function createSecureSign($method, $uri, $sphNameValuePairs = array())
    {
        $parsedSphParameters = PaymentHighwayUtility::parseSphParameters($sphNameValuePairs);
        $secureSigner = new SecureSigner($this->signatureKeyId, $this->signatureSecret);

        return $secureSigner->createSignature($method, $uri, $parsedSphParameters);

    }
}