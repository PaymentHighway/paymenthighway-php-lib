<?php namespace Solinor\PaymentHighway\Model;

/**
 * Class Token
 *
 * @package Solinor\PaymentHighway\Model
 */

class Token implements \JsonSerializable
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