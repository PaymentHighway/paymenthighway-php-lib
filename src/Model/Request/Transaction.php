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
     * @param bool $blocking
     * @param string $orderId
     * @throws Exception
     */
    public function __construct( $request, $amount, $currency, $blocking = true, $orderId = null )
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->orderId = $orderId;
        $this->blocking = $blocking;

        $this->setRequestByType($request);
    }

    /**
     * @param $request
     * @throws Exception
     */
    private function setRequestByType( $request )
    {
        if( $request instanceof Token ){
            $this->token = $request;
        }
        elseif( $request instanceof Card ){
            $this->card = $request;
        }
        else{
            throw new Exception("Invalid request object type must be type of Card or Token");
        }
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