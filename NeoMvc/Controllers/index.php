<?php
namespace NeoMvc\Controllers;
/**
 * Description of index
 * @author Bardas Catalin
 * date: Jun 28, 2013
 */
use NeoMvc\Models\ProductsModel;
use NeoMvc\Models as Models;

class index extends controller {

    
    private $productModel = null;

    private $CategoriesModel=null;
    
    function __construct() {
        parent::__construct();
        
        $this->initView();
      
        $this->view->pageName = "Home";
        $this->productModel=new ProductsModel();
      
    }

    public function index() {
        $this->view->render('index/index');
    }


}

?>
