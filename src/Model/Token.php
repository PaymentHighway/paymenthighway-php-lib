<?php namespace Solinor\PaymentHighway\Model;

/**
 * Class Token
 *
 * @package Solinor\PaymentHighway\Model
 */

class Token extends JsonSerializable
{
    public $id = null;
    public $cvc = null;

    /**
     * @param string $id
     * @param string $cvc
     */
    public function __construct($id, $cvc = null)
    {
        $this->id = $id;
        $this->cvc = $cvc;
    }
}
