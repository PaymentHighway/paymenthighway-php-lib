<?php namespace Solinor\PaymentHighway\Model;

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
     * @return string
     */
    public function createSignature($method, $uri, array $nameValuePairs)
    {
        return sprintf(
            '%s %s %s',
            self::$SignatureScheme,
            $this->secretKeyId,
            $this->sign(
                sprintf(
                    "%s\n%s\n%s",
                    $method,
                    $uri,
                    $this->concatParameters($nameValuePairs)
                )
            )
        );
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

        foreach( $parameters as $key => $value)
            $string .= $key . ':' . $value . "\n";

        return $string;
    }
}