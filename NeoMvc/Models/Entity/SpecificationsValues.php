<?php

namespace NeoMvc\Models\Entity;

/**
 * @Entity 
 * @Table(name="specifications_values")
 */
use Doctrine\Common\Collections\ArrayCollection;
use NeoMvc\Models\Entity\AbstractEntity;

class SpecificationsValues extends AbstractEntity {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     *
     * @Column(type="integer") @var string 
     */
    protected $id_item;

    /**
     *
     * @Column(type="integer") @var string 
     */
    protected $id_specification;

    /**
     *
     * @Column(type="string") @var string 
     */
    protected $slug;

    /**
     *
     * @Column(type="string") @var string 
     */
    protected $value;

    /**
     * @ManyToOne(targetEntity="Item",inversedBy="SpecificationsValues",cascade={"persist"})
     * @JoinColumn(name="id_item", referencedColumnName="id_item" ,onDelete="CASCADE")
     */
    private $item;

    /**
     * @ManyToOne(targetEntity="Specification",inversedBy="SpecificationValues",cascade={"persist"})
     * @JoinColumn(name="id_specification", referencedColumnName="id_specification" ,onDelete="CASCADE")
     */
    private $specification;
    private $nr_products_available = 0;

    function __construct() {
        $this->specification = new ArrayCollection();
    }

    public function getId_item() {
        return $this->id_item;
    }

    public function setId_item($id_item) {
        $this->id_item = $id_item;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId_specification() {
        return $this->id_specification;
    }

    public function setId_specification($id_specification) {
        $this->id_specification = $id_specification;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($spec_slug) {
        $this->slug = $spec_slug;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->slug = \NeoMvc\Controllers\controller::makeSlugs($value);
        $this->value = $value;
    }

    public function getItem() {
        return $this->item;
    }

    public function setItem($item) {
        $this->item = $item;
    }

    public function getSpecification() {
        return $this->specification;
    }

    public function setSpecification($specification) {
        $this->specification = $specification;
    }

    public function addSpecValue(SpecificationsValues $value) {
        $this->specification->add($value);
        $value->setSpecification($this);
    }

    public function getNrProductsAvailable() {
        return $this->nr_products_available;
    }

    public function setNrProductsAvailable($nr_products_available) {
        $this->nr_products_available = $nr_products_available;
    }

    /**
     *
     *  Reprezinta preturile produselor generate de specificatia curenta
     * Are rol in detemirnarea numarului de produse in functie de pret-ul ales de user
     */
    private $PriceList = false;

    public function getPriceList() {
        return $this->PriceList;
    }

    public function setPriceList($PriceList) {
        $this->PriceList = $PriceList;
    }

    /**
     * Metoda necesara pentru a reduce numarul de produse disponibile ale specificatiei curenta, IN FUNCTIE DE PRET
     * @param type $minPrice
     * @param type $maxPrice
     */
    public function filterAvailableProductsByPrice($minPrice, $maxPrice) {

        if ($this->PriceList && $this->nr_products_available) {

            //verificam daca preturile produselor salvate in priceList se incadreaza in intervalul min si max
            $nr_available = 0;
            foreach ($this->PriceList as $price) {
                //echo "Pret:".$price.' MinPrice:'.$minPrice.' Maxprice:'.$maxPrice;
                if ($minPrice <= $price && $price <= $maxPrice) {
                    $nr_available++;
                    //exit("Incrementez");
                }
            }
            //updatam numarul de produse disponibile
            $this->nr_products_available = $nr_available;
        }
    }

}

?>
