<?php

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppBootstrap.php';
require_once __DIR__ . '/../app/AppKernel.php';

$request = Request::createFromGlobals();
$bootstrap = new Bootstrap('prod');

$kernel = new AppKernel($request, $bootstrap->container);

$kernel->handle();