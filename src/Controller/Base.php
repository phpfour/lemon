<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Base
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Response;
     */
    protected $response;

    /**
     * @var array
     */
    protected $config;

    /**
     * Inject the Request object for further use.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Inject the configuration.
     *
     * @param $config array
     */
    public function setConfiguration($config)
    {
        $this->config = $config;
    }

    /**
     * Initializer function to be used by child classes.
     */
    public function init(){}
}