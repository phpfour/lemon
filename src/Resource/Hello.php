<?php

namespace Resource;

use Symfony\Component\HttpFoundation\Response;

class Hello extends Base
{
    public function get()
    {
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(array('Hello' => 'World')));
        $response->setStatusCode(200);

        return $response;
    }
}