<?php

namespace NeoMvc\Models\Entity;

/**
 * @Entity 
 * @Table(name="orders_vouchers")
 */
class OrderVoucher {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    private $id_voucher;

    /**
     *
     * @Column(type="integer")
     */
    private $id_order_item;

    /**
     * @ManyToOne(targetEntity="OrderItem",inversedBy="vouchers")
     * @JoinColumn(name="id_order_item", referencedColumnName="id" ,onDelete="CASCADE")
     */
    private $orderItem;

    /**
     *
     * @Column(type="string")
     */
    protected $code;

    /**
     *
     * @Column (type="string",nullable=true)
     */
    protected 	$recipientName;
    
    /**
     *
     * @Column (type="string",nullable=true)
     */
    protected $recipientEmail;
    
    /**
     *
     * @Column (type="integer",nullable=true)
     */
    protected $is_gift;
    
    

    public function getId_voucher() {
        return $this->id_voucher;
    }

    public function setId_voucher($id_voucher) {
        $this->id_voucher = $id_voucher;
    }

   
    public function getId_order_item() {
        return $this->id_order_item;
    }

    public function setId_order_item($id_order_item) {
        $this->id_order_item = $id_order_item;
    }

    public function getOrderItem() {
        return $this->orderItem;
    }

    public function setOrderItem($orderItem) {
        $this->orderItem = $orderItem;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getRecipientName() {
        return $this->recipientName;
    }

    public function setRecipientName($recipientName) {
        $this->recipientName = $recipientName;
    }

    public function getRecipientEmail() {
        return $this->recipientEmail;
    }

    public function setRecipientEmail($recipientEmail) {
        $this->recipientEmail = $recipientEmail;
    }

    public function getIs_gift() {
        return $this->is_gift;
    }

    public function setIs_gift($is_gift) {
        $this->is_gift = $is_gift;
    }






}

?>
