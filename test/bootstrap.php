<?php

define("TEST_DIR", realpath(__DIR__));

require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppBootstrap.php';
require_once __DIR__ . '/../app/AppKernel.php';
require_once __DIR__ . '/test_helper.php';

global $config;
global $container;

$bootstrap = new Bootstrap('test');
$bootstrap->container['dm']->getConnection()->connect();

$config = $bootstrap->container['conf'];
$container = $bootstrap->container;

prepareBackupDatabase();

HttpHelper::$bootstrap = $bootstrap;