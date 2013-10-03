<?php

/**
 * Description of error
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
class error extends controller {

    function __construct() {
        parent::getInstance()->initView();
        parent::getInstance()->view->pageName = "Eroare - Pagina nu a fost gasita";
    }

    public function index($optional = false) {
        parent::getInstance()->view->render('error/index');
    }

}

?>
