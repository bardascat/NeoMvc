<?php

/**
 * 
 * @author Bardas Catalin
 * date: Jun 1, 2013
 */

namespace NeoMvc\Controllers;

use NeoMvc\Controllers as Controllers;
use NeoMvc\Libs\View;
use NeoMvc\Libs as Libs;
use NeoMvc\Libs\SimpleImage;
use NeoMvc\Models\Entity as Entity;
use NeoMvc\Models as Models;

abstract class controller {

    const USER_LEVEL = 3;
    const PARTNER_LEVEL = 2;
    const ADMIN_LEVEL = 1;

    public $globalJs;
    public $globalCss;

    /** @var  View $view  */
    protected $view;

    /** @var  User $UserController  */
    protected $UserController;

    /**  @var Entity\User $logged_user */
    protected $logged_user = false;

    public function __construct() {
		//seteaza un hash cookie pentru shopping cart
        self::setHash();
        $this->UserController = new Controllers\User();
        $this->logged_user = $this->UserController->getUser();
    }

    public function require_login() {

        if (!$this->logged_user) {
            header('Location: ' . URL);
        }
    }

    /**
     * Metoda folosita in controller pentru a specifica levelu de acces
     * 1 => Admin
     * 2=> Partener
     * 3=> User
     */
    public function setAccessLevel($level) {
        if (!$this->logged_user || $this->logged_user->getAccessLevel() > $level) {
            exit("Permission denied");
        }
    }

    public function initView($categories = "product") {
        $this->view = new View();
        $this->pageName = "NeoMvc";
        $this->view->NeoCartModel = new Models\NeoCartModel();
        $this->view->logged_user = $this->logged_user;
        $this->initHeaderFiles();
    }

    private function updateHeaderFiles() {
        $this->view->globalJs = $this->globalJs;
        $this->view->globalCss = $this->globalCss;
    }

    public function initHeaderFiles() {
        
        $this->globalJs = array("scripts/jquery.1.10.min.js", "scripts/global.js", "scripts/customAlert.js");
        $this->globalCss = array("css/main.css", "css/alert/customAlert.css");
        $this->updateHeaderFiles();
    }

    public function add_js($array) {
        foreach ($array as $item) {
            $this->globalJs[] = $item;
        }
        $this->updateHeaderFiles();
    }

    public function add_css($array) {
        foreach ($array as $item) {
            $this->globalCss[] = $item;
        }
        $this->updateHeaderFiles();
    }

    public function initHeaderFilesAdmin() {
        $this->globalJs = array("scripts/jquery.1.10.min.js", "scripts/admin.js", "scripts/jquery_ui/ui-1-10.js", "scripts/ckeditorScripts/ckeditor.js", "scripts/customAlert.js", "scripts/source_fancy/jquery.fancybox.js?v=2.0.6");
        $this->globalCss = array("css/admin.css", "css/alert/customAlert.css", "scripts/jquery_ui/ui-1-10.css", "css/custom-theme/jquery-ui-1.8.17.custom.css", "scripts/source_fancy/jquery.fancybox.css?v=2.0.6");
        $this->updateHeaderFiles();
    }

    protected function upload_images($upload_images, $path, $Entity = "NeoMvc\Models\Entity\ItemImage") {

        $image = new SimpleImage();

        $images = array();
        foreach ($upload_images['tmp_name'] as $tmp_file) {
            if ($Entity)
                $productImage = new $Entity;
            else
                $productImage = array();

            if ($tmp_file != "") {
                if (!is_dir($path))
                    mkdir($path, 0777);

                $photo_name = substr(md5(rand(100, 9999)), 0, 10) . '.jpg';
                move_uploaded_file($tmp_file, $path . '/' . $photo_name);

                //big photo
                $image->load($path . '/' . $photo_name);
                $image->resizePerfect(900, 900);
                $image->save($path . '/' . $photo_name);
                if (is_object($productImage))
                    $productImage->setImage($path . '/' . $photo_name);
                else
                    $productImage['image'] = $path . '/' . $photo_name;

                //thumb
                $image->load($path . '/' . $photo_name);
                $image->resizePerfect(300, 300);
                $image->save($path . '/' . $photo_name . '_thumb');
                if (is_object($productImage)) {
                    $productImage->setThumb($path . '/' . $photo_name . '_thumb');
                } else {
                    $productImage['thumb'] = $path . '/' . $photo_name . '_thumb';
                }
                $images[] = $productImage;
            }
        }
        return $images;
    }

