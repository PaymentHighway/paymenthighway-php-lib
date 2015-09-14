<?php namespace Solinor\PaymentHighway;

use Httpful\associative;
use Httpful\Request;
use Httpful\Response;
use Rhumsaa\Uuid\Console\Exception;
use Solinor\PaymentHighway\Model\SecureSigner;

/**
 * Class DangerZone
 *
 * @package Solinor\PaymentHighway
 */

class DangerZone
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

    private $pciDssEnabled = false;

    private function getUriForTokenInit() {
        return "/tokenization";
    }

    private function getUriForTokenize($tokenizationId) {
        return "/tokenization/$tokenizationId/tokenize";
    }

    private function getUriForToken($tokenizationId) {
        return "/tokenization/$tokenizationId";
    }

    public function enablePciDss() {
        $this->pciDssEnabled = true;
    }

    private function throwExceptionIfPciDssDisabled()
    {
        if($this->pciDssEnabled !== true)
        {
            throw new \Exception("You must enabled pci dss manually if you want to use manual card adding.");
        }
    }

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
     * Init manual card tokenization
     *
     * @return \Httpful\Response
     */
    public function tokenInit()
    {
        $this->throwExceptionIfPciDssDisabled();

        $headers = $this->createNameValuePairs();
        $uri = $this->getUriForTokenInit();

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        $response = Request::post($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->send();

        return $this->getTokenizationIdOrThrow($response);
    }

    private function getTokenizationIdOrThrow(Response $response)
    {
        try {
            if($response->body->result->code === 100 && $response->body->result->message ==='OK')
            {
                return $response->body->id;
            }
        } catch (\Exception $e) {}

        throw new \Exception("getTokenizationOrThrow: could not get tokenizationId: " . print_r($response, true));
    }

    /**
     * Manual card tokenization
     *
     * @return \Httpful\Response
     */
    public function tokenize($tokenizationId, $expiryMonth, $expiryYear, $cvc, $pan){
        $this->throwExceptionIfPciDssDisabled();

        $headers = $this->createNameValuePairs();
        $uri = $this->getUriForTokenize($tokenizationId);

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        $response = Request::post($this->serviceUrl . $uri)
            ->body(json_encode(array('expiry_month' => $expiryMonth, 'expiry_year' => $expiryYear, 'cvc' => $cvc, 'pan' => $pan)))
            ->addHeaders($headers)
            ->send();

        return $this->returnTrueIfTokenizationOkOrThrow($response);
    }


    /**
     * Manual card get token
     */
    public function getToken(){}

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