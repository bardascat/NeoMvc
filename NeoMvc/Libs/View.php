<?php

namespace NeoMvc\Libs;

/**
 * Description of View
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
use NeoMvc\Controllers\controller;
use NeoMvc\Controllers\Sessions;

class View {

    private $meta;

    public function getMeta() {
        return $this->meta;
    }

    public function setMeta($meta) {
        $this->meta = $meta;
    }

    function __construct() {
        
    }

    public function render($name, $admin = false, $type = false) {

        if ($type == "popup") {
            require 'views/popup_header.php';
            require 'views/' . $name . '.php';
        } else

        if ($admin == true) {
            require 'views/admin_header.php';
            require 'views/' . $name . '.php';
        } else {

            require 'views/header.php';
            require 'views/' . $name . '.php';
            require 'views/footer.php';
        }

        echo $this->show_notifications();
        exit();
    }

    private function show_notifications() {
        $text = controller::get_alert_message();
        $notification = "";
        if ($text) {
            $notification = '
            <script type="text/javascript">
                $(document).ready(function() {
                    alert("' . $text . '");
                });
            </script>';
        }

        if (isset($this->form_js))
            $notification.=$this->form_js;

        $form_ok = Session::get_flash_data("form_ok");
        if ($form_ok) {
            $notification .= "
            <script type='text/javascript'>
                $(document).ready(function() {
                   $('.content').prepend('<div class=\"ui-state-highlight ui-corner-all\" id=\"validation_highlight\"></div>'); $('#validation_highlight').html('.$form_ok.');
                });
            </script>'";
        }

        return $notification;
    }

    public function write($var) {
        if (isset($_REQUEST[$var]))
            return $_REQUEST[$var];
        else
            return false;
    }

    public function getRomanianDay($day) {
        switch ($day) {
            case "Monday": return "Luni";
                break;
            case "Tuesday": return "Marti";
                break;
            case "Wednesday": return "Miercuri";
                break;
            case "Thursday": return "Joi";
                break;
            case "Friday": return "Vineri";
                break;
            case "Saturday": return "Sambata";
                break;
            case "Sunday": return "Duminica";
                break;
        }
    }

    public function getRomanianMonth($date) {


        $month = date("m", strtotime($date));
        $year = date("Y", strtotime($date));

        switch ($month) {
            case "1": return "Ianuarie - " . $year;
                break;
            case "2": return "Februarie - " . $year;
                break;
            case "3": return "Martie - " . $year;
                break;
            case "4": return "Aprilie - " . $year;
                break;
            case "5": return "Mai - " . $year;
                break;
            case "6": return "Iunie - " . $year;
                break;
            case "7": return "Iulie - " . $year;
                break;
            case "8": return "August - " . $year;
                break;
            case "9": return "Septembrie - " . $year;
                break;
            case "10": return "Octombrie - " . $year;
                break;
            case "11": return "Noimebrie - " . $year;
                break;
            case "12": return "Decembrie - " . $year;
                break;
        }
    }

    /**
     * Oringo functions
     */
    public function getHumanPaymentMethod($method) {
        switch ($method) {
            case "card": {
                    echo "Plata Card";
                }break;
            case "op": {
                    echo "Transfer Bancar";
                }break;
            case "ramburs": {
                    echo "Plata Ramburs";
                }break;
            default: {
                    echo "<span style='color:#F00'>Eroare 3:29: Contactati adminul</span>";
                }break;
        }
    }

    public function getHumanShippingStatus($status) {
        switch ($status) {
            case "Undelivered": {
                    echo "Nelivrat";
                }break;
            case "Delivered": {
                    echo "<span style='color:#6c9900'>Livrat</span>";
                }break;
            default: {
                    echo "<span style='color:#F00'>Eroare 3:28: Contactati adminul</span>";
                }break;
        }
    }

    public function getHumanPaymentStatus($status) {
        switch ($status) {
            case "W": {
                    echo "In asteptare";
                }break;
            case "F": {
                    echo "<span style='color:#6c9900'>Finalizata</span>";
                }break;
            case "R": {
                    echo "Refund";
                }break;
            case "C": {
                    echo "<span style='color:#F00'>Anulata</span>";
                }break;
            default: {
                    echo $status;
                }break;
        }
    }

    /**
     * Metoda folosite in view pentru gestionarea bifarea/debifarea filtrelor
     * @param \NeoMvc\Models\Entity\CategoryFilters $filter
     * @param \NeoMvc\Models\Entity\FiltersValues $value
     * @return string
     */
    public function checkFilter(\NeoMvc\Models\Entity\Specification $filter, \NeoMvc\Models\Entity\SpecificationsValues $value) {
        $queryString = str_replace('url=', '', $_SERVER['QUERY_STRING']);
        $filters = explode("/", $queryString);
        $filters = $this->getSelectedFilters($filters);

        if (isset($filters[$filter->getSlug()])) {
            foreach ($filters[$filter->getSlug()] as $filterValue) {
                if ($filterValue == $value->getValue()) {
                    return "checked='checked'";
                }
            }
        }

        return "";
    }

    /**
     * Metoda folosita sa seteze price range-ul in inputuri
     * @param type $min
     * @param type $max
     */
    public function getSelecedPrice($min = false, $max = false) {
        $queryString = str_replace('url=', '', $_SERVER['QUERY_STRING']);
        $filters = explode("/", $queryString);

        $filters = $this->getSelectedFilters($filters);

        if (isset($filters['min_price']) && isset($filters['max_price'])) {
            return $filters['min_price'][0] . ',' . $filters['max_price'][0];
        }
        else
            return $min . "," . $max;
    }

    /**
     * Metoda folosita sa construiasca queryStringul din filtre, adauga sau scoate query-ul in functie de check uncheck
     * @param \NeoMvc\Models\Entity\CategoryFilters $filter
     * @param \NeoMvc\Models\Entity\FiltersValues $value
     * @return String
     */
    public function getFilterUrl(\NeoMvc\Models\Entity\Specification $filter, \NeoMvc\Models\Entity\SpecificationsValues $value) {
        $queryString = str_replace('url=', '', $_SERVER['QUERY_STRING']);
        $filters = explode("/", $queryString);
        $filtersArray = $this->getSelectedFilters($filters);


        $updatedQuery = "";
        if (isset($filtersArray[$filter->getSlug()])) {
            foreach ($filtersArray[$filter->getSlug()] as $key => $filterValue) {
                if ($filterValue == $value->getValue()) {
                    /* scoatem filtrul curent si reconstruim url query */
                    unset($filtersArray[$filter->getSlug()][$key]);
                    foreach ($filtersArray as $name => $values) {
                        foreach ($values as $value2) {
                            $updatedQuery.='/' . $name . '=' . $value2;
                        }
                    }
                    return URL . $filters[0] . '/' . $filters[1] . '/' . $filters[2] . $updatedQuery;
                }
            }
        }

        return URL . $queryString . '/' . $filter->getSlug() . '=' . $value->getValue();
    }

    public function hasFiltersSelected() {
        $queryString = str_replace('url=', '', $_SERVER['QUERY_STRING']);
        $filters = explode("/", $queryString);
        if (count($filters) > 3)
            return true;
        else
            return false;
    }

    public function removeFilters() {
        $queryString = str_replace('url=', '', $_SERVER['QUERY_STRING']);
        $filters = explode("/", $queryString);

        echo URL . $filters[0] . '/' . $filters[1] . '/' . $filters[2];
    }

    private function getSelectedFilters($filters) {
        unset($filters[0]); //controller name
        unset($filters[1]); //categorie
        unset($filters[2]); //subcategorie

        $filtersArray = array();

        if ($filters)
            foreach ($filters as $filter) {
                $arr = explode("=", $filter);
                if (!isset($arr[0]) || !isset($arr[1]))
                    exit("<h1> $filter Page2 not found</h1>");
                $filterArray = array("name" => $arr[0], "value" => $arr[1]);
                $filtersArray[$arr[0]][] = $arr[1];
            }

        return $filtersArray;
    }

}

?>
