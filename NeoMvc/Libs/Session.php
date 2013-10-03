<?php
namespace NeoMvc\Libs;
/**
 * Description of Session
 * @author Bardas Catalin
 * date: Dec 29, 2011 
 */
class Session {

    public static function init() {
        @session_start();
    }

    public static function set_flash_data($key,$value){
        $_SESSION[$key] = $value;
       
    }
    public static function get_flash_data($key){
        $data=self::get($key);
        self::unsetS($key);
        return $data;
    }
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    public static function unsetS($key){
        unset($_SESSION[$key]);
    }
    public static function get($key) {
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return false;
    }

    public static function destroy() {
        //unset($_SESSION);
        session_destroy();
    }

}

?>
