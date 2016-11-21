<?php namespace Solinor\PaymentHighway\Security;

use Exception;
use Solinor\PaymentHighway\PaymentHighwayUtility;

/**
 * Class SecureSigner
 * @package Solinor\PaymentHighway\Model
 */

class SecureSigner {

    public static $SignatureScheme = "SPH1";

    private $secretKeyId = null;
    private $secretKey = null;

    /**
     * @param string $secretKeyId
     * @param string $secretKey
     */
    public function __construct($secretKeyId, $secretKey)
    {
        $this->secretKeyId = $secretKeyId;
        $this->secretKey = $secretKey;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $nameValuePairs
     * @param string $body
     * @return string
     */
    public function createSignature($method, $uri, array $nameValuePairs, $body = "")
    {
        return sprintf(
            '%s %s %s',
            self::$SignatureScheme,
            $this->secretKeyId,
            $this->sign(
                sprintf(
                    "%s\n%s\n%s%s",
                    $method,
                    $uri,
                    $this->concatParameters($nameValuePairs),
                    trim($body)
                )
            )
        );
    }

    /**
     * @param array $params either headers or request params in format [ key => value ]
     * @throws Exception
     */
    public function validateFormRedirect(array $params )
    {
        $this->validateSignature("GET", "", $params);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $nameValuePairs either headers or request params in format [ key => value ]
     * @param string $body
     * @throws Exception
     */
    public function validateSignature($method, $uri, array $nameValuePairs, $body = "")
    {

        $expectedSignature = $nameValuePairs["signature"];

        $actualSignature = $this->createSignature(
            $method,
            $uri,
            PaymentHighwayUtility::parseSphParameters($nameValuePairs),
            $body
        );

        if($expectedSignature != $actualSignature) {
            throw new Exception("Response signature mismatch!");
        }
    }

    /**
     * @param string $data
     * @return string
     */
    private function sign( $data )
    {
        return hash_hmac('sha256', $data, $this->secretKey);
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function concatParameters( array $parameters )
    {
        $string = "";
        ksort($parameters);
        foreach( $parameters as $key => $value)
            $string .= $key . ':' . $value . "\n";

        return $string;
    }
}