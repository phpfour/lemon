<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->register();

$src = array(
    'Controller' => __DIR__ . '/../src',
    'Document'   => __DIR__ . '/../src',
    'Repository' => __DIR__ . '/../src',
    'Mapper'     => __DIR__ . '/../src',
    'Exception'  => __DIR__ . '/../src',
    'Entities'   => __DIR__ . '/../src'
);

$loader->registerNamespaces($src);
