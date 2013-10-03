<?php

/**
 * Description of meta_desc
 * @author Bardas Catalin
 * date: Feb 2, 2012 
 */
class meta_desc {
    /*
     * Informatii:
     * 
     * ex meta description:
  
     */

    public $meta_desc_array = array(
        
        'home' => 'HTML SIMPLIFIED',
        
    );
    
    private static $m_pInstance;
    private function __construct() {
        
    }

    public static function getInstance() {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new meta_desc();
        }
        return self::$m_pInstance;
    }

    public function get_meta_desc($pageName) {


        if (isset($pageName) && isset($this->meta_desc_array))
            if (@array_key_exists($pageName, $this->meta_desc_array)) {
                return $this->meta_desc_array[$pageName];
            } else
                return "";
        else {
            return "";
        }
    }

}

?>
