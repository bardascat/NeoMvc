<?php

namespace NeoMvc\Models;

/**
 * Description of Model
 * @author Bardas Catalin
 * date: Dec 28, 2011 g
 */
use Doctrine\ORM\EntityManager;
use NeoMvc\Libs\Database;

class Model extends \NeoMvc\Libs\Database {

    /**
     * @var Database $db
     */
    protected $db = null;

    /**
     * @var EntityManager $Eem
     */
    protected $em = null;
    protected static $connection;

    /**
     * Singleton pattern, returneaza conexiunea
     * @return EntityManger
     */
    public function getConnection() {
        if (!self::$connection) {
            $this->initDb();
            self::$connection = $this->db->connect();
        }
        return self::$connection;
    }

    public function initDb() {
        if (!$this->db)
            $this->db = new Database();
    }

    protected function mapPostToEntity($post, &$entity, $class) {

        $obj_props = get_object_vars($entity);

        foreach ($post as $key => $value) {
            if (array_key_exists($key, $obj_props))
                $entity->$key = $value;
        }
    }

    public function getNextId($table) {
        $q = "SHOW TABLE STATUS LIKE '$table'";

        $stmt = $this->em->getConnection()->prepare($q);
        $stmt->execute();
        $r = $stmt->fetchAll();
        return $r[0]['Auto_increment'];
    }

    public function getCurrentId($table, $column) {
        $q = "select * from $table order by $column desc limit 1";

        $stmt = $this->em->getConnection()->prepare($q);
        $stmt->execute();
        $r = $stmt->fetchAll();
        if (isset($r[0]['id']))
            return $r[0]['id'];
        else
            return 1;
    }

    private function createRandomPassword() {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double) microtime() * 1000000);
        $i = 0;
        $pass = '';
        while ($i <= 7) {

            $num = rand() % 33;

            $tmp = substr($chars, $num, 1);

            $pass = $pass . $tmp;

            $i++;
        }
        return $pass;
    }

}

?>
