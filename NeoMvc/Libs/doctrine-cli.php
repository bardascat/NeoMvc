<?php

//doctrine-cli.php
use Symfony\Component\Console\Helper\HelperSet,
    Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper,
    Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper,
    Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\ClassLoader;

require_once "vendor/autoload.php";

$loader = new ClassLoader("Entity", "models");
$loader->register();

$dbParams = array(
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'host' => '127.0.0.1',
    'dbname' => 'oringo2.0'
);
$path = array('../../NeoMvc/Models/Entity');

$config = Setup::createAnnotationMetadataConfiguration($path, true);

//$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

$em = EntityManager::create($dbParams, $config);


$helperSet = new HelperSet(array(
    'em' => new EntityManagerHelper($em),
    'conn' => new ConnectionHelper($em->getConnection())
        ));
ConsoleRunner::run($helperSet);
?>
