<?php

namespace NeoMvc\Models\Entity;

/**
 * @Entity 
 * @Table(name="cart_items")
 */
use Doctrine\Common\Collections\ArrayCollection;

class CartItem {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string",nullable=true)
     */
    private $unique_hash;

    /**
     * @Column(type="integer")
     */
    private $id_item;

    /**
     * @Column(type="integer")
     */
    private $id_cart;

    /**
     * @Column(type="string",nullable=true)
     */
    private $id_size;
    
    /**
     * @Column(type="integer",nullable=true)
     */
    private $is_gift;

    /**
     * @Column(type="integer")
     */
    private $quantity = 1;

    /**
     * @ManyToOne(targetEntity="NeoCart",inversedBy="CartItems")
     * @JoinColumn(name="id_cart", referencedColumnName="id_cart" ,onDelete="CASCADE")
     */
    private $NeoCart;

    /**
     * @ManyToOne(targetEntity="Item")
     * @JoinColumn(name="id_item", referencedColumnName="id_item")
     */
    private $item;

    /**
     * Aici bagam json cu numele beneficiarilor voucherului, sau daca vrea sa il dea prietenilor
     * @Column (type="text",nullable=true)
     */
    protected $details;

    /**
     * 
     * @return Item
     */
    public function getItem() {
        return $this->item;
    }

    public function setItem(Item $item) {
        $this->item = $item;
        $this->id_item = $item->getIdItem();
    }

    public function setNeoCart(NeoCart $item) {
        $this->NeoCart = $item;
    }

    public function getNeoCart() {
        return $this->NeoCart;
    }

    public function getSize() {
        return $this->id_size;
    }

    public function setSize($size) {
        $this->id_size = $size;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getUnique_hash() {
        return $this->unique_hash;
    }

    public function setUniqueHash() {
        /**
         * Are rolul de nu a baga duplicate, in situatia in care se adauga in cos acelasi lucru.
         * Ar trebui sa creasca cantitatea
         */
        $this->unique_hash = md5($this->id_cart . $this->id_item . $this->id_size.$this->is_gift);
    }

    public function getId_item() {
        return $this->id_item;
    }

    public function setId_item($id_item) {
        $this->id_item = $id_item;
    }

    public function getTotal($price) {
        return round($price * $this->quantity);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId_cart() {
        return $this->id_cart;
    }

    public function setId_cart($id_cart) {
        $this->id_cart = $id_cart;
    }

    public function getDetails() {
        return $this->details;
    }

    public function setDetails($details) {
        $this->details = $details;
    }
    
    public function getIs_gift() {
        return $this->is_gift;
    }

    public function setIs_gift($is_gift) {
        $this->is_gift = $is_gift;
    }



}

?>
