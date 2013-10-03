<?php

/**
 * Description of Database
 * @author Bardas Catalin
 * date: Jun 26, 2013
 */

namespace NeoMvc\Libs;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\ClassLoader;

class Database {

    function __construct() {
        
    }

    public function connect() {
        $dbParams = array(
            'driver' => 'pdo_mysql',
            'user' => 'root',
            'password' => '',
            'host' => '',
            'dbname' => ''
        );
        $path = array('NeoMvc/Models/Entity');


        $config = Setup::createAnnotationMetadataConfiguration($path, true);
        $config->addEntityNamespace("Entity", "NeoMvc\Models\Entity");

       //  $config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

        $em = EntityManager::create($dbParams, $config);

        //$this->updateSchema($em);
        return $em;
    }

    public function updateSchema($em) {

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata("Entity:CartItem"),
            $em->getClassMetadata("Entity:Category"),
            $em->getClassMetadata("Entity:Company"),
            $em->getClassMetadata("Entity:Item"),
            $em->getClassMetadata("Entity:ItemCategories"),
            $em->getClassMetadata("Entity:ItemImage"),
            $em->getClassMetadata("Entity:NeoCart"),
   
            $em->getClassMetadata("Entity:Order"),
            $em->getClassMetadata("Entity:OrderItem"),
         
            $em->getClassMetadata("Entity:User"),
            $em->getClassMetadata("Entity:Specification"),
            $em->getClassMetadata("Entity:SpecificationsValues"),
            $em->getClassMetadata("Entity:ItemReviews"),
            $em->getClassMetadata("Entity:OrderVoucher")
        );

        $tool->updateSchema($classes);
        exit("done");
    }

}

?>