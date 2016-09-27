<?php namespace Solinor\PaymentHighway;

use Httpful\Request;
use Httpful\Response;
use Solinor\PaymentHighway\Model\Request\Transaction;
use Solinor\PaymentHighway\Security\SecureSigner;
use Respect\Validation\Validator;

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
    public function __construct($serviceUrl, $signatureKeyId, $signatureSecret, $account, $merchant, $apiversion = "20160630")
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
        $uri = '/transaction';
        return $this->makeRequest(self::$METHOD_POST, $uri);
    }

    /**
     * @param string|UUID $transactionId
     * @param $amount
     * @param $currency
     * @return Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function commitFormTransaction($transactionId, $amount, $currency)
    {
        $uri = '/transaction/' . $transactionId . '/commit';
        return $this->makeRequest(self::$METHOD_POST, $uri, new Transaction(null, $amount, $currency));
    }


    /**
     * Charge the credit card
     * @param string|UUID $transactionId
     * @param Transaction $transaction
     * @return Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function debitTransaction($transactionId, Transaction $transaction)
    {
        $uri = '/transaction/' . $transactionId . '/debit';
        return $this->makeRequest(self::$METHOD_POST, $uri, $transaction);
    }


    /**
     * @param string $transactionId
     * @param string $amount
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function revertTransaction($transactionId, $amount)
    {
        $uri = '/transaction/' . $transactionId . '/revert';
        return $this->makeRequest(self::$METHOD_POST, $uri, array('amount' => $amount));
    }

    /**
     * @param string $transactionId
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function statusTransaction($transactionId)
    {
        $uri = '/transaction/' . $transactionId;
        return $this->makeRequest(self::$METHOD_GET, $uri);
    }

    /**
     * @param string $orderId
     * @param int $limit between 1 and 100
     * @param string $startDate acceptable format yyyy-MM-dd'T'HH:mm:ss'Z'
     * @param string $endDate acceptable format yyyy-MM-dd'T'HH:mm:ss'Z'
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function searchByOrderId($orderId, $limit = null, $startDate = null, $endDate = null)
    {
        $uri = '/transactions?order=' . $orderId;

        if (Validator::intVal()->max(100, true)->notEmpty()->validate($limit)) {
            $uri .= '&limit=' . $limit;
        }
        if (Validator::date("Y-m-d'T'HH:mm:ss'Z'")->notEmpty()->validate($startDate)) {
            $uri .= '&start-date=' . urlencode($startDate);
        }
        if (Validator::date("Y-m-d'T'HH:mm:ss'Z'")->notEmpty()->validate($endDate)) {
            $uri .= '&end-date=' . urlencode($limit);
        }

        return $this->makeRequest(self::$METHOD_GET, $uri);
    }

    /**
     * @param $tokenizeId
     * @return \Httpful\Response
     */
    public function tokenize($tokenizeId)
    {
        $uri = '/tokenization/' . $tokenizeId;
        return $this->makeRequest(self::$METHOD_GET, $uri);
    }

    /**
     * @param string $date date in format yyyyMMdd
     * @return \Httpful\Response
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getReport($date)
    {
        $uri = '/report/batch/' . $date;
        return $this->makeRequest(self::$METHOD_GET, $uri);
    }


    /**
     * @param string $date in format yyyyMMdd. The date to fetch the reconciliation report for.
     * @param bool $useDateProcessed Use the acquirer processed date instead of report received date. Might cause changes to the past
     * @return \Httpful\Response
     */
    public function fetchReconciliationReport($date, $useDateProcessed = false)
    {
        $uri = '/report/reconciliation/' . $date . '?use-date-processed=' . $useDateProcessed;
        return $this->makeRequest(self::$METHOD_GET, $uri);
    }

    /**
     * Create name value pairs
     *
     * @return array
     */
    private function createHeaderNameValuePairs()
    {

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

    /**
     * @param $method
     * @param $uri
     * @param null $body
     * @return \Httpful\Response
     */
    private function makeRequest($method, $uri, $body = null)
    {
        $headers = $this->createHeaderNameValuePairs();
        $jsonBody = '';
        if ($body)
            $jsonBody = json_encode($body);

        ksort($headers);
        $signature = $this->createSecureSign($method, $uri, $headers, $jsonBody);

        $headers[self::$SIGNATURE] = $signature;
        $headers[self::$CT_HEADER] = self::$CT_HEADER_INFO;

        if ($method === self::$METHOD_POST) {
            $response = Request::post($this->serviceUrl . $uri)
                ->body($jsonBody);
        } else {
            $response = Request::get($this->serviceUrl . $uri);
        }

        return $response->addHeaders($headers)->send();
    }
}