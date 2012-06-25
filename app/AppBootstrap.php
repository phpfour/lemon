<?php

use Symfony\Component\Yaml\Parser;

class Bootstrap
{
    /**
     * @var array
     */
    public $conf;

    /**
     * @var array
     */
    public $routes = array();

    public function __construct($env)
    {
        $this->loadConfiguration($env);
    }

    private function loadConfiguration($env)
    {
        $yaml = new Parser();

        $default = $yaml->parse(file_get_contents(__DIR__ . '/config/config.yml'));
        $environment = $yaml->parse(file_get_contents(__DIR__ . '/config/config_' . $env . '.yml'));

        if (is_array($environment)) {
            $this->conf = array_merge($default, $environment);
        } else {
            $this->conf = $default;
        }

        $fileIterator = new DirectoryIterator(__DIR__ . '/config/routes');

        foreach ($fileIterator as $file) {
            if ($file->isFile()) {
                $routes = $yaml->parse(file_get_contents(__DIR__ . '/config/routes/' . $file->getFilename()));
                $this->routes = array_merge($this->routes, $routes);
            }
        }
    }
}