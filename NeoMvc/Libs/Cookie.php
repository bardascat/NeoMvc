<?php
namespace NeoMvc\Libs;
/**
 * Description of Cookie
 * @author Bardas Catalin
 * date: Jan 4, 2012 
 */
class Cookie {

    public static function set($key, $value) {
        setcookie($key, serialize($value), mktime(0, 0, 0, 12, 31, 2015), '/');
    }

    public static function setAtIndex($key, $index, $value) {
        $temp = Cookie::get($key);
        $temp[$index] = $value;
        Cookie::set($key, $temp);
    }

    public static function removeIndex($key, $index) {
        $temp = Cookie::get($key);
        unset($temp[$index]);
        Cookie::set($key, $temp);
    }

    public static function get($key) {
        if (isset($_COOKIE[$key]))
            return unserialize(stripslashes($_COOKIE[$key]));
        else
            return false;
    }

    public static function destroyCookie($key) {
        if (isset($_COOKIE[$key])) {
            setcookie($key, "false", time() - 3600, '/');
        }
    }

}

?>
