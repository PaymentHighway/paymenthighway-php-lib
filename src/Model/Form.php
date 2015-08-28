<?php namespace Solinor\PaymentHighway\Model;

/**
 * Class Form
 * @package Solinor\PaymentHighway\Model
 */

class Form {

    private $method = null;
    private $baseurl = null;
    private $actionurl = null;
    private $parameters = array();

    /**
     * @param string $method
     * @param string $baseurl
     * @param string $actionurl
     * @param array $parameters
     */
    public function __construct($method, $baseurl, $actionurl, array $parameters)
    {
        $this->method = $method;
        $this->baseurl = $baseurl;
        $this->actionurl = $actionurl;
        $this->parameters = $parameters;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getAction()
    {
        return $this->baseurl . $this->actionurl;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}