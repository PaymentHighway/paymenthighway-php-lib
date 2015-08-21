<?php namespace Solinor\PaymentHighway;


use Solinor\PaymentHighway\Model\SecureSigner;

class FormBuilder {

    static $METHOD_POST = "POST";
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

    private $method = self::METHOD_POST;
    private $baseUrl = null;
    private $signatureKeyId = null;
    private $signatureSecret = null;
    private $account = null;
    private $merchant = null;
    private $successUrl = null;
    private $failureUrl = null;
    private $cancelUrl = null;
    private $language = null;

    /**
     * @param string $method
     * @param string $signatureKeyId
     * @param string $signatureSecret
     * @param string $account
     * @param string $merchant
     * @param string $baseUrl
     * @param string $successUrl
     * @param string $failureUrl
     * @param string $cancelUrl
     * @param string $language
     */
    public function __construct( $method, $signatureKeyId, $signatureSecret, $account,
                                 $merchant, $baseUrl, $successUrl, $failureUrl,
                                 $cancelUrl, $language)
    {
        $this->method = $method;
        $this->signatureKeyId = $signatureKeyId;
        $this->signatureSecret = $signatureSecret;
        $this->account = $account;
        $this->merchant = $merchant;
        $this->baseUrl = $baseUrl;
        $this->successUrl = $successUrl;
        $this->failureUrl = $failureUrl;
        $this->cancelUrl = $cancelUrl;
        $this->language = $language;
    }

    /**
     * TODO: not implemented yet!
     */
    public function generateAddCardParameters(){}

    /**
     * TODO: not implemented yet!
     */
    public function generatePaymentParameters(){}

    /**
     * TODO: not implemented yet!
     */
    public function generateAddCardAndPaymentParameters(){}

    /**
     * @return array
     */
    private function createFormParameterArray()
    {
        $parameterArray = array(
            self::$SPH_SUCCESS_URL => $this->successUrl,
            self::$SPH_FAILURE_URL => $this->failureUrl,
            self::$SPH_CANCEL_URL => $this->cancelUrl,
            self::$SPH_ACCOUNT => $this->account,
            self::$SPH_MERCHANT => $this->merchant,
            self::$SPH_TIMESTAMP => PaymentHighwayUtility::getDate(),
            self::$SPH_REQUEST_ID => PaymentHighwayUtility::createRequestId(),
        );

        ksort($parameterArray, SORT_DESC);

        return $parameterArray;
    }

    /**
     * @TODO: not implemented yet!
     * @param string $uri
     * @param array  $sphNameValuePairs
     */
    private function createSecureSign($uri, $sphNameValuePairs = array())
    {
        $parsedSphParameters = PaymentHighwayUtility::parseSphParameters($sphNameValuePairs);
        $secureSigner = new SecureSigner($this->signatureKeyId, $this->signatureSecret);

    }

}