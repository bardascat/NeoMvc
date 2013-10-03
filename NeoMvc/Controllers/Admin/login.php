<?php
namespace NeoMvc\Controllers\Admin;
use NeoMvc\Controllers\controller;
/**
 * Description of index
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
class login extends controller {

    function __construct() {

        parent::__construct();

        $this->initView();
        $this->view->pageName = "Home";
        
        if ($this->logged_user) {

            header('Location:' . URL . 'admin');
        }
    }

    public function index() {
        $this->view->render('admin/login',true,-1);
    }

    public function login() {

        if (count($_POST) > 0) {
            
            $user = $this->UserController->login_user($_POST['username'], md5($_POST['password']));
            
            if (!$user || $user->getAccessLevel>1) {
                controller::set_alert_message("Datele introduse sunt incorecte");
                header("Location: " . URL . 'admin/login');
            } else {
                 header("Location: " . URL . 'admin');
            }
        }
        else
            header('Location: ' . URL);
    }


}

?>
