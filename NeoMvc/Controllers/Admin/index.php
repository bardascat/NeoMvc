<?php
namespace NeoMvc\Controllers\Admin;
use NeoMvc\Controllers\controller;
/**
 * Description of index
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
class index extends controller {

    function __construct() {
        parent::__construct();
        
        
        $this->setAccessLevel(self::ADMIN_LEVEL);
        
        $this->initView();
        
        $this->initHeaderFilesAdmin();

        $this->view->pageName = "Admin";
    }

    public function index() {
        $this->view->render('admin/index', true);
    }

    public function logout() {
        $this->UserController->logout();
    }

}

?>
