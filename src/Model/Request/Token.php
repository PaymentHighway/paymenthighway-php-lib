<?php namespace Solinor\PaymentHighway\Model\Request;


/**
 * Class Token
 *
 * @package Solinor\PaymentHighway\Model\Request
 */

class Token
{
    public $id = null;
    public $cvc = null;

    /**
     * @param string $id
     * @param string $cvc
     */
    public function __construct( $id, $cvc = null)
    {
        $this->id = $id;
        $this->cvc = $cvc;
    }

}