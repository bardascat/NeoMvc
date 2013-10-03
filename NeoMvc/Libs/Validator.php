<?php

namespace NeoMvc\Libs;

/**
 * @author M H Rasel
 * 3-5-2009::0:38
 * @copyright 2009
 */
class Validator {

    var $_errmessages;
    var $_rules;
    var $_errors;
    var $_messages;
    var $_defauleErrorMessage;
    var $_defaultDateFormat;
    var $_data;
    var $_curElement;

    function Reset() {
        $this->_errormessages = array();
        $this->_rules = array();
        $this->_messages = array();
        $this->_defauleErrorMessage = array();
    }

    function setDefaultDateFormat($format) {
        $this->_defaultDateFormat = $format;
    }

    function __construct($rules = "", $messages = "") {
        $this->Reset();
        $this->setDefaultDateFormat("mmddyy");
        if (is_array($rules))
            $this->_rules = $rules;
        if (is_array($messages))
            $this->_errmessages = $messages;
    }

    function addRules($rules, $reset = false) {
        if ($reset)
            $this->Reset();
        $this->_rules = array_merge($this->_rules, $rules);
    }

    function addErrors($errors, $reset = false) {
        if ($reset)
            $this->Reset();
        $this->_errmessages = array_merge($this->_errmessages, $errors);
    }

    function isValid($data) {
        $valid = true;
        $this->_data = $data;

        foreach ($this->_rules as $field => $rule) {

            if (isset($rule["type"]) && ($rule["type"] == "radio") || (@$rule["type"] == "checkbox")) {

                if (!isset($data[$field]))
                    $data[$field] = "";
            }
        }
        //print_r($data);

        foreach ($data as $element => $value) {
            if (isset($this->_rules[$element])) {
                if (!$this->validate($element, $value))
                    $valid = false;
            }
        }
        //Updated Catalin
        if ($this->extra_errors)
            $valid = false;

        return $valid;
    }

    private $js = null;

    public function form_js() {


        $this->js = '<script type="text/javascript"> $(document).ready(function(){';
        $errors_html = "";
        $error_fields = $this->ErrorFields();
        foreach ($error_fields as $key => $field) {
            // collect error for each error full field
            $errors = $this->getErrors($field);
            if (is_array($errors))
                $errors_html.="<span>$errors[0]</span>";
            else
                $errors_html.="<span>$errors[0]</span>";
        }

        //repopulate fields
        foreach ($this->_data as $field => $value) {
            $this->_data[$field] = preg_replace('/\s+/', ' ', $this->_data[$field]);


            $this->_data[$field] = json_encode($this->_data[$field]);

            $this->js.='$(":input[name= \'' . $field . '\']").val(' . $this->_data[$field] . ');';
        }

        //append manual errors
        $errors_html.=$this->extra_errors;

        $this->js.=" $('.content').prepend('<div class=\"ui-state-error ui-corner-all\" id=\"validation_errors\"></div>'); $('#validation_errors').html('$errors_html');";
        $this->js.='});</script>';

        return $this->js;
    }

    private $extra_errors = null;

    public function addErrorMsg($msg) {
        $this->extra_errors.="<span>$msg</span>";
    }

    function validate($element, $value) {
        $this->_curElement = $element;
        $rules = $this->_rules[$element];

        if (isset($this->_errmessages[$element]))
            $errormessage = $this->_errmessages[$element];

        if (is_array($rules)) {

            $valid = true;
            $curErr = array();
            foreach ($rules as $rule => $con) {

                if (!$this->check($rule, $value, $con)) {
                    $valid = false;
                    if (isset($errormessage[$rule]))
                        $curErr[] = $errormessage[$rule];
                    else {
                        $curErr[] = $this->DefaultErrorMsg($rule, $element);
                    }
                }
            }

            if (!$valid && !isset($this->_messages[$element]))
                $this->_messages[$element] = $curErr;
            return $valid;
        }
        else {
            if (!$this->check($rules, $value)) {
                if (isset($errormessage))
                    $this->_messages[$element] = $errormessage;
                else
                    $this->_messages[$element] = $this->DefaultErrorMsg($rules);
                return false;
            }
            else
                return true;
        }
    }

