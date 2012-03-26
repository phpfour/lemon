<?php

require_once __DIR__ . '/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->register();

$vendorDir = __DIR__ . '/../vendor';

$loader->registerNamespaces(array(
    'Symfony\\Component\\Yaml' => $vendorDir . '/symfony/yaml/',
    'Symfony\\Component\\HttpFoundation' => $vendorDir . '/symfony/http-foundation/',
    'Symfony\\Component\\Console' => $vendorDir . '/symfony/console/',
    'Symfony\\Component\\ClassLoader' => $vendorDir . '/symfony/class-loader/',
    'SessionHandlerInterface' => $vendorDir . '/symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stubs',
    'Resource' => __DIR__ . '/../src',
));