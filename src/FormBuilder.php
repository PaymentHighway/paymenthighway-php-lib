<?php namespace Solinor\PaymentHighway;


use Solinor\PaymentHighway\Model\Form;
use Solinor\PaymentHighway\Security\SecureSigner;

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
    static $SPH_TOKEN = "sph-token";
    static $SPH_ACCEPT_CVC_REQUIRED = "sph-accept-cvc-required";
    static $SPH_SKIP_FORM_NOTIFICATIONS = 'sph-skip-form-notifications';
    static $SPH_EXIT_IFRAME_ON_RESULT = 'sph-exit-iframe-on-result';
    static $SPH_EXIT_IFRAME_ON_THREE_D_SECURE = 'sph-exit-iframe-on-three-d-secure';
    static $SPH_USE_THREE_D_SECURE = 'sph-use-three-d-secure';
    static $LANGUAGE = "language";
    static $DESCRIPTION = "description";
    static $SIGNATURE = "signature";

    static $ADD_CARD_URI = "/form/view/add_card";
    static $PAYMENT_URI = "/form/view/pay_with_card";
    static $ADD_AND_PAY_URI = "/form/view/add_and_pay_with_card";
    static $CVC_AND_TOKEN_URI = "/form/view/pay_with_token_and_cvc";
    static $MOBILE_PAY_URI = "/form/view/mobilepay";


    private $method = 'POST';
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
     * Get parameters for add card request
     *
     * @param bool $acceptCvcRequired
     * @param bool $skipFormNotifications
     * @param bool $exitIframeOnResult
     * @param bool $exitIframeOn3ds
     * @param bool $use3ds
     * @return Form
     */
    public function generateAddCardParameters( $acceptCvcRequired = null, $skipFormNotifications = null,
                                               $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null )
    {
        $commonParameters = $this->createFormParameterArray();

        if(!is_null($acceptCvcRequired))
            $commonParameters[self::$SPH_ACCEPT_CVC_REQUIRED] = $acceptCvcRequired;
        if(!is_null($skipFormNotifications))
            $commonParameters[self::$SPH_SKIP_FORM_NOTIFICATIONS] = $skipFormNotifications;
        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($exitIframeOn3ds))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_THREE_D_SECURE] = $exitIframeOn3ds;
        if(!is_null($use3ds))
            $commonParameters[self::$SPH_USE_THREE_D_SECURE] = $use3ds;

        ksort($commonParameters, SORT_DESC);

        $signature = $this->createSecureSign(self::$ADD_CARD_URI, $commonParameters);

        $commonParameters[self::$LANGUAGE] = $this->language;
        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$ADD_CARD_URI, $commonParameters);
    }

    /**
     * Get parameters for Payment request.
     *
     * @param string $amount
     * @param string $currency
     * @param string $orderId
     * @param string $description
     * @param bool $skipFormNotifications
     * @param bool $exitIframeOnResult
     * @param bool $exitIframeOn3ds
     * @param bool $use3ds
     * @return Form
     */
    public function generatePaymentParameters($amount, $currency, $orderId, $description, $skipFormNotifications = null,
                                              $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null )
    {
        $commonParameters = $this->createFormParameterArray();

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = $currency;
        $commonParameters[self::$SPH_ORDER] = $orderId;

        if(!is_null($skipFormNotifications))
            $commonParameters[self::$SPH_SKIP_FORM_NOTIFICATIONS] = $skipFormNotifications;
        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($exitIframeOn3ds))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_THREE_D_SECURE] = $exitIframeOn3ds;
        if(!is_null($use3ds))
            $commonParameters[self::$SPH_USE_THREE_D_SECURE] = $use3ds;


        ksort($commonParameters, SORT_DESC);

        $signature = $this->createSecureSign(self::$PAYMENT_URI, $commonParameters);

        $commonParameters[self::$DESCRIPTION] = $description;
        $commonParameters[self::$LANGUAGE] = $this->language;
        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$PAYMENT_URI, $commonParameters);

    }

    /**
     * Get parameters for adding and paying with card request.
     *
     * @param string $amount
     * @param string $currency
     * @param string $orderId
     * @param string $description
     * @param bool $skipFormNotifications
     * @param bool $exitIframeOnResult
     * @param bool $exitIframeOn3ds
     * @param bool $use3ds
     * @return Form
     */
    public function generateAddCardAndPaymentParameters($amount, $currency, $orderId, $description, $skipFormNotifications = null,
                                                        $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null )
    {
        if(!is_null($skipFormNotifications))
            $commonParameters[self::$SPH_SKIP_FORM_NOTIFICATIONS] = $skipFormNotifications;
        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($exitIframeOn3ds))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_THREE_D_SECURE] = $exitIframeOn3ds;
        if(!is_null($use3ds))
            $commonParameters[self::$SPH_USE_THREE_D_SECURE] = $use3ds;

        $commonParameters = $this->createFormParameterArray();

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = $currency;
        $commonParameters[self::$SPH_ORDER] = $orderId;

        ksort($commonParameters, SORT_DESC);

        $signature = $this->createSecureSign(self::$ADD_AND_PAY_URI, $commonParameters);

        $commonParameters[self::$DESCRIPTION] = $description;
        $commonParameters[self::$LANGUAGE] = $this->language;
        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$ADD_AND_PAY_URI, $commonParameters);
    }

    /**
     * @param string|UUID $tokenId
     * @param int $amount
     * @param string $currency
     * @param string $orderId
     * @param string $description
     * @param bool $skipFormNotifications
     * @param bool $exitIframeOnResult
     * @param bool $exitIframeOn3ds
     * @param bool $use3ds
     * @return Form
     */
    public function generatePayWithTokenAndCvcParameters( $tokenId, $amount, $currency, $orderId, $description, $skipFormNotifications = null,
                                                          $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null )
    {
        if(!is_null($skipFormNotifications))
            $commonParameters[self::$SPH_SKIP_FORM_NOTIFICATIONS] = $skipFormNotifications;
        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($exitIframeOn3ds))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_THREE_D_SECURE] = $exitIframeOn3ds;
        if(!is_null($use3ds))
            $commonParameters[self::$SPH_USE_THREE_D_SECURE] = $use3ds;

        $commonParameters = $this->createFormParameterArray();

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = $currency;
        $commonParameters[self::$SPH_ORDER] = $orderId;
        $commonParameters[self::$SPH_TOKEN] = $tokenId;
        $commonParameters[self::$DESCRIPTION] = $description;

        ksort($commonParameters,SORT_DESC);

        $signature = $this->createSecureSign(self::$CVC_AND_TOKEN_URI,$commonParameters);

        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$CVC_AND_TOKEN_URI, $commonParameters);
    }

    /**
     * @param int $amount
     * @param string $currency
     * @param string $orderId
     * @param string $description
     * @param bool $exitIframeOnResult
     * @return Form
     */
    public function generatePayWithMobilePayParameters($amount, $currency, $orderId, $description,
                                                       $exitIframeOnResult = null)
    {
        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;

        $commonParameters = $this->createFormParameterArray();

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = $currency;
        $commonParameters[self::$SPH_ORDER] = $orderId;

        $commonParameters[self::$DESCRIPTION] = $description;

        ksort($commonParameters,SORT_DESC);

        $signature = $this->createSecureSign(self::$MOBILE_PAY_URI,$commonParameters);

        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$MOBILE_PAY_URI, $commonParameters);
    }

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

        return $parameterArray;
    }

    /**
     * @param string $uri
     * @param array $sphNameValuePairs
     * @return string formatted signature
     */
    private function createSecureSign($uri, $sphNameValuePairs = array())
    {
        $parsedSphParameters = PaymentHighwayUtility::parseSphParameters($sphNameValuePairs);
        $secureSigner = new SecureSigner($this->signatureKeyId, $this->signatureSecret);

        return $secureSigner->createSignature($this->method, $uri, $parsedSphParameters);

    }

}