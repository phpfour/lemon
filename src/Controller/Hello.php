<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Response;

class Hello extends Base
{
    public function world()
    {
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(array('Hello' => 'World')));
        $response->setStatusCode(200);

        return $response;
    }
}