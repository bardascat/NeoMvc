<?php

namespace NeoMvc\Models\Entity;

/**
 * @Entity 
 * @Table(name="items")
 */
use Doctrine\Common\Collections\ArrayCollection;
use NeoMvc\Models\Entity\AbstractEntity;

class Item extends AbstractEntity {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    protected $id_item;

    /**
     *
     * Column(type="integer") @var string 
     */
    protected $id_user;

    /**
     *
     * @Column(type="string") @var string 
     */
    protected $name;

    /**
     *
     * @Column(type="string") @var string 
     */
    protected $slug;

    /**
     *
     * @Column(type="datetime") @var float
     */
    protected $createdDate;

    /**
     *
     * @Column(type="string") @var float
     */
    protected $item_type;

    /**
     *
     * @Column(type="integer") @var float
     */
    protected $active = 1;

    /** @OneToMany(targetEntity="ItemCategories", mappedBy="item",cascade={"persist","merge"}) */
    protected $ItemCategories;

    /** @OneToMany(targetEntity="ItemReviews", mappedBy="item",cascade={"persist"}) */
    protected $reviews;


    /** @OneToMany(targetEntity="SpecificationsValues", mappedBy="item",cascade={"persist"}) */
    protected $SpecificationsValues;

    /**
     * @OneToMany(targetEntity="ItemImage",mappedBy="item",cascade={"persist","merge"})
     * @OrderBy({"primary_image" = "desc","id_image"="desc"})
     */
    protected $images;

    /**
     * @ManyToOne(targetEntity="User",inversedBy="items")
     * @JoinColumn(name="id_user", referencedColumnName="id_user" ,onDelete="CASCADE")
     */
    protected $company;



    public function __construct() {
        $this->createdDate = new \DateTime("now");
        $this->SpecificationsValues = new ArrayCollection();

        $this->images = new ArrayCollection();
        $this->ItemCategories = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getCreatedDate() {
        return $this->createdDate->format("d-m-Y");
    }

    public function getIdItem() {
        return $this->id_item;
    }

    public function setIdItem($id_item) {
        $this->id_item = $id_item;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function addSpecValue(SpecificationsValues $specValues) {
        $this->SpecificationsValues->add($specValues);
        $specValues->setItem($this);
    }

    public function addImage(ItemImage $image) {
        $image->setProduct($this);
        if (!($this->images instanceof ArrayCollection))
            $this->images = new ArrayCollection ();

        $this->images->add($image);
    }

    public function addReview(ItemReviews $review) {
        $this->reviews->add($review);
        $review->setItem($this);
    }

    public function getReviews() {
        return $this->reviews;
    }

    public function addCategory(ItemCategories $itemCategories) {
        $itemCategories->setItem($this);
        $this->ItemCategories->add($itemCategories);
    }

    public function getCategory() {
        if (count($this->ItemCategories) < 1)
            return false;

        $firstCategory = $this->ItemCategories[0];
        $category = $firstCategory->getCategory();
        return $category;
    }

    public function getImages() {
        return $this->images;
    }

    public function getItem_type() {
        return $this->item_type;
    }

    public function setItem_type($item_type) {
        $this->item_type = $item_type;
    }

    public function getId_item() {
        return $this->id_item;
    }

    public function setId_item($id_item) {
        $this->id_item = $id_item;
    }

    public function getId_user() {
        return $this->id_user;
    }

    public function setId_user($id_user) {
        $this->id_user = $id_user;
    }

    public function getCompany() {
        return $this->company;
    }

    public function setCompany($company) {
        $this->company = $company;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }


    /**
     * 
     * Intoarce id-ul din tabelul specifications_values, in functie de id_filtrului.
     * Necesar in metoda de update a produsului
     */
    public function getSpecValueId($id_spec) {
        $id_filter_value = "";
        foreach ($this->SpecificationsValues as $specValue) {
            if ($specValue->getId_specification() == $id_spec)
                $id_value = $specValue->getId();
        }
        return $id_value;
    }

    /**
     * 
     * Metoda folosita pentru a repopula inputurile din forms
     */
    public function getIterationArray() {

        $iteration = array();
        foreach ($this as $key => $value) {
            if (!is_object($value) || ($value instanceof \DateTime))
                $iteration[$key] = $value;
        }

        //adaugam detaliile
        $ItemDetails = $this->getItemDetails();
        $extra = $ItemDetails->getIterationArray();
        foreach ($extra as $key => $value)
            $iteration[$key] = $value;

        //adaugam compania
        $company = $this->getCompany();
        $iteration['id_company'] = $company->getId_user();


        //adaugam specificatiile
        $specs = $this->SpecificationsValues;

        if ($specs)
            foreach ($specs as $spec) {

                $iteration[$spec->getId_specification()] = $spec->getValue();
            }

        $iteration['description']['ckeditor'] = 'true';

        return $iteration;
    }

}

?>
