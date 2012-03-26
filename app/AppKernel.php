<?php

require_once __DIR__ . '/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppKernel
{
    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $resourceClass = $this->getResourceClass();

        if ($resourceClass === false) {
            $response = new Response();
            $response->setStatusCode(406);
        } else {
            $response = $this->loadResource($resourceClass);
        }

        $response->prepare($this->request);
        $response->send();
    }

    private function loadResource($resourceClass)
    {
        $method = strtolower($this->request->getMethod());
        $resource = new $resourceClass($this->request);
        $response = $resource->$method();

        return $response;
    }

    private function getResourceClass()
    {
        $paths = explode('/', $this->request->getPathInfo());
        $resource = 'Resource\\' . ucfirst($paths[1]);

        if (class_exists($resource)) {
            return $resource;
        }

        return false;
    }
}