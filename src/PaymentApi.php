<?php namespace Solinor\PaymentHighway;

use Httpful\Request;
use Httpful\Response;
use Solinor\PaymentHighway\Model\SecureSigner;
use Solinor\PaymentHighway\Model\Request\Card;
use Solinor\PaymentHighway\Model\Request\Token;

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
    static $CT_HEADER = "Content-type";
    static $CT_HEADER_INFO = "application/json; charset=utf-8";

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
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function initTransaction()
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction';

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::post($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->send();

        return $response;
    }

    /**
     * @param string $uuid
     * @param string|int $amount
     * @param string $currency
     *
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function commitFormTransaction($uuid, $amount, $currency)
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction/'.$uuid.'/commit';

        ksort($headers);

        $body = array(
            'amount' => $amount,
            'currency' => $currency,
        );

        $jsonBody = json_encode($body);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers, $jsonBody);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::post($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->body($jsonBody)
            ->send();

        return $response;
    }


    /**
     * Charge the credit card
     * @param string|UUID $transactionId
     * @param Card|Token $request
     * @param string|int $amount
     * @param string $currency
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function debitTransaction( $transactionId, $request, $amount, $currency )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction/' . $transactionId . '/debit';

        ksort($headers);

        $body = array(
            'amount' => $amount,
            'currency' => $currency,
        );
        $body += $this->createTransactionRequestBody( $request );

        $jsonBody = json_encode($body);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers, $jsonBody);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::post($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->body($jsonBody)
            ->send();

        return $response;

    }

    /**
     * @param $transactionId
     * @param $request
     * @param $amount
     * @param $currency
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function creditTransaction( $transactionId, $request, $amount, $currency )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction/' . $transactionId . '/credit';

        ksort($headers);

        $body = $this->createTransactionRequestBody( $request );
        $body += array(
            'amount' => $amount,
            'currency' => $currency,
        );

        $jsonBody = json_encode($body);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers, $jsonBody);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::post($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->body($jsonBody)
            ->send();

        return $response;
    }

    /**
     * @param string $transactionId
     * @param string $amount
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function revertTransaction($transactionId, $amount)
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction/' . $transactionId . '/revert';

        ksort($headers);

        $jsonBody = json_encode(array('amount' => $amount));

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers, $jsonBody);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::post($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->body($jsonBody)
            ->send();

        return $response;
    }

    /**
     * @param string $transactionId
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function statusTransaction( $transactionId )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction/' . $transactionId;

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_GET, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::get($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->send();

        return $response;
    }

    /**
     * @param $tokenizeId
     * @return \Httpful\Response
     */
    public function tokenize( $tokenizeId )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/tokenization/' . $tokenizeId;

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_GET, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::get($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->send();

        return $response;
    }

    /**
     * @param string $date date in format yyyyMMdd
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getReport( $date )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/report/batch/' . $date;

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_GET, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        $response = Request::get($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->send();

        return $response;
    }

    /**
     * Create name value pairs
     *
     * @return array
     */
    private function createHeaderNameValuePairs() {

        $nameValuePairs = array(
            "sph-account" => $this->account,
            "sph-merchant" => $this->merchant,
            "sph-timestamp" => PaymentHighwayUtility::getDate(),
            "sph-request-id" => PaymentHighwayUtility::createRequestId(),
        );

        return $nameValuePairs;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $sphNameValuePairs
     * @param string $body
     * @return string formatted signature
     */
    private function createSecureSign($method, $uri, array $sphNameValuePairs = array(), $body = "")
    {
        $parsedSphParameters = PaymentHighwayUtility::parseSphParameters($sphNameValuePairs);
        $secureSigner = new SecureSigner($this->signatureKeyId, $this->signatureSecret);

        return $secureSigner->createSignature($method, $uri, $parsedSphParameters, $body);

    }

    /**
     * @param Card|Token $request
     * @return array|mixed
     */
    private function createTransactionRequestBody( $request )
    {
        if($request instanceof Card)
        {
            return array('card' => $request);
        }
        if( $request instanceof Token)
        {
            return array('token' => $request);
        }
    }
}