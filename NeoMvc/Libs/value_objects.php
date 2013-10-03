<?php

/**
 * Description of value_object
 * @author Bardas Catalin
 * date: Jan 12, 2012 
 */
class product_vo {

    public $id_user;
    public $id_product;
    public $name;
    public $um;
    public $price;
    public $currency;
    public $tva;
    public $quantity = 1;

    public function __construct() {

        //only works on pdo class

        $this->set_internal_values();
        if (!$this->tva)
            $this->tva = 0;
    }

    public function set_internal_values() {

        $this->pret_fara_tva = $this->pret_fara_tva();
        $this->valoare_tva = $this->valoare_tva();


        $this->pret_fara_tva_total = $this->pret_fara_tva($this->quantity);
        $this->valoare_tva_total = $this->valoare_tva($this->quantity);
    }

    public function get_currency_value() {
        $currency_value = Model::getInstance()->select("select * from currency 
                        where moneda='{$this->currency}'", true, 0);
        if (!$currency_value) {
            NeoErrorHandler::getInstance()->throw_error("CURRENCY VALUE NOT FOUND " . $this->currency, "critical");
        } else {
            return $currency_value->value;
        }
    }

    public function pret_fara_tva($quantity = false) {
        if (!$this->tva) {
            if ($quantity)
                return ($this->price * $quantity);
            else
                return $this->price;
        } else {
            //de scos tvaul din pret
            $intreg = 100 + $this->tva;
            $pret_fara_tva = round((100 * $this->price) / $intreg, 2);
            if ($quantity)
                return ($pret_fara_tva * $quantity);
            else
                return $pret_fara_tva;
        }
    }

    public function valoare_tva($quantity = false) {
        if (!$this->tva)
            return 0;
        else {
            //de scos tvaul din pret
            $intreg = 100 + $this->tva;
            $pret_fara_tva = round((100 * $this->price) / $intreg, 2);
            $tva = $this->price - $pret_fara_tva;
            if ($quantity)
                return ($tva * $quantity);
            else
                return $tva;
        }
    }

}

?>
