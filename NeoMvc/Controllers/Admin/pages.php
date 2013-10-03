<?php

/**
 * Description of index
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
class pages extends controller {

    function __construct() {

        $this->parent = parent::getInstance();
        $this->parent->initView();
        $this->parent->initHeaderFilesAdmin();


        $customer = $this->parent->require_admin();
        $this->AdminModel = $this->parent->load_model('admin_model');
    }

    public function index() {

        if (isset($_GET['pageName'])) {
            $this->parent->view->pageText = Model::getInstance()->select("select data from pages where id='{$_GET['pageName']}'",true,0);
        }
        $this->parent->view->pageName="Editeaza pagini";
        parent::getInstance()->view->render('admin/pages', true);
    }

    public function logout() {
        $this->Customer->logout();
    }

    public function edit_page() {
        Model::getInstance()->query("update pages set data='{$_POST['textAreaPage']}' where id='{$_POST['pageName']}'");
        controller::getInstance()->set_alert_message("Pagina a fost salvata");
        header('Location: ' . controller::getInstance()->getRefPage());
    }

}

?>
