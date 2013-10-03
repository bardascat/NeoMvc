<?php

namespace NeoMvc\Controllers\Admin;

use NeoMvc\Controllers\controller;
use NeoMvc\Models\ProductsModel;
use NeoMvc\Models\CategoriesModel;
use NeoMvc\Models\Entity as Entity;
use NeoMvc\Models as Models;
use NeoMvc\Libs\Session;
use NeoMvc\Models\Model;

/**
 * Description of index
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
class product extends controller {

    /**
     *
     * @var ProductsModel $ProductsModel
     */
    private $ProductsModel;
    private $CategoriesModel;
    private $UserModel;

    function __construct() {
        parent::__construct();
        $this->setAccessLevel(self::ADMIN_LEVEL);
        $this->initView();
        $this->view->pageName = "Produse";
        $this->initHeaderFilesAdmin();
        $this->add_js(array("scripts/neoSelectInput/neoSelectInput.js"));
        $this->ProductsModel = new ProductsModel();
        $this->CategoriesModel = new CategoriesModel();
        $this->UserModel = new Models\UserModel();
    }

    public function products_list() {
        $products = $this->ProductsModel->getProducts();
        $this->view->products = $products;
        $this->view->render('admin/product/products_list', true);
    }

    public function add_product() {
        $this->view->pageName = "Adauga produs";

        $this->view->companies = $this->UserModel->getCompaniesList();
        $this->view->tree = $this->CategoriesModel->createCheckboxList("product");
        $this->view->render('admin/product/add_product', true);
    }

    public function addProductDo() {

        if (count($_POST) > 0) {
            $objValidator = $this->validate_product();

            if (!$objValidator->isValid($_POST)) {
                $category = false;

                if ($_POST['categories'][0])
                    $category = $_POST['categories'][0];
                $this->view->form_js = $objValidator->form_js();
                $this->view->tree = $this->CategoriesModel->createCheckboxList("product", false, $category);

                $this->view->companies = $this->UserModel->getCompaniesList();
                $this->view->render('admin/product/add_product', true);
            } else {

                $id = $this->ProductsModel->getNextId("items", "id_item");
                $images = $this->upload_images($_FILES['image'], "application_uploads/items/" . $id);
                $_POST['images'] = $images;

                $this->ProductsModel->addProduct($_POST);

                Session::set_flash_data('form_ok', 'Produsul a fost adaugat');
                header('Location:' . URL . 'admin/product/products_list');

                exit();
            }
        } else {
            header('Location:' . URL . 'admin/product/add_product');
        }
    }

    public function edit_product($param) {

        if (!isset($param[0])) {
            echo " PAGE NOT FOUND";
            exit();
        }
        $this->view->tree = $this->CategoriesModel->createCheckboxList("product", $param[0]);
        $this->view->companies = $this->UserModel->getCompaniesList();

        /* @var $product Entity\Item  */
        $item = $this->ProductsModel->get_product($param[0]);
        $this->view->item = $item;

        $this->populate_form($item);
        $this->view->render('admin/product/edit_product', true);
    }

    public function editProductDo() {

        if (count($_POST) > 0) {
            $id_item = $_POST['id_item'];
            $objValidator = $this->validate_product("edit");
            if (!$objValidator->isValid($_POST)) {
                $this->view->form_js = $objValidator->form_js();
                $this->view->item = $this->ProductsModel->get_product($id_item);
                $this->view->tree = $this->CategoriesModel->createCheckboxList("product", false,$_POST['categories'][0]);
                $this->view->companies = $this->UserModel->getCompaniesList();
                $this->view->render('admin/product/edit_product', true);
            } else {
                $images = $this->upload_images($_FILES['image'], "application_uploads/items/" . $id_item);
                $_POST['images'] = $images;
                $this->ProductsModel->updateProduct($_POST);
                Session::set_flash_data('form_ok', 'Produsul a fost salvat');
                header('Location:' . URL . 'admin/product/edit_product/' . $id_item);
            }
        } else {
            header('Location:' . URL . 'admin/product/eddit_product');
        }
    }

    public function delete_product($param) {
        if (isset($param[0])) {

            $id_product = $param[0];

            $this->ProductsModel->delete_product($id_product);

            controller::set_alert_message("<br/> Produsul a fost sters");

            header("Location: " . $this->getRefPage());
        }
    }

    public function delete_image($param) {
        if (isset($_POST['id_image'])) {
            $data = false;

            $this->ProductsModel->delete_image($_POST['id_image']);

            echo json_encode(array('type' => 'success', 'msg' => 'Produsul a fost sters', 'data' => $data));
        } else {
            echo json_encode(array('type' => 'error', 'msg' => 'Id produs incorect'));
        }
    }

    public function search() {
        if (isset($_POST['query'])) {
            $products_list = $this->ProductsModel->search_products($this->logged_user->id_user, $_POST['query']);
            $this->view->products_list = $products_list;

            $this->view->render('products/products_list');
        }
        else
            echo "PAGE NOT FOUND !";
    }

    public function categories() {
        $this->view->pageName = "Administrare Categorii Produse";


        $menu = $this->CategoriesModel->createAdminList("product");
        $this->view->CategoriesAdminMenu = $menu;
        $this->view->render('admin/product/categories', true);
    }

    /**
     * 
     * Validators
     * 
     * @return \NeoMvc\Libs\Validator
     */
    private function validate_product($type = false) {

        $rules = array(
            "name" => array(
                "require" => true,
                "minlength" => 5
            ),
            "id_company" => array(
                "require" => true,
                "minlength" => 1
            ),
            "description" => array("Require" => true, "minlength" => 10),
            "brief" => array("Require" => true, "minlength" => 10),
            "price" => array("Require" => true, "float" => true),
            "sale_price" => array("Require" => true, "float" => true),
        );
        $messages = array(
            "name" => array(
                "require" => "Nume produs obligatoriu",
                "minlength" => "Nume produs obligatoriu -  5 caractere"
            ),
            "id_company" => array(
                "require" => "Alegeti partenerul",
                "minlength" => "Alegeti partenerul"
            ),
            "description" => array(
                "Require" => "Descriere obligatorie !",
                "float" => "Descriere obligatorie (10)"),
            "brief" => array(
                "Require" => "Scurta descriere obligatorie !",
                "float" => "Scurta Descriere obligatorie (5)"),
            "price" => array(
                "Require" => "Pret obligatoriu",
                "float" => "Pret - tip de date float !")
        );


        $objValidator = new \NeoMvc\Libs\Validator($rules, $messages);

        if (!isset($_POST['categories']))
            $objValidator->addErrorMsg("Alegeti categoria produsului");
        else {
           
            //verificam daca a setat specificatiile
            $specs = $this->CategoriesModel->getSpecifications($_POST['categories'][0]);
            if ($specs) {
                $this->view->specs = $specs;
                foreach ($specs as $spec) {
                    if (strlen($_POST[$spec->getId_specification()]) < 1){
                        if($spec->getType()=="info") $msg="Ati uitat sa setati specificatia";
                        else if($spec->getType()=="filter") $msg="Ati uitat sa setati filtrul";
                        $objValidator->addErrorMsg("$msg: " . $spec->getName());
                    }   
                }
            }
         
        }
        if ($type != "edit")
            if (!isset($_FILES['image']['name'][0]) || !$_FILES['image']['name'][0])
                $objValidator->addErrorMsg("Adaugati cel putin o poza !");

        return $objValidator;
    }

   

    public function getSpecsValues() {
        if (isset($_POST['id_filter'])) {
            
            $id_spec = $_POST['id_filter'];
           
            $specsValues = $this->ProductsModel->getSpecsValues($id_spec);
            $select = '';
            foreach ($specsValues as $specValue) {
                $select.='<option name="' . $specValue->getValue() . '">' . $specValue->getValue() . '</option>';
            }
            echo json_encode($select);
        }
    }

}

