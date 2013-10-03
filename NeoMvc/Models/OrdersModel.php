<?php

/**
 * Description of login_model
 * @author Bardas Catalin
 * date: Dec 29, 2011 
 */

namespace NeoMvc\Models;

use Doctrine\ORM\EntityManager;
use NeoMvc\Models\Entity as Entity;

class OrdersModel extends \NeoMvc\Models\Model {

    function __construct() {
        $this->em = $this->getConnection();
    }

    public function getOrderItem($id_item) {
        $orderItem = $this->em->find("Entity:OrderItem", $id_item);
        return $orderItem;
    }

}

?>
