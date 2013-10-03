<?php

/**
 * Description of login_model
 * @author Bardas Catalin
 * date: Dec 29, 2011 
 */

namespace NeoMvc\Models;

use Doctrine\ORM\EntityManager;
use NeoMvc\Models\Entity as Entity;

class NeoCartModel extends \NeoMvc\Models\Model {

    function __construct() {
        $this->em = $this->getConnection();
    }

    public function addToCart($_REQUEST, Entity\NeoCart $cart) {

        $cartItem = new Entity\CartItem();
        //get item
        $item = $this->em->find("Entity:Item", $_REQUEST['id_item']);
        $cartItem->setItem($item);

        $cartItem->setQuantity($_REQUEST['quantity']);
        if (isset($_REQUEST['size']))
            $cartItem->setSize($_REQUEST['size']);

        //setam unique hash. Acest hash este generat de atributele ce fac cartitem-ul unic=> id_cart,id_size,id_item
        $cartItem->setId_cart($cart->getId_cart());

        if (isset($_REQUEST['is_gift'])) {
            $cartItem->setIs_gift(1);
            $cartItem->setDetails($_REQUEST['details']);
        }

        $cartItem->setUniqueHash();

        //Verificam daca produsul cu acelasu hash a mai fost adaugat in cos. Daca da facem update la cantitate
        if ($this->tryUpdateQuantity($cart, $cartItem)) {
            return true;
        }
        $cart->addCartItem($cartItem);
        $this->em->persist($cart);
        $this->em->flush();

        return true;
    }

    private function tryUpdateQuantity(Entity\NeoCart $cart, Entity\CartItem $cartItem) {

        $rows = $this->em->createQuery("update Entity:CartItem c set c.quantity=c.quantity+1 where c.unique_hash=:hash")
                ->setParameter(":hash", $cartItem->getUnique_hash())
                ->execute();

        //in rows avem cate >0 => a facut updatate, teoretic daca e 1 a fost un duplicat
        if ($rows)
            return true;
        else
            return false;
    }

    public function createCart(Entity\NeoCart $cart) {
        $this->em->persist($cart);
        $this->em->flush();
        return true;
    }

    /**
     * Intoarce shopping cartul in functie de cookie. Daca nu exista il creeaza
     * @param type $hash
     * @return \NeoMvc\Models\Entity\NeoCart
     */
    public function getCart($hash) {

        $cartRep = $this->em->getRepository("Entity:NeoCart");
        $cart = $cartRep->findBy(array("hash" => $hash));

        if (isset($cart[0]))
            return $cart[0];
        else {
            //trebuie sa cream una
            $cart = new Entity\NeoCart();
            $cart->setHash($hash);
            $this->em->persist($cart);
            $this->em->flush($cart);
            return $cart;
        }
    }

    /**
     * Face update la cantitatea unui item din cart
     * @param type $_POST
     * @param type $cartHash
     * @return Boolean
     */
    public function updateQuantity($_POST) {
        $cartItem = $this->getCartItemByPk($_POST['cartItem']);
        $remove = false;

        if (isset($_POST['plus']))
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        else {
            //stergem itemul
            if ($cartItem->getQuantity() <= 1) {
                $remove = true;
            }
            $cartItem->setQuantity($cartItem->getQuantity() - 1);
        }
        if ($remove)
            $this->em->remove($cartItem);
        else
            $this->em->persist($cartItem);

        $this->em->flush();
    }

    public function deleteCartItem($id_item) {

        $dql = $this->em->createQuery("delete from Entity:CartItem item where item.id=:id_item");
        $dql->setParameter(":id_item", $id_item);
        $dql->execute();
        return true;
    }

    public function getNrItems() {

        $hash = \NeoMvc\Controllers\controller::getHash();

        $dql = $this->em->createQuery("
            select count(cartItems.id) as nr_items from Entity:NeoCart cart join cart.CartItems cartItems
            where cart.hash=:hash");

        $dql->setParameter(":hash", $hash);
        $r = $dql->getResult();
        if (!isset($r[0]))
            0;
        else {
            $r = $r[0];
            return $r['nr_items'];
        }
    }

    /**
     * Intoarce un obiect cartItem
     * @param type $id
     * @return Entity\CartItem
     */
    public function getCartItemByPk($id) {
        $cartItem = $this->em->find("Entity:CartItem", $id);
        return $cartItem;
    }

    public function emptyCart() {

        $dql = $this->em->createQuery('delete Entity:NeoCart cart where cart.hash=:hash');
        $dql->setParameter(":hash", \NeoMvc\Controllers\NeoCart::getHash());
        $dql->execute();
        return true;
    }

    /**
     * 
     * @param \NeoMvc\Models\Entity\User $user
     * @param type $params
     * @return \NeoMvc\Models\Entity\Order
     */
    public function insertOrder(Entity\User $user, $params) {
        $nextOrderId = $this->getNextId("orders", "id_order");

        $date = new \DateTime();
        $stamp = $date->getTimestamp();
        $last_four = substr($stamp, -4);

        $cart = $this->getCart(\NeoMvc\Controllers\NeoCart::getHash());
        if (!$cart) {
            header('Location: ' . URL);
            exit();
        }

        $cartItems = $cart->getCartItems();

        $order = new Entity\Order();

        $total = 0;
        /* @var $cartItem Entity\CartItem */
        foreach ($cartItems as $cartItem) {
            $orderItem = new Entity\OrderItem();

            $item = $cartItem->getItem();

            /* @var $itemDetails Entity\Product */ // sau Entity\Offer
            $itemDetails = $item->getItemDetails();

            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setId_size($cartItem->getSize()); //To do with Entity
            $orderItem->setTotal($cartItem->getTotal($itemDetails->getSale_price()));
            $orderItem->setItem($item);
            $total+=$cartItem->getTotal($itemDetails->getSale_price());

            /**
             * Daca Itemul este oferta, atunci trebuie sa adaugam vouchere
             * Observatie: item-ul poate fi facut si cadou atunci mai avem in post pe langa nume si emailul prietenului
             */
            if ($item->getItem_type() == "offer") {
                for ($i = 0; $i < $orderItem->getQuantity(); $i++) {
                    $voucher = new Entity\OrderVoucher();
                    $voucher->setRecipientName($_POST['name_' . $cartItem->getId()][$i]);
                    if ($cartItem->getIs_gift()) {
                        $voucher->setRecipientEmail($_POST['email_' . $cartItem->getId()][$i]);
                        $voucher->setIs_gift(1);
                    }
                    $code = "ORV" . $nextOrderId . 'V' . substr(uniqid(), -4);
                    $voucher->setCode($code);
                    $orderItem->addVoucher($voucher);
                }
            }

            $order->addOrderItem($orderItem);
        }

        $order->setTotal($total);
        $order->setPayment_method($params['payment_method']);
        $order->setShipping_cost($this->getShippingCost($params['payment_method']));
        $order->setUser($user);
        $order->setPayment_status("W");
        $orderCode = "ORO" . $nextOrderId . 'O' . $last_four;
        $order->setOrderNumber($orderCode);

        $this->em->persist($order);

        $this->em->flush();
        $this->emptyCart();
        return $order;
    }

    public function getShippingCost($shippingMethod) {
        switch ($shippingMethod) {
            case "card": {
                    return 0;
                }break;
            case "op": {
                    return 0;
                }break;
            case "ramburs": {
                    return 0;
                }break;
            default: {
                    exit("Err:12:00 Payment method not implemented");
                }break;
        }
    }

}

?>
