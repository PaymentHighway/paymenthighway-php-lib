<?php namespace Solinor\PaymentHighway;


class FormBuilder {

    const METHOD_POST = "POST";
    const SPH_ACCOUNT = "sph-account";
    const SPH_MERCHANT = "sph-merchant";
    const SPH_AMOUNT = "sph-amount";
    const SPH_CURRENCY = "sph-currency";
    const SPH_ORDER = "sph-order";
    const SPH_SUCCESS_URL = "sph-success-url";
    const SPH_FAILURE_URL = "sph-failure-url";
    const SPH_CANCEL_URL = "sph-cancel-url";
    const SPH_REQUEST_ID = "sph-request-id";
    const SPH_TIMESTAMP = "sph-timestamp";
    const LANGUAGE = "language";
    const DESCRIPTION = "description";
    const SIGNATURE = "signature";

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
    private function createFormParameterArray(){

        $parameterArray = array(
            self::SPH_SUCCESS_URL => $this->successUrl,
            self::SPH_FAILURE_URL => $this->failureUrl,
            self::SPH_CANCEL_URL => $this->cancelUrl,
            self::SPH_ACCOUNT => $this->account,
            self::SPH_MERCHANT => $this->merchant,
            self::SPH_TIMESTAMP => PaymentHighwayUtility::getDate(),
            self::SPH_REQUEST_ID => PaymentHighwayUtility::createRequestId(),
        );

        ksort($parameterArray, SORT_DESC);

        return $parameterArray;
    }

    /**
     * @TODO: not implemented yet!
     */
    private function createSecureSign(){}

}