<?php

namespace NeoMvc\Models\Entity;

/**
 * @Entity 
 * @Table(name="items_reviews")
 */
use Doctrine\Common\Collections\ArrayCollection;
use NeoMvc\Models\Entity\AbstractEntity;

class ItemReviews extends AbstractEntity {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    protected $id_review;

    /**
     *
     * @Column(type="integer") @var string 
     */
    protected $id_item;


    /**
     * @ManyToOne(targetEntity="Item",inversedBy="Reviews",cascade={"persist"})
     * @JoinColumn(name="id_item", referencedColumnName="id_item" ,onDelete="CASCADE")
     */
    private $item;
    
    /**
     * @ManyToOne(targetEntity="User",inversedBy="ItemReviews",cascade={"persist"})
     * @JoinColumn(name="id_user", referencedColumnName="id_user" ,onDelete="CASCADE")
     */
    private $user;

    /**
     *
     * @Column(type="text")
     */
    protected $comment;

    /**
     *
     * @Column(type="integer")
     */
    protected $rating;

    /**
     *
     * @Column(type="datetime")
     */
    protected $date;

    function __construct() {
        $this->date = new \DateTime("now");
    }

    public function getDate() {
        return $this->date->format("d-m-Y");
    }

        public function getId_review() {
        return $this->id_review;
    }

    public function setId_review($id_review) {
        $this->id_review = $id_review;
    }

    public function getItem() {
        return $this->item;
    }

    public function setItem($item) {
        $this->item = $item;
    }

    public function getComment() {
        return $this->comment;
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public function getRating() {
        return $this->rating;
    }

    public function setRating($rating) {
        $this->rating = $rating;
    }
    
    public function getId_item() {
        return $this->id_item;
    }

    public function setId_item($id_item) {
        $this->id_item = $id_item;
    }
    
    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }





}

?>
