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
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\HttpFoundation\Response;
     */
    protected $response;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var DBALConnection
     */
    protected $conn;

    /**
     * @var \Pimple
     */
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;

        $this->dm = $container['dm'];
        $this->em = $container['em'];
        $this->conn = $container['dbal'];
        $this->config = $container['conf'];
    }

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
     * Initializer function to be used by child classes.
     */
    public function init(){}
}