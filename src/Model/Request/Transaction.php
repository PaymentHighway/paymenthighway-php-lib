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
    public $commit = null;

    public $reference_number = null;

    /**
     * @param int $amount
     * @param string $currency
     * @param Card|Token|null $request
     * @param bool $blocking
     * @param string $orderId
     * @param \Solinor\PaymentHighway\Model\Splitting $splitting
     * @param bool $commit Whether or not automatically commit the payment. Default true.
     * @param string $referenceNumber Reference number in RF or Finnish reference format, used when settling the transaction to the merchant account. Only used if one-by-ony transaction settling is configured.
     * @throws \Exception
     */
    public function __construct( $request, $amount, $currency, $blocking = true, $orderId = null, $splitting = null, $commit = null, $referenceNumber = null)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->order = $orderId;
        $this->blocking = $blocking;
        $this->splitting = $splitting;
        $this->commit = $commit;
        $this->reference_number = $referenceNumber;

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
