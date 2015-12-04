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

    /**
     * @return null|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->baseurl . $this->actionurl;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}