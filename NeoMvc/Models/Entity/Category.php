<?php

namespace NeoMvc\Models\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity 
 * @Table(name="categories")
 */
class Category {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    private $id_category;

    /** @OneToMany(targetEntity="ItemCategories", mappedBy="category") */
    protected $ItemCategories;


    /** @OneToMany(targetEntity="Specification", mappedBy="category",cascade={"persist"}) */
    protected $specifications;

    /**
     *
     * @Column(type="string") @var string 
     */
    public $name;

    /**
     *
     * @Column(type="string",unique=true) @var string 
     */
    public $slug;

    /**
     * @Column(type="integer",nullable=true) @var string 
     */
    private $id_parent;

    /**
     * @Column(type="string",nullable=true) @var string 
     */
    private $thumb;

    /**
     * @Column(type="string",nullable=true) @var string 
     */
    private $cover;

    /**
     * @Column(type="string") @var string 
     */
    private $item_type;

    function __construct() {
        $this->ItemCategories = new ArrayCollection();
        $this->specifications = new ArrayCollection();
    }

    public function addItemCategory(ItemCategories $itemCategories) {
        $itemCategories->setCategory($this);
        $this->ItemCategories->add($itemCategories);
    }

    public function addSpecification(Specification $spec) {
        $this->specifications->add($spec);
        $spec->setCategory($this);
    }

    public function getSpecifications() {
        return $this->specifications;
    }

    public function getId_category() {
        return $this->id_category;
    }

    public function setId_category($id_category) {
        $this->id_category = $id_category;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getId_parent() {
        return $this->id_parent;
    }

    public function setId_parent($id_parent) {
        $this->id_parent = $id_parent;
    }

    public function getThumb() {
        return $this->thumb;
    }

    public function setThumb($photo) {
        $this->thumb = $photo;
    }

    public function getCover() {
        return $this->cover;
    }

    public function setCover($cover) {
        $this->cover = $cover;
    }

    public function getItem_type() {
        return $this->item_type;
    }

    public function setItem_type($item_type) {
        $this->item_type = $item_type;
    }

    private $filters = array();
    private $specInfo = array();

    public function getFilters() {

        if ($this->filters)
            return $this->filters;

        foreach ($this->specifications as $spec) {
            if ($spec->getType() == "filter")
                $this->filters[] = $spec;
            else
                $this->specInfo[] = $spec;
        }

        return $this->filters;
    }

    public function getSpecInfo() {

        if ($this->specInfo)
            return $this->specInfo;
        foreach ($this->specifications as $spec) {
            if ($spec->getType() == "info")
                $this->specInfo[] = $spec;
            else
                $this->filters[] = $spec;
        }

        return $this->specInfo;
    }

}

?>
