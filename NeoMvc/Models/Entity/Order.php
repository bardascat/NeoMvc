<?php

namespace NeoMvc\Models\Entity;

/**
 * @Entity 
 * @Table(name="orders")
 */
use Doctrine\Common\Collections\ArrayCollection;

class Order {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    private $id_order;

    /**
     * @ManyToOne(targetEntity="User",inversedBy="orders")
     * @JoinColumn(name="id_user", referencedColumnName="id_user" ,onDelete="CASCADE")
     */
    private $user;

    /** @OneToMany(targetEntity="OrderItem", mappedBy="order",cascade={"persist"}) */
    private $orderItems;


    /**
     * @Column(type="integer") @var float
     */
    private $id_user;

    /**
     * @Column(type="string") @var string
     */
    private $payment_method;

    /**
     * @Column(type="string",nullable=true) @var float
     */
    private $shipping_notes;

    /**
     * @Column(type="float") @var float
     */
    private $shipping_cost;

    /**
     * @Column(type="string") @var float
     */
    private $shipping_status = "Undelivered";

    /**
     * @Column(type="string") @var string
     * statusurile by default sunt: F(finalizat), W(waiting), C(anulat),R(refund)
     */
    private $payment_status = "W";

    /**
     * @Column(type="float") @var float
     */
    private $total;

    /**
     * @Column(type="datetime") @var float
     */
    private $orderedOn;

    /**
     * @Column(type="datetime",nullable=true) @var float
     */
    private $shippedOn;

    /**
     * @Column(type="string",nullable=true)
     */
    private $order_number;

    public function __construct() {
        $this->orderedOn = new \DateTime("now");
        $this->orderItems = new ArrayCollection();
      
    }

    public function getOrderedOn() {
        return $this->orderedOn->format("d-m-Y");
    }

    public function getShippedOn() {
        return $this->shippedOn->format('d-m-Y');
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function addOrderItem(OrderItem $orderItem) {
        $this->orderItems->add($orderItem);
        $orderItem->setOrder($this);
    }

    /**
     * @return OrderItem
     */
    public function getItems() {
        return $this->orderItems;
    }

    public function getPayment_method() {
        return $this->payment_method;
    }

    public function setPayment_method($payment_method) {
        $this->payment_method = $payment_method;
    }

    public function getShipping_notes() {
        return $this->shipping_notes;
    }

    public function setShipping_notes($shipping_notes) {
        $this->shipping_notes = $shipping_notes;
    }

    public function getShipping_cost() {
        return $this->shipping_cost;
    }

    public function setShipping_cost($shipping_cost) {
        $this->shipping_cost = $shipping_cost;
    }

    public function getShipping_status() {
        return $this->shipping_status;
    }

    public function setShipping_status($shipping_status) {
        $this->shipping_status = $shipping_status;
    }

    public function getPayment_status() {
        return $this->payment_status;
    }

    public function setPayment_status($payment_status) {
        $this->payment_status = $payment_status;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function setOrderNumber($number) {
        $this->order_number = $number;
    }

    public function getOrderNumber() {
        return $this->order_number;
    }

    /**
     * 
     * @return User;
     */
    public function getUser() {
        return $this->user;
    }

}

?>
