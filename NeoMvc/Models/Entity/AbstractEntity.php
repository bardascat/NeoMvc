<?php
namespace NeoMvc\Models\Entity;

abstract class AbstractEntity {

    public function postHydrate($_POST, $customMap = false) {
        foreach ($_POST as $key => $value) {

            if (property_exists($this, $key)) {
                //check for date
                if ($value == date("Y-m-d", @strtotime($value))) {
                    $value = new \DateTime($value);
                }
                $this->$key = $value;
            }
            if ($customMap)
                foreach ($customMap as $key => $prop) {
                    $this->$prop = $_POST[$key];
                }
        }
    }

}

?>