    protected function populate_form($object) {
//repopulate fields
        $js = '<script type="text/javascript"> $(document).ready(function(){';
        $iteration = $object->getIterationArray();

        foreach ($iteration as $key => $value) {

            if (is_object($value)) {
                if (get_class($value) == "DateTime")
                    $value = $value->format("d-m-Y");
            }
            else
                $value = preg_replace('/\s+/', ' ', $value);

            $value = json_encode($value);

            if ($cond && array_key_exists($key, $cond)) {
                switch ($cond[$key]) {
                    case "radio": {
                            $js.='$(":input[value=\'' . $value . '\']").prop("checked", true)';

                            exit();
                        }break;
                }
            } else {
                $js.='$(":input[name= \'' . $key . '\']").val(' . $value . ');';
            }
        }
        $js.='});</script>';

        if (isset($this->view->form_js))
            $this->view->form_js.= $js;
        else
            $this->view->form_js = $js;
    }

    public function getRefPage() {
        $ref = $_SERVER['HTTP_REFERER'];
        return $ref;
    }

    public static function set_alert_message($text) {

        $_SESSION['session_message'] = $text;
    }

    public static function get_alert_message() {

        if (isset($_SESSION['session_message'])) {
            $text = $_SESSION['session_message'];
            $_SESSION['session_message'] = null;

            return $text;
        }
        else
            return false;
    }

    public static function getImageSized($image, $width_p, $height_p) {
        list($width, $height) = @getimagesize($image);
        $sizeArray = array();

        if ($height > $height_p)
            $ratio1 = $height_p / $height;
        else
            $ratio1 = 1;

        if ($width > $width_p)
            $ratio2 = $width_p / $width;
        else
            $ratio2 = 1;
        if ($ratio1 >= $ratio2) {
            $sizeArray[0] = $height * $ratio2;
            $sizeArray[1] = $width * $ratio2;
        }
        if ($ratio2 >= $ratio1) {
            $sizeArray[0] = $height * $ratio1;
            $sizeArray[1] = $width * $ratio1;
        }
        if ($sizeArray[0] > $height)
            $sizeArray[0] = $height;
        if ($sizeArray[1] > $width)
            $sizeArray[1] = $width;
        return $sizeArray;
    }

    /* SLUGS */

    public static function makeSlugs($string, $maxlen = 0) {
        $newStringTab = array();
        $string = strtolower(self::noDiacritics($string));
        if (function_exists('str_split')) {
            $stringTab = str_split($string);
        } else {
            $stringTab = my_str_split($string);
        }

        $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-");
//$numbers=array("0","1","2","3","4","5","6","7","8","9");

        foreach ($stringTab as $letter) {
            if (in_array($letter, range("a", "z")) || in_array($letter, $numbers)) {
                $newStringTab[] = $letter;
//print($letter);
            } elseif ($letter == " ") {
                $newStringTab[] = "-";
            }
        }

        if (count($newStringTab)) {
            $newString = implode($newStringTab);
            if ($maxlen > 0) {
                $newString = substr($newString, 0, $maxlen);
            }

            $newString = self::removeDuplicates('--', '-', $newString);
        } else {
            $newString = '';
        }

        return $newString;
    }

    public function checkSlug($sSlug) {
        if (ereg("^[a-zA-Z0-9]+[a-zA-Z0-9\_\-]*$", $sSlug)) {
            return true;
        }

        return false;
    }

    public static function noDiacritics($string) {
//cyrylic transcription
        $cyrylicFrom = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $cyrylicTo = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia');


        $from = array("Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž");
        $to = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");


        $from = array_merge($from, $cyrylicFrom);
        $to = array_merge($to, $cyrylicTo);

        $newstring = str_replace($from, $to, $string);
        return $newstring;
    }

    public static function removeDuplicates($sSearch, $sReplace, $sSubject) {
        $i = 0;
        do {

            $sSubject = str_replace($sSearch, $sReplace, $sSubject);
            $pos = strpos($sSubject, $sSearch);

            $i++;
            if ($i > 100) {
                die('removeDuplicates() loop error');
            }
        } while ($pos !== false);

        return $sSubject;
    }

    public function curl_post($post_params) {

        $fields_string = "";

//url-ify the data for the POST
        foreach ($post_params as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');


        $URL = "https://www.activare3dsecure.ro/teste3d/cgi-bin/";


        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);



        curl_close($ch);
        print_r($output);
    }

    public function euro_value() {
        require_once('libs/curs_bnr.php');
        $curs = new curs_bnr();
        $euro_value = $curs->get_curs();
        return $euro_value;
    }

    public function convert_lei_euro($lei) {
        $euro = $this->euro_value();
        return round($lei / $euro);
    }

    public static function getHash() {
        if (Libs\Cookie::get('cart_id')) {
            //get cookie id
            $cookie_id = Libs\Cookie::get('cart_id');
            return $cookie_id;
        }
        else
            return false;
    }

    public static function setHash() {
        if (Libs\Cookie::get('cart_id')) {
            $cookie_id = Libs\Cookie::get('cart_id');
            return $cookie_id;
        } else {
            $cookie_id = self::generateHash();
            Libs\Cookie::set('cart_id', $cookie_id);

            return $cookie_id;
        }
    }

    public static function generateHash() {
        return md5(uniqid(microtime()) . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    }

}

?>
