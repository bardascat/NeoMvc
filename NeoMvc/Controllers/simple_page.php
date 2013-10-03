<?php

/**
 * Description of index
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
class simple_page extends controller {

    private $pageData = array();

    function __construct() {
        $this->parent=parent::getInstance();
        
        $this->parent->initView();
    }

    public function index($params) {

        $page_id=$params[0];
        $page=  Model::getInstance()->select("select * from pages where id='$page_id'",true,0);
        
        $this->parent->view->page=$page;
        
        $this->parent->view->pageName= ucwords(str_replace("-", " ", $page->page_name));
        $this->parent->view->render('simple_page/index');
    }

}

?>
