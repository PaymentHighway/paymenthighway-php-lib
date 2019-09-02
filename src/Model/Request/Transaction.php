<?php namespace Solinor\PaymentHighway\Model\Request;

use Solinor\PaymentHighway\Model\Token;
use Solinor\PaymentHighway\Model\Card;

/**
 * Class Transaction
 * @package Solinor\PaymentHighway\Model\Request
 */

class Transaction extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $amount = null;
    public $currency = null;
    public $order = null;
    public $blocking = true;

    public $token = null;
    public $card = null;
    public $splitting = null;

    /**
     * @param int $amount
     * @param string $currency
     * @param Card|Token|null $request
     * @param bool $blocking
     * @param string $orderId
     * @param \Solinor\PaymentHighway\Model\Splitting $splitting
     * @throws \Exception
     */
    public function __construct( $request, $amount, $currency, $blocking = true, $orderId = null, $splitting = null )
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->order = $orderId;
        $this->blocking = $blocking;
        $this->splitting = $splitting;

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
}
