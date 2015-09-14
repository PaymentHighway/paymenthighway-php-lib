<?php namespace Solinor\PaymentHighway;

use Httpful\Request;
use Httpful\Response;
use Solinor\PaymentHighway\Exception\PciDssDisabledException;
use Solinor\PaymentHighway\Exception\ServerCouldNotInitializeTokenException;
use Solinor\PaymentHighway\Exception\ServerCouldNotReturnTokenException;
use Solinor\PaymentHighway\Exception\ServerCouldNotTokenizeCardDataException;
use Solinor\PaymentHighway\Model\SecureSigner;
use Solinor\PaymentHighway\Model\Token;

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

    private function getUriForTokenInit()
    {
        return "/tokenization";
    }

    private function getUriForTokenize($tokenizationId)
    {
        return "/tokenization/$tokenizationId/tokenize";
    }

    private function getUriForToken($tokenizationId)
    {
        return "/tokenization/$tokenizationId";
    }

    public function enablePciDss()
    {
        $this->pciDssEnabled = true;
    }

    private function throwExceptionIfPciDssDisabled()
    {
        if ($this->pciDssEnabled !== true) {
            throw new PciDssDisabledException("You must enabled pci dss manually if you want to use manual card adding.");
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
    public function __construct($serviceUrl, $signatureKeyId, $signatureSecret, $account, $merchant)
    {
        $this->serviceUrl = $serviceUrl;
        $this->signatureKeyId = $signatureKeyId;
        $this->signatureSecret = $signatureSecret;
        $this->account = $account;
        $this->merchant = $merchant;
    }

    /**
     * Initializes card tokenization by receiveing tokenization id that is used for the upcoming other requests.
     *
     * @return string TokenizationId as string or else throws exception.
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
            if ($this->isResultOk($response)) {
                return $response->body->id;
            }
        } catch (\Exception $e) {
        }

        throw new ServerCouldNotInitializeTokenException("getTokenizationOrThrow: " . print_r($response, true));
    }

    private function isResultOk(Response $response)
    {
        return ($response->body->result->code === 100 && $response->body->result->message === 'OK');
    }

    /**
     * Tokenizes given card.
     *
     * Make sure you do not log card data!
     *
     * @param string $tokenizationId Call init to get this one.
     * @param string $expiryMonth ^[0-9]{2}$
     * @param string $expiryYear ^[0-9]{4}$
     * @param string $cvc String
     * @param string $pan
     * @return true Returns true if tokenization call was OK or else throws exception.
     */
    public function tokenize($tokenizationId, $expiryMonth, $expiryYear, $cvc, $pan)
    {
        $this->throwExceptionIfPciDssDisabled();

        $headers = $this->createNameValuePairs();
        $uri = $this->getUriForTokenize($tokenizationId);
        $jsonPayload = json_encode(
            array(
                'card' => array(
                    'expiry_month' => $expiryMonth,
                    'expiry_year' => $expiryYear,
                    'cvc' => $cvc,
                    'pan' => $pan
                )
            )
        );

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_POST, $uri, $headers, $jsonPayload);

        $headers[self::$SIGNATURE] = $signature;
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        $response = Request::post($this->serviceUrl . $uri)
            ->body($jsonPayload)
            ->addHeaders($headers)
            ->send();

        return $this->returnTrueIfTokenizationOkOrThrow($response);
    }

    private function returnTrueIfTokenizationOkOrThrow(Response $response)
    {
        try {
            if ($this->isResultOk($response)) {
                return true;
            }
        } catch (\Exception $e) {
        }

        throw new ServerCouldNotTokenizeCardDataException("returnTrueIfTokenizationOkOrThrow: " . $response->raw_body);
    }

    /**
     * Gets the token for tokenized card.
     *
     * @param string $tokenizationId
     * @return Token Returns Token object that contains token id and some card data or else throws exception.
     */
    public function getToken($tokenizationId)
    {
        $this->throwExceptionIfPciDssDisabled();

        $headers = $this->createNameValuePairs();
        $uri = $this->getUriForToken($tokenizationId);

        ksort($headers);

        $signature = $this->createSecureSign(self::$METHOD_GET, $uri, $headers);

        $headers[self::$SIGNATURE] = $signature;
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        $response = Request::get($this->serviceUrl . $uri)
            ->addHeaders($headers)
            ->send();

        return $this->getTokenAndPartialCardDataOrThrow($response);
    }

    private function getTokenAndPartialCardDataOrThrow($response)
    {
        try {
            if ($this->isResultOk($response)) {
                return new Token(
                    $response->body->card_token,
                    $response->body->card->type,
                    $response->body->card->partial_pan,
                    $response->body->card->expire_year,
                    $response->body->card->expire_month
                );
            }
        } catch (\Exception $e) {
        }

        throw new ServerCouldNotReturnTokenException("getTokenAndPartialCardDataOrThrow: " . $response->raw_body);
    }

    /**
     * Create name value pairs
     *
     * @return array
     */
    private function createNameValuePairs()
    {

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
    private function createSecureSign($method, $uri, $sphNameValuePairs = array(), $body = '')
    {
        $parsedSphParameters = PaymentHighwayUtility::parseSphParameters($sphNameValuePairs);
        $secureSigner = new SecureSigner($this->signatureKeyId, $this->signatureSecret);

        return $secureSigner->createSignature($method, $uri, $parsedSphParameters, $body);

    }
}