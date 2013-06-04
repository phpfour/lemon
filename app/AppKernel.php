<?php

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Controller\ControllerResolver,
    Symfony\Component\Routing\Route,
    Symfony\Component\Routing\RouteCollection,
    Symfony\Component\Routing\RequestContext,
    Symfony\Component\Routing\Matcher\UrlMatcher,
    Symfony\Component\Routing\Exception\ResourceNotFoundException,
    Symfony\Component\Routing\Exception\MethodNotAllowedException;

class AppKernel
{
    /**
     * @var Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $routes;

    /**
     * @var array
     */
    protected $conf;

    /**
     * @var DBALConnection
     */
    protected $conn;

    /**
     * @var Pimple
     */
    protected $container;

    /**
     * @var \Memcache
     */
    protected $cache;

    public function __construct(Request $request, $container)
    {
        $this->request = $request;

        $this->dm = $container['dm'];
        $this->conf = $container['conf'];
        $this->conn = $container['dbal'];
        $this->routes = $container['routes'];
        $this->cache = $container['cache'];
    }

    public function handle($return = false)
    {
        try {
            $this->matchRoute();
            $response = $this->loadResource();
        } catch (ResourceNotFoundException $e) {
            $response = new Response();
            $response->setStatusCode(404);
        } catch (MethodNotAllowedException $e) {
            $response = new Response();
            $response->setStatusCode(405);
        }

        $response->prepare($this->request);

        if ($return) {
            return $response;
        }

        $this->logTrace($response);

        $response->send();
    }

    private function loadResource()
    {
        $resolver = new ControllerResolver();

        $controller = $resolver->getController($this->request);
        $arguments = $resolver->getArguments($this->request, $controller);

        $controller[0]->setRequest($this->request);
        $controller[0]->setContainer($this->container);
        $controller[0]->init();

        $response = call_user_func_array($controller, $arguments);
        return $response;
    }

    private function matchRoute()
    {
        $routes = new RouteCollection();

        foreach ($this->routes as $key => $route) {
            if (!empty($route['requirements'])) {
                $routes->add($key, new Route($route['pattern'], $route['defaults'], $route['requirements']));
            } else {
                $routes->add($key, new Route($route['pattern'], $route['defaults']));
            }
        }

        $context = new RequestContext();
        $context->fromRequest($this->request);
        $matcher = new UrlMatcher($routes, $context);

        $attributes = $matcher->match($this->request->getPathInfo());
        $this->request->attributes->add($attributes);
    }

    private function logTrace($response)
    {
        $logContent = "
Response Type    : Success
Route            : " . $_SERVER['REQUEST_URI'] . "
Method           : " . $_SERVER['REQUEST_METHOD'] . "
Auth-Token       : " . $this->request->headers->get('auth-token') . "
Request Data     : " . json_encode($this->request->request->all()) . "
Requested By     : " . $_SERVER['REMOTE_ADDR'] . "
User Agent       : " . $this->request->headers->get('user-agent') . "
Controller Route : " . $this->request->attributes->get('_controller') . "
Response Code    : " . $response->getStatusCode() . "
Response Content : " . $response->getContent() . "\n\n";

        $fp = @fopen(__DIR__.'/logs/service.log', 'a+');
        @fwrite($fp, $logContent);
    }
}