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
    public $order = null;
    public $blocking = true;

    public $token = null;
    public $card = null;

    /**
     * @param int $amount
     * @param string $currency
     * @param Card|Token|null $request
     * @param bool $blocking
     * @param string $orderId
     * @throws Exception
     */
    public function __construct( $request, $amount, $currency, $blocking = true, $orderId = null )
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->order = $orderId;
        $this->blocking = $blocking;

        $this->setRequestByType($request);
    }

    /**
     * @param $request
     */
    private function setRequestByType( $request )
    {
        if( $request instanceof Token ){
            $this->token = $request;
        }
        elseif( $request instanceof Card ){
            $this->card = $request;
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