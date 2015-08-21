<?php namespace Solinor\PaymentHighway\Model;


class SecureSigner {

    public static $SignatureScheme = "SPH1";
    public static $Algorithm = "HmacSHA256";

    private $secretKeyId = null;
    private $secretKey = null;

    private $signer = null;


    public function __construct($secretKeyId, $secretKey)
    {
        $this->secretKeyId = $secretKeyId;
        $this->secretKey = $secretKey;
    }

    public function sign()
    {

    }

}