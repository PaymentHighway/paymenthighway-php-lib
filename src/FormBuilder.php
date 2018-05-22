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
    static $SPH_SHOP_LOGO_URL = 'sph-shop-logo-url';
    static $SPH_MOBILEPAY_PHONE_NUMBER = 'sph-mobilepay-phone-number';
    static $SPH_MOBILEPAY_SHOP_NAME = 'sph-mobilepay-shop-name';
    static $SPH_SUB_MERCHANT_NAME = 'sph-sub-merchant-name';
    static $SPH_SUB_MERCHANT_ID = 'sph-sub-merchant-id';
    static $LANGUAGE = "language";
    static $DESCRIPTION = "description";
    static $SIGNATURE = "signature";
    static $SPH_WEBHOOK_SUCCESS_URL = "sph-webhook-success-url";
    static $SPH_WEBHOOK_FAILURE_URL = "sph-webhook-failure-url";
    static $SPH_WEBHOOK_CANCEL_URL = "sph-webhook-cancel-url";
    static $SPH_WEBHOOK_DELAY = "sph-webhook-delay";
    static $SPH_SHOW_PAYMENT_METHOD_SELECTOR = "sph-show-payment-method-selector";
    static $SPH_PHONE_NUMBER = "sph-phone-number";
    static $SPH_REFERENCE_NUMBER = "sph-reference-number";
    static $SPH_APP_URL = "sph-app-url";


    static $ADD_CARD_URI = "/form/view/add_card";
    static $PAYMENT_URI = "/form/view/pay_with_card";
    static $ADD_AND_PAY_URI = "/form/view/add_and_pay_with_card";
    static $CVC_AND_TOKEN_URI = "/form/view/pay_with_token_and_cvc";
    static $MOBILE_PAY_URI = "/form/view/mobilepay";
    static $MASTERPASS_PAY_URI = "/form/view/masterpass";
    static $SIIRTO_PAY_URI = "/form/view/siirto";


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
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param int $webhookDelay             Delay for webhook in seconds. Between 0-900
     * @return Form
     */
    public function generateAddCardParameters( $acceptCvcRequired = null, $skipFormNotifications = null,
                                               $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null,
                                               $webhookSuccessUrl = null, $webhookFailureUrl = null, $webhookCancelUrl = null,
                                               $webhookDelay = null)
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

        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        ksort($commonParameters, SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

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
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param int $webhookDelay             Delay for webhook in seconds. Between 0-900
     * @param bool $showPaymentMethodSelector
     * @return Form
     */
    public function generatePaymentParameters($amount, $currency, $orderId, $description, $skipFormNotifications = null,
                                              $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null,
                                              $webhookSuccessUrl = null, $webhookFailureUrl = null, $webhookCancelUrl = null,
                                              $webhookDelay = null, $showPaymentMethodSelector = null)

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
        if(!is_null($showPaymentMethodSelector))
            $commonParameters[self::$SPH_SHOW_PAYMENT_METHOD_SELECTOR] = $showPaymentMethodSelector;

        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        ksort($commonParameters, SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

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
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param int $webhookDelay             Delay for webhook in seconds. Between 0-900
     * @return Form
     */
    public function generateAddCardAndPaymentParameters($amount, $currency, $orderId, $description, $skipFormNotifications = null,
                                                        $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null,
                                                        $webhookSuccessUrl = null, $webhookFailureUrl = null, $webhookCancelUrl = null,
                                                        $webhookDelay = null)
    {
        $commonParameters = $this->createFormParameterArray();

        if(!is_null($skipFormNotifications))
            $commonParameters[self::$SPH_SKIP_FORM_NOTIFICATIONS] = $skipFormNotifications;
        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($exitIframeOn3ds))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_THREE_D_SECURE] = $exitIframeOn3ds;
        if(!is_null($use3ds))
            $commonParameters[self::$SPH_USE_THREE_D_SECURE] = $use3ds;

        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = $currency;
        $commonParameters[self::$SPH_ORDER] = $orderId;

        ksort($commonParameters, SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

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
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param int $webhookDelay             Delay for webhook in seconds. Between 0-900
     * @return Form
     */
    public function generatePayWithTokenAndCvcParameters( $tokenId, $amount, $currency, $orderId, $description, $skipFormNotifications = null,
                                                          $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null,
                                                          $webhookSuccessUrl = null, $webhookFailureUrl = null, $webhookCancelUrl = null,
                                                          $webhookDelay = null)
    {
        $commonParameters = $this->createFormParameterArray();

        if(!is_null($skipFormNotifications))
            $commonParameters[self::$SPH_SKIP_FORM_NOTIFICATIONS] = $skipFormNotifications;
        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($exitIframeOn3ds))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_THREE_D_SECURE] = $exitIframeOn3ds;
        if(!is_null($use3ds))
            $commonParameters[self::$SPH_USE_THREE_D_SECURE] = $use3ds;

        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = $currency;
        $commonParameters[self::$SPH_ORDER] = $orderId;
        $commonParameters[self::$SPH_TOKEN] = $tokenId;
        $commonParameters[self::$DESCRIPTION] = $description;

        ksort($commonParameters,SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

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
     * @param string $shopLogoUrl The logo must be 250x250 pixel in .png format and must be hosted on a HTTPS (secure) server. Optional.
     * @param string $phoneNumber User phone number with country code. Max AN 15. Optional.
     * @param string $shopName Max 100 AN. Name of the shop/merchant. MobilePay app displays this under the shop logo.  If omitted, the merchant name from PH is used. Optional.
     * @param string $subMerchantId Max 15 AN. Should only be used by a Payment Facilitator customer
     * @param string $subMerchantName Max 21 AN. Should only be used by a Payment Facilitator customer
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param int $webhookDelay             Delay for webhook in seconds. Between 0-900
     * @return Form
     */
    public function generatePayWithMobilePayParameters($amount, $currency, $orderId, $description,
                                                       $exitIframeOnResult = null, $shopLogoUrl = null, $phoneNumber = null,
                                                       $shopName = null , $subMerchantId = null, $subMerchantName = null,
                                                       $webhookSuccessUrl = null, $webhookFailureUrl = null, $webhookCancelUrl = null,
                                                       $webhookDelay = null)
    {
        $commonParameters = $this->createFormParameterArray();

        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;

        if(!is_null($shopLogoUrl))
            $commonParameters[self::$SPH_SHOP_LOGO_URL] = $shopLogoUrl;

        if(!is_null($phoneNumber))
            $commonParameters[self::$SPH_MOBILEPAY_PHONE_NUMBER] = $phoneNumber;

        if(!is_null($shopName))
            $commonParameters[self::$SPH_MOBILEPAY_SHOP_NAME] = $shopName;

        if(!is_null($subMerchantId))
            $commonParameters[self::$SPH_SUB_MERCHANT_ID] = $subMerchantId;

        if(!is_null($subMerchantName))
            $commonParameters[self::$SPH_SUB_MERCHANT_NAME] = $subMerchantName;

        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = $currency;
        $commonParameters[self::$SPH_ORDER] = $orderId;

        $commonParameters[self::$DESCRIPTION] = $description;

        ksort($commonParameters,SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

        $signature = $this->createSecureSign(self::$MOBILE_PAY_URI,$commonParameters);

        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$MOBILE_PAY_URI, $commonParameters);
    }

    /**
     * Get parameters for Masterpass payment request.
     *
     * @param string $amount
     * @param string $currency
     * @param string $orderId
     * @param string $description
     * @param bool $skipFormNotifications
     * @param bool $exitIframeOnResult
     * @param bool $exitIframeOn3ds
     * @param bool $use3ds
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param int $webhookDelay             Delay for webhook in seconds. Between 0-900
     * @return Form
     */
    public function generateMasterpassParameters($amount, $currency, $orderId, $description, $skipFormNotifications = null,
                                                 $exitIframeOnResult = null, $exitIframeOn3ds = null, $use3ds = null,
                                                 $webhookSuccessUrl = null, $webhookFailureUrl = null, $webhookCancelUrl = null,
                                                 $webhookDelay = null)
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

        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        ksort($commonParameters, SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

        $signature = $this->createSecureSign(self::$MASTERPASS_PAY_URI, $commonParameters);

        $commonParameters[self::$DESCRIPTION] = $description;
        $commonParameters[self::$LANGUAGE] = $this->language;
        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$MASTERPASS_PAY_URI, $commonParameters);

    }

    /**
     * Get parameters for Siirto request.
     *
     * @param string $amount                The amount to pay in euro cents. Siirto supports only euros.
     * @param string $orderId               A generated order ID, may for example be always unique or used multiple times for recurring transactions.
     * @param string $description           Description of the payment shown in the form.
     * @param string $referenceNumber       Reference number
     * @param string $phoneNumber           User phone number with country code. Max AN 15. Optional
     * @param bool $exitIframeOnResult
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param string $webhookDelay          Delay for webhook in seconds. Between 0-900
     * @return Form
     */
    public function generateSiirtoParameters($amount, $orderId, $description, $referenceNumber, $phoneNumber = null,
                                             $exitIframeOnResult = null, $webhookSuccessUrl = null, $webhookFailureUrl = null,
                                             $webhookCancelUrl = null, $webhookDelay = null)
    {
        $commonParameters = $this->createFormParameterArray();

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = "EUR";
        $commonParameters[self::$SPH_ORDER] = $orderId;
        $commonParameters[self::$SPH_REFERENCE_NUMBER] = $referenceNumber;

        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($phoneNumber))
            $commonParameters[self::$SPH_PHONE_NUMBER] = $phoneNumber;

        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        ksort($commonParameters, SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

        $signature = $this->createSecureSign(self::$SIIRTO_PAY_URI, $commonParameters);

        $commonParameters[self::$DESCRIPTION] = $description;
        $commonParameters[self::$LANGUAGE] = $this->language;
        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$SIIRTO_PAY_URI, $commonParameters);
    }

    /**
     * Get parameters for Pivo request.
     *
     * @param string $amount                The amount to pay in euro cents. Pivo supports only euros.
     * @param string $orderId               A generated order ID, may for example be always unique or used multiple times for recurring transactions.
     * @param string $description           Description of the payment shown in the form.
     * @param string $referenceNumber       Reference number
     * @param string $phoneNumber           User phone number with country code. Max AN 15. Optional
     * @param string $appUrl                When used, Pivo tries to open application with this url. Optional.
     * @param bool $exitIframeOnResult
     * @param string $webhookSuccessUrl     The URL the PH server makes request after the transaction is handled. The payment itself may still be rejected.
     * @param string $webhookFailureUrl     The URL the PH server makes request after a failure such as an authentication or connectivity error.
     * @param string $webhookCancelUrl      The URL the PH server makes request after cancelling the transaction (clicking on the cancel button).
     * @param string $webhookDelay          Delay for webhook in seconds. Between 0-900
     * @return Form
     */
    public function generatePivoParameters($amount, $orderId, $description, $referenceNumber = null,  $phoneNumber = null, $appUrl = null,
                                           $exitIframeOnResult = null, $webhookSuccessUrl = null, $webhookFailureUrl = null,
                                           $webhookCancelUrl = null, $webhookDelay = null)
    {
        $commonParameters = $this->createFormParameterArray();

        $commonParameters[self::$SPH_AMOUNT] = $amount;
        $commonParameters[self::$SPH_CURRENCY] = "EUR";
        $commonParameters[self::$SPH_ORDER] = $orderId;

        if(!is_null($exitIframeOnResult))
            $commonParameters[self::$SPH_EXIT_IFRAME_ON_RESULT] = $exitIframeOnResult;
        if(!is_null($phoneNumber))
            $commonParameters[self::$SPH_PHONE_NUMBER] = $phoneNumber;
        if(!is_null($referenceNumber))
            $commonParameters[self::$SPH_REFERENCE_NUMBER] = $referenceNumber;
        if(!is_null($appUrl))
            $commonParameters[self::$SPH_APP_URL] = $appUrl;
        $commonParameters = array_merge($commonParameters,
            $this->createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay));

        ksort($commonParameters, SORT_DESC);

        $commonParameters = $this->booleans2Text($commonParameters);

        $signature = $this->createSecureSign(self::$SIIRTO_PAY_URI, $commonParameters);

        $commonParameters[self::$DESCRIPTION] = $description;
        $commonParameters[self::$LANGUAGE] = $this->language;
        $commonParameters[self::$SIGNATURE] = $signature;

        return new Form($this->method, $this->baseUrl, self::$SIIRTO_PAY_URI, $commonParameters);
    }

    /**
     * @param $webhookSuccessUrl
     * @param $webhookFailureUrl
     * @param $webhookCancelUrl
     * @param $webhookDelay
     * @return array
     */
    private function createWebhookParametersArray($webhookSuccessUrl, $webhookFailureUrl, $webhookCancelUrl, $webhookDelay) {
        $parameters = [];
        if(!is_null($webhookSuccessUrl))
            $parameters[self::$SPH_WEBHOOK_SUCCESS_URL] = $webhookSuccessUrl;
        if(!is_null($webhookFailureUrl))
            $parameters[self::$SPH_WEBHOOK_FAILURE_URL] = $webhookFailureUrl;
        if(!is_null($webhookCancelUrl))
            $parameters[self::$SPH_WEBHOOK_CANCEL_URL] = $webhookCancelUrl;
        if(!is_null($webhookDelay))
            $parameters[self::$SPH_WEBHOOK_DELAY] = $webhookDelay;

        return $parameters;
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
     * @param array $parameters
     * @return array
     */
    private function booleans2Text(array $parameters) {
        $translatedArray = array();
        foreach( $parameters as $key => $value) {
            $translatedArray[$key] = is_bool($value) ? ($value ? "true" : "false") : $value;
        }
        return $translatedArray;
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