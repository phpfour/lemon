<?php

use Doctrine\Common\ClassLoader,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\ODM\MongoDB\DocumentManager,
    Doctrine\MongoDB\Connection,
    Doctrine\ODM\MongoDB\Configuration,
    Doctrine\DBAL\Configuration as DBALConfig,
    Doctrine\DBAL\DriverManager as DBALDriverManager,
    Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration as ORMConfiguration,
    Symfony\Component\Yaml\Parser;

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
        $this->loadConfiguration($env);
        $this->createServiceContainer();
        $this->loadConfiguration($env);
        $this->loadRoutes();
        $this->loadDBAL();
        $this->loadDoctrineODM();
        $this->loadDoctrineORM();
        $this->loadCacheService();
    }

    private function createServiceContainer()
    {
        $this->container = new Pimple();
    }

    private function loadConfiguration($env)
    {
        $yaml = new Parser();

        $default = $yaml->parse(file_get_contents(__DIR__ . '/config/config.yml'));
        $environment = $yaml->parse(file_get_contents(__DIR__ . '/config/parameters.yml'));

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

    private function loadRoutes()
    {
        $yaml = new Parser();
        $fileIterator = new DirectoryIterator(__DIR__ . '/config/routes');

        foreach ($fileIterator as $file) {
            if ($file->isFile() && $file->getExtension() == 'yml') {
                $routes = $yaml->parse(file_get_contents(__DIR__ . '/config/routes/' . $file->getFilename()));
                $this->routes = array_merge($this->routes, $routes);
            }
        }

        $this->container['routes'] = $this->routes;
    }

    private function loadDBAL()
    {
        $this->container['dbal'] = $this->container->share(function($c){

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

    private function loadDoctrineODM()
    {
        $this->container['dm'] = $this->container->share(function($c){

            $config = new Configuration();
            $config->setProxyDir(__DIR__ . '/cache');
            $config->setProxyNamespace('Proxies');

            $config->setHydratorDir(__DIR__ . '/cache');
            $config->setHydratorNamespace('Hydrators');
            $config->setDefaultDB($c['conf']['mongodb']['database']);
            AnnotationDriver::registerAnnotationClasses();

            $reader = new AnnotationReader();
            $config->setMetadataDriverImpl(new AnnotationDriver($reader, __DIR__ . '/../src/Documents'));

            $dsn = 'mongodb://' . $c['conf']['mongodb']['host'] . ':' . $c['conf']['mongodb']['port'];
            $dm = DocumentManager::create(new Connection($dsn), $config);

            return $dm;

        });
    }

    private function loadCacheService()
    {
        $this->container['cache'] = new \Memcache();
    }
}