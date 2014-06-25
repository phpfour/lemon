<?php

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Controller\ControllerResolver,
    Symfony\Component\Routing\Route,
    Symfony\Component\Routing\RouteCollection,
    Symfony\Component\Routing\RequestContext,
    Symfony\Component\Routing\Matcher\UrlMatcher,
    Symfony\Component\Routing\Exception\ResourceNotFoundException,
    Symfony\Component\Routing\Exception\MethodNotAllowedException,
    Doctrine\DBAL\Connection as DBALConnection;

class AppKernel
{
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

    public function __construct(Request $request, $container)
    {
        $this->request = $request;
        $this->container = $container;

        $this->conf = $container['conf'];
        $this->conn = $container['dbal'];
        $this->routes = $container['routes'];

    }

    public function handle()
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
}