    function isint($number) {
        $text = (string) $number;
        $textlen = strlen($text);
        if ($textlen == 0)
            return 0;
        for ($i = 0; $i < $textlen; $i++) {
            $ch = ord($text{$i});
            if (($ch < 48) || ($ch > 57))
                return 0;
        }
        return 1;
    }

    function check($rule, $value, $condition = true) {

        switch (strtolower($rule)) {
            case "require" : return !(trim($value) == "");  //	
            case "maxlength" : return (strlen($value) <= $condition); //		
            case "minlength" : return (strlen($value) >= $condition); //	
            case "eqlength" : return (strlen($value) == $condition); //		
            case "equal" : return ($value == $condition); //
            case "numeric" : return is_numeric($value); //
            case "int" : return $this->isint($value);
            /*                                    if (preg_match("/^0+$/", $value)) 
              return true;
              $v = (int) $value;
              return ((string)$v===(string)$value);
             */
            case "float" : $v = (float) $value;
                return ((string) $v === (string) $value);

            case "min" : if ($value < $condition)
                    return false; break;
            case "max" : if ($value > $condition)
                    return false; break;
            case "gt" : if ($value < $condition)
                    return false; break;
            case "lt" : if ($value > $condition)
                    return false; break;
            case "email" :
                return preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9]+$/", $value, $m);
            case "type" : return true;
            default:
                if (method_exists($this, $rule)) {
                    return @call_user_method($rule, $this, $condition, $value);
                }
                else
                    die("Error: rule '" . $rule . "' not found");
        }
        return true;
    }

    function DefaultErrorMsg($rule, $element = false) {
        $element = "<b>" . ucfirst($element) . "</b>";
        switch (strtolower($rule)) {
            case "require" : return $element . "  este obligatoriu";        //    
            case "maxlength" : return $element . " Over Length"; //        
            case "minlength" : return $element . "  este obligatoriu";    //    
            case "eqlength" : return $element . " Length Mismatch"; //        
            case "equal" : return $element . " Data Mismatch"; //
            case "numeric" : return $element . " Numeric Value Require"; //
            case "int" : return $element . " Tip de date int necesar.";
            case "float" : return $element . " Tip de date float necesar";
            case "gt" :
            case "min" : return $element . " Too small";
            case "lt" :
            case "max" : return $element . " Too high";
            case "date" : return $element . " Data incorecta";
            case "email" : return $element . " Adresa email incorecta";
            default : return "error";
        }
        return true;
    }

    function CountError() {
        return count($this->_messages);
    }

    function ErrorFields() {
//      print_r($this->_errmessages);
        return array_keys($this->_messages);
    }

    function getErrors($element) {
        return $this->_messages[$element];
    }

    function date($con, $value) {

        if (date('Y-m-d', strtotime($value)) == $value)
            return true;
        if (date('d-m-Y', strtotime($value)) == $value)
            return true;
        
        return false;
        
    }

    function depend($con, $value) {

        if ($this->check("require", $this->_data[$con['depend_on']])) {

            $valid = true;
            $curErr = array();
            foreach ($con as $rule => $con) {
                if ($rule != 'depend_on')
                    if (!$this->check($rule, $value, $con)) {
                        $valid = false;
                        if (isset($errormessage[$rule]))
                            $curErr[] = $errormessage[$rule];
                        else
                            $curErr[] = $this->DefaultErrorMsg($rule);
                    }
            }
            if (!$valid)
                $this->_messages[$this->_curElement] = $curErr;
            return $valid;
        }
        else
            return true;
    }

}

//$vv = new Validator();
//$vv->check("email","nightbd@yahoo.com");
?>