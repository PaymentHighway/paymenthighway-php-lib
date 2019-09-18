<?php namespace Solinor\PaymentHighway\Model\Request;

use Solinor\PaymentHighway\Model\Token;
use Solinor\PaymentHighway\Model\Card;

/**
 * Class CustomerInitiatedTransaction
 * @package Solinor\PaymentHighway\Model\Request
 */

class CustomerInitiatedTransaction extends \Solinor\PaymentHighway\Model\JsonSerializable
{
    public $amount = null;
    public $currency = null;
    public $order = null;
    public $blocking = true;

    public $token = null;
    public $card = null;
    public $splitting = null;

    public $strong_customer_authentication = null;

    /**
     * @param Card|Token|null $request
     * @param int $amount
     * @param string $currency
     * @param \Solinor\PaymentHighway\Model\Sca\StrongCustomerAuthentication $strong_customer_authentication
     * @param bool $blocking
     * @param string $orderId
     * @param \Solinor\PaymentHighway\Model\Splitting $splitting
     * @throws \Exception
     */
    public function __construct( $request, $amount, $currency, $strong_customer_authentication, $blocking = true, $orderId = null, $splitting = null )
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->order = $orderId;
        $this->blocking = $blocking;
        $this->splitting = $splitting;
        $this->strong_customer_authentication = $strong_customer_authentication;

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
