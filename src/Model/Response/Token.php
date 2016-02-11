<?php namespace Solinor\PaymentHighway\Model\Response;

/**
 * Class Token use in DangerZone responses.
 * @package Solinor\PaymentHighway\Model
 */

class Token {

    public $cardToken = '';
    public $type = '';
    public $partialPan = '';
    public $expireYear = '';
    public $expireMonth = '';

    /**
     * @param $cardToken
     * @param $type
     * @param $partialPan
     * @param $expireYear
     * @param $expireMonth
     */
    public function __construct($cardToken, $type, $partialPan, $expireYear, $expireMonth)
    {
        $this->cardToken = $cardToken;
        $this->type = $type;
        $this->partialPan = $partialPan;
        $this->expireYear = $expireYear;
        $this->expireMonth = $expireMonth;
    }
}