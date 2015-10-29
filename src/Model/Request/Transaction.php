<?php namespace Solinor\PaymentHighway\Model\Request;

use Solinor\PaymentHighway\Model\Token;
use Solinor\PaymentHighway\Model\Card;
/**
 * Class Transaction
 * @package Solinor\PaymentHighway\Model\Request
 */

class Transaction implements \JsonSerializable
{
    public $amount = null;
    public $currency = null;
    public $orderId = null;
    public $blocking = true;

    public $token = null;
    public $card = null;

    /**
     * @param Card|Token $request
     * @param int $amount
     * @param string $currency
     * @param string $orderId
     */
    public function __construct( $request, $amount, $currency, $blocking = true, $orderId = null )
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->orderId = $orderId;
        $this->blocking = $blocking;

        $this->setRequestByType($request);
    }

    private function setRequestByType( $request )
    {
        if( $request instanceof Token )
            $this->token = $request;

        if( $request instanceof Card )
            $this->card = $request;

    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $data = get_object_vars($this);

        foreach($data as $key => $val)
            if($val === null)
                unset($data[$key]);

        return $data;
    }
}