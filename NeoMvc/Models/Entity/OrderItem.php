<?php

namespace NeoMvc\Models\Entity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity 
 * @Table(name="orders_items")
 */
class OrderItem {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Order",inversedBy="orderItems")
     * @JoinColumn(name="id_order", referencedColumnName="id_order" ,onDelete="CASCADE")
     */
    private $order;

    /**
     * @ManyToOne(targetEntity="Item")
     * @JoinColumn(name="id_item", referencedColumnName="id_item")
     */
    private $item;

    /** @OneToMany(targetEntity="OrderVoucher", mappedBy="orderItem",cascade={"persist"}) */
    private $vouchers;

    /**
     * @Column(type="integer")
     */
    private $quantity;

    /**
     * @Column(type="integer",nullable=true)
     */
    private $id_size;

    /**
     * @Column(type="float")
     */
    private $total;

    function __construct() {
        $this->vouchers = new ArrayCollection();
    }

    public function setOrder(Order $order) {
        $this->order = $order;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getId_size() {
        return $this->id_size;
    }

    public function setId_size($id_size) {
        $this->id_size = $id_size;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setItem(Item $item) {
        $this->item = $item;
    }

    public function getItem() {
        return $this->item;
    }

    public function addVoucher(OrderVoucher $voucher) {
        $this->vouchers->add($voucher);
        $voucher->setOrderItem($this);
    }
    /**
     * 
     * @return \NeoMvc\Models\Entity\OrderVoucher
     */
    public function getVouchers(){
        return $this->vouchers;
    }
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }



}

?>
