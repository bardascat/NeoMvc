<?php

namespace NeoMvc\Models\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity 
 * @Table(name="company")
 */
class Company {

    /**
     *
     * @Id  @Column(type="integer")
     * @GeneratedValue
     */
    protected $id_company;

    /**
     *
     * @Column(type="string",nullable=false)
     */
    protected $company_name;
    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $website;
    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $phone;

    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $cif;

    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $regCom;

    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $address;


    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $latitude;

    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $longitude;

    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $image;

    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $iban;

    /**
     *
     * @Column(type="string",nullable=true)
     */
    protected $bank;

    /**
     * @OneToOne(targetEntity="User",inversedBy="company")
     * @JoinColumn(name="id_user", referencedColumnName="id_user" ,onDelete="CASCADE")
     */
    protected $user;

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getId_company() {
        return $this->id_company;
    }

    public function setId_company($id_company) {
        $this->id_company = $id_company;
    }

    public function getCompany_name() {
        return $this->company_name;
    }

    public function setCompany_name($company_name) {
        $this->company_name = $company_name;
    }

    public function getCif() {
        return $this->cif;
    }

    public function setCif($cif) {
        $this->cif = $cif;
    }

    public function getRegCom() {
        return $this->regCom;
    }

    public function setRegCom($regCom) {
        $this->regCom = $regCom;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getIban() {
        return $this->iban;
    }

    public function setIban($iban) {
        $this->iban = $iban;
    }

    public function getBank() {
        return $this->bank;
    }

    public function setBank($bank) {
        $this->bank = $bank;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }



    public function getLatitude() {
        return $this->latitude;
    }

    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    public function postHydrate($_POST, $customMap = false) {
        foreach ($_POST as $key => $value) {

            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
            if ($customMap)
                foreach ($customMap as $key => $prop) {
                    $this->$prop = $_POST[$key];
                }
        }
    }

    public function getIterationArray() {
        $iteration = array();
        foreach ($this as $key => $value) {
            if (!is_object($value) || ($value instanceof \DateTime))
                $iteration[$key] = $value;
        }
        return $iteration;
    }

    public function getWebsite() {
        return $this->website;
    }

    public function setWebsite($website) {
        $this->website = $website;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }


}

?>
