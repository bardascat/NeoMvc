<?php

namespace NeoMvc\Models\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity 
 * @Table(name="specifications")
 */
class Specification {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    private $id_specification;

    /**
     *
      @Column(type="integer")
     */
    private $id_category;

    /**
     * @ManyToOne(targetEntity="Category",inversedBy="specification")
     * @JoinColumn(name="id_category", referencedColumnName="id_category" ,onDelete="CASCADE")
     */
    private $category;

    /** @OneToMany(targetEntity="SpecificationsValues", mappedBy="specification",cascade={"persist"}) */
    private $SpecificationValues;

    /**
     * @Column(type="string")
     */
    protected $name;
    /**
     * @Column(type="string",nullable=true)
     */
    protected $title;

    /**
     * @Column(type="string")
     */
    protected $slug;
    
    //type paote fi filter sau info. 
    /**
     * @Column(type="string")
     */
    protected $type="info";

    public function getId_specification() {
        return $this->id_specification;
    }

    public function setId_specification($id_specification) {
        $this->id_specification = $id_specification;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {

        $this->name = $name;
        $this->slug = \NeoMvc\Controllers\controller::makeSlugs($this->name);
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }
    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getSpecificationValues() {
        return $this->SpecificationValues;
    }

    public function setSpecificationValues($SpecificationValues) {
        $this->SpecificationValues = $SpecificationValues;
    }



}

?>
