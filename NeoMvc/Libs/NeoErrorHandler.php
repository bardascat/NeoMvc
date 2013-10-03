<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NeoErrorHandler
 *
 * @author Neo
 */
class NeoErrorHandler {

    private static $m_pInstance;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new NeoErrorHandler();
        }
        return self::$m_pInstance;
    }

    public function throw_error($msg, $type = false) {

        //handler error
        exit($msg);
    }

}

?>
