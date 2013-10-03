<?php

namespace NeoMvc\Models\Entity;

/**
 * @Entity 
 * @Table(name="item_categories")
 */
class ItemCategories {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $id_item;

    /**
     * @Column(type="integer")
     */
    private $id_category;

    /** @ManyToOne(targetEntity="Category", inversedBy="ItemCategories")
     *  @JoinColumn(name="id_category", referencedColumnName="id_category" ,onDelete="CASCADE")
     *  */
    protected $category;

    /** @ManyToOne(targetEntity="Item", inversedBy="ItemCategories")
     *  @JoinColumn(name="id_item", referencedColumnName="id_item" ,onDelete="CASCADE")
     *  
     */
    protected $item;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId_item() {
        return $this->id_item;
    }

    public function setId_item($id_item) {
        $this->id_item = $id_item;
    }

    public function getId_category() {
        return $this->id_category;
    }

    public function setId_category($id_category) {
        $this->id_category = $id_category;
    }

    public function getCategory() {
        
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getItem() {
        return $this->item;
    }

    public function setItem($item) {
        $this->item = $item;
    }



}

?>
