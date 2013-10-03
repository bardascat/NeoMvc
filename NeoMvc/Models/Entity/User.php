<?php

namespace NeoMvc\Models\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity 
 * @Table(name="users")
 */
class User extends AbstractEntity {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    protected $id_user;

    /**
     *
     * @Column(type="string",nullable=true) @var string 
     */
    protected $nume;

    /**
     * @Column(type="string",nullable=true) @var string 
     */
    protected $prenume;

    /**
     * @Column(type="string",unique=true) @var string 
     */
    protected $email;

    /**
     * @Column(type="string") @var string 
     */
    protected $password;
    /**
     * @Column(type="string") @var string 
     */
    protected $real_password;

    /**
     * @Column(type="string") @var string 
     * Level 1 admin, level 2 partener, level 3 user
     */
    protected $access_level = 3;

    /**
     * @Column(type="datetime")
     */
    protected $created_date;

    /**
     * @OneToMany(targetEntity="Item",mappedBy="company")
     */
    protected $items;

    /**
     * @OneToMany(targetEntity="ItemReviews",mappedBy="user")
     */
    protected $ItemReviews;

    /**
     * @OneToOne(targetEntity="Company",mappedBy="user",cascade={"persist"})
     */
    protected $company;

    /**
     * @OneToMany(targetEntity="Order",mappedBy="user",cascade={"persist"})
     * @OrderBy({"id_order" = "desc"})
     */
    protected $orders;

    function __construct() {
        $this->created_date = new \DateTime("now");
        $this->item = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->ItemReviews = new ArrayCollection();
    }

    public function addItem(Item $item) {
        $this->items->add($item);
        $item->setUser($this);
    }

    public function addOrder(Order $order) {
        $this->orders->add($order);
        $order->setUser($this);
    }

    public function addItemReview(ItemReviews $review) {
        $this->ItemReviews->add($review);
        $review->setUser($this);
    }

    /**
     * Intoarce toate comenzile userului, indiferent de tipul item-urilor
     * @return OrderItem
     */
    public function getOrders() {
        return $this->orders;
    }

    /**
     * TODO: S-ar putea sa nu fie foarte optim, poate va fi necesar sa rezolv dintr un singur query
     * Intoarce comenzile ce contin doar item-uri de tip produs
     * @return OrderItem
     */
    public function getProductOrders() {
        $orders = array();
        $nr_items = 0;
        foreach ($this->orders as $order) {
            $has_product = false;
            $orderItems = $order->getItems();
            foreach ($orderItems as $orderItem) {
                $item = $orderItem->getItem();
                if ($item->getItem_type() == "product") {
                    $nr_items++;
                    $has_product = true;
                }
            }
            if ($has_product) {
                $orderArray = array('order' => $order, 'nr_items' => $nr_items);
                $orders[] = $orderArray;
                $nr_items = 0;
                $has_product = false;
            }
        }
        return $orders;
    }

    /**
     * Intoarce comenzile ce ontin doar item-uri de tip Offer
     * @return OrderItem
     */
    public function getOfferOrders() {
        $orders = array();
        $nr_items = 0;
        foreach ($this->orders as $order) {
            $has_offer = false;
            $orderItems = $order->getItems();
            foreach ($orderItems as $orderItem) {
                $item = $orderItem->getItem();
                if ($item->getItem_type() == "offer") {
                    $has_offer = true;
                }
            }
            if ($has_offer) {
                $orderArray = array('order' => $order, 'nr_items' => $nr_items);
                $orders[] = $orderArray;
                $nr_items = 0;
                $has_offer = false;
            }
        }
        return $orders;
    }

    public function getCreatedDate() {
        return $this->created_date->format('d-m-Y');
    }

    public function getId_user() {
        return $this->id_user;
    }

    public function setId_user($id_user) {
        $this->id_user = $id_user;
    }

    public function getNume() {
        return $this->nume;
    }

    public function setNume($nume) {
        $this->nume = $nume;
    }

    public function getPrenume() {
        return $this->prenume;
    }

    public function setPrenume($prenume) {
        $this->prenume = $prenume;
    }

    public function getAccessLevel() {
        return $this->access_level;
    }

    public function setAccessLevel($access_level) {
        $this->access_level = $access_level;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getRealPassword() {
        return $this->real_password;
    }

    public function setRealPassword($pssword) {
        $this->real_password = $pssword;
    }

    public function getCompanyDetails() {
        return $this->company;
    }

    public function setCompany(Company $company) {
        $this->company = $company;
        $company->setUser($this);
    }

    public function getIterationArray() {

        $iteration = array();
        foreach ($this as $key => $value) {
            if (!is_object($value) || ($value instanceof \DateTime))
                $iteration[$key] = $value;
        }

        //adaugam detaliile
        $company = $this->getCompanyDetails();
        if ($company) {
            $extra = $company->getIterationArray();
            foreach ($extra as $key => $value)
                $iteration[$key] = $value;
        }
        return $iteration;
    }

}

?>
