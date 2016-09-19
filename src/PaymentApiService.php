<?php namespace Solinor\PaymentHighway;

use Httpful\Request;
use Httpful\Response;
use Solinor\PaymentHighway\Model\Request\Transaction;
use Solinor\PaymentHighway\Security\SecureSigner;
use Respect\Validation\Validator;

/**
 * Class PaymentApiService
 *
 * @package Solinor\PaymentHighway
 */

class PaymentApiService
{
    /* Payment API headers */
    static $USER_AGENT = "PaymentHighway Php Library";
    static $METHOD_POST = "POST";
    static $METHOD_GET = "GET";
    static $CT_HEADER = "Content-type";
    static $CT_HEADER_INFO = "application/json; charset=utf-8";
    static $API_VERSION_INFO = "";

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
    static $SPH_API_VERSION = "sph-api-version";
    static $LANGUAGE = "language";
    static $DESCRIPTION = "description";
    static $SIGNATURE = "signature";

    /* private variables */
    private $serviceUrl = "";
    private $signatureKeyId = null;
    private $signatureSecret = null;
    private $account = null;
    private $merchant = null;
    private $apiversion = "";

    /**
     * Constructor
     *
     * @param string $serviceUrl
     * @param string $signatureKeyId
     * @param string $signatureSecret
     * @param string $account
     * @param string $merchant
     * @param string $apiversion
     */
    public function __construct( $serviceUrl,  $signatureKeyId,  $signatureSecret,  $account,  $merchant, $apiversion = "20150605")
    {
        $this->serviceUrl = $serviceUrl;
        $this->signatureKeyId = $signatureKeyId;
        $this->signatureSecret = $signatureSecret;
        $this->account = $account;
        $this->merchant = $merchant;
        $this->apiversion = $apiversion;
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
     * @param string|UUID $transactionId
     * @param Transaction $transaction
     * @return Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function commitFormTransaction( $transactionId, Transaction $transaction )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction/'. $transactionId .'/commit';

        ksort($headers);

        $jsonBody = json_encode($transaction);

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
     * @param Transaction $transaction
     * @return Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function debitTransaction( $transactionId, Transaction $transaction )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transaction/' . $transactionId . '/debit';

        ksort($headers);

        $jsonBody = json_encode($transaction);

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
     * @param string $orderId
     * @param int $limit between 1 and 100
     * @param string $startDate acceptable format yyyy-MM-dd'T'HH:mm:ss'Z'
     * @param string $endDate acceptable format yyyy-MM-dd'T'HH:mm:ss'Z'
     * @return \Httpful\associative|string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function searchByOrderId( $orderId, $limit = null, $startDate = null , $endDate = null )
    {
        $headers = $this->createHeaderNameValuePairs();
        $uri = '/transactions?order=' . $orderId;

        if(Validator::intVal()->max(100, true)->notEmpty()->validate($limit)){
            $uri += '&limit='.$limit;
        }
        if(Validator::date("Y-m-d'T'HH:mm:ss'Z'")->notEmpty()->validate($startDate)){
            $uri += '&start-date='.urlencode($startDate);
        }
        if(Validator::date("Y-m-d'T'HH:mm:ss'Z'")->notEmpty()->validate($endDate)){
            $uri += '&end-date='.urlencode($limit);
        }


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
            self::$SPH_ACCOUNT => $this->account,
            self::$SPH_MERCHANT => $this->merchant,
            self::$SPH_TIMESTAMP => PaymentHighwayUtility::getDate(),
            self::$SPH_REQUEST_ID => PaymentHighwayUtility::createRequestId(),
            self::$SPH_API_VERSION => $this->apiversion
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
}