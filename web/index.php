<?php

require_once __DIR__ . '/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

$kernel = new AppKernel($request);
$kernel->handle();