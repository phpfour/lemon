<?php
use Doctrine\Common\ClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Configuration as DBALConfig;
use Doctrine\DBAL\DriverManager as DBALDriverManager;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
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

    /**
     * @var Pimple
     */
    public $container;

    public function __construct($env)
    {
        $this->createServiceContainer();
        $this->loadConfiguration($env);
        $this->loadRoutes();
        $this->loadDBAL();
        $this->loadDoctrineORM();

    }
    private function createServiceContainer()
    {
        $this->container = new Pimple();
    }

    private function loadConfiguration($env)
    {
        $yaml = new Parser();

        $default = $yaml->parse(file_get_contents(__DIR__ . '/config/config.yml'));
        $environment = $yaml->parse(file_get_contents(__DIR__ . '/config/config_' . $env . '.yml'));

        if (is_array($environment)) {
            $conf = array_merge($default, $environment);
        } else {
            $conf = $default;
        }

        $this->container['env'] = $env;
        $this->container['conf'] = $conf;
    }

    private function loadRoutes()
    {
        $yaml = new Parser();
        $fileIterator = new DirectoryIterator(__DIR__ . '/config/routes');

        foreach ($fileIterator as $file) {
            if ($file->isFile()) {
                $routes = $yaml->parse(file_get_contents(__DIR__ . '/config/routes/' . $file->getFilename()));
                $this->routes = array_merge($this->routes, $routes);
            }
        }

        $this->container['routes'] = $this->routes;
    }

    private function loadDBAL()
    {
        $this->container['dbal'] = $this->container->share(function($c) {

            $dbalConfig = new \Doctrine\DBAL\Configuration();

            $db   = $c['conf']['mysql']['db'];
            $host = $c['conf']['mysql']['host'];
            $user = $c['conf']['mysql']['user'];
            $pass = $c['conf']['mysql']['pass'];

            $connectionParams = array(
                'dbname'   => $db,
                'user'     => $user,
                'password' => $pass,
                'host'     => $host,
                'driver'   => 'pdo_mysql',
            );

            $conn = DBALDriverManager::getConnection($connectionParams, $dbalConfig);
            return $conn;

        });
    }
    private function loadDoctrineORM()
    {
        $this->container['em'] = $this->container->share(function($c){

            if ($c['env'] == "dev") {
                $cache = new \Doctrine\Common\Cache\ArrayCache;
            } else {
                $cache = new \Doctrine\Common\Cache\ApcCache;
            }

            $config = new ORMConfiguration;
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);

            $driverImpl = $config->newDefaultAnnotationDriver(__DIR__ . '/../src/Entities');
            $config->setMetadataDriverImpl($driverImpl);

            $config->setProxyDir(__DIR__ . '/cache/Proxies');
            $config->setProxyNamespace('Proxies');

            if ($c['env'] == "dev") {
                $config->setAutoGenerateProxyClasses(true);
            } else {
                $config->setAutoGenerateProxyClasses(false);
            }

            $em = EntityManager::create($c['dbal'], $config);
            return $em;

        });
    }

}