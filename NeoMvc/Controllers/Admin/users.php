<?php

namespace NeoMvc\Controllers\Admin;

use NeoMvc\Controllers\controller;
use NeoMvc\Models as Models;
use NeoMvc\Models\Entity as Entity;
use NeoMvc\Libs\Session;

/**
 * Description of index
 * @author Bardas Catalin
 * date: Dec 28, 2011 
 */
class users extends controller {

    private $CategoriesModel;
    private $UserModel;

    function __construct() {
        parent::__construct();
        $this->setAccessLevel(self::ADMIN_LEVEL);
        $this->initView();
        $this->initHeaderFilesAdmin();
        $this->UserModel = new Models\UserModel();
    }

    public function company_list() {
        $this->view->pageName = "Lista parteneri";
        $companies = $this->UserModel->getCompaniesList();
        $this->view->companies = $companies;
        $this->view->render("admin/users/company_list", true);
    }

    public function add_company() {
        $this->view->pageName = "Adauga Partener";
        $this->view->render("admin/users/add_company", true);
    }

    public function addCompanySubmit() {
        if (count($_POST) > 0) {
            $this->view->pageName = "Adauga Partener";
            $objValidator = $this->validate_company();

            if (!$objValidator->isValid($_POST)) {
                $this->view->form_js = $objValidator->form_js();

                $this->view->render('admin/users/add_company', true);
            } else {

                $user = new Entity\User();

                $user->setEmail($_POST['email']);
                $user->setPassword(md5($_POST['password']));
                $user->setRealPassword(($_POST['password']));
                $user->setPrenume($_POST['prenume']);
                $user->setNume($_POST['nume']);
                $user->setAccessLevel(2); // partener

                $company = new Entity\Company();
                $company->postHydrate($_POST);

                $id = $this->UserModel->getNextId("users", "id_user");
                $images = $this->upload_images($_FILES['image'], "application_uploads/company/" . $id, false);
                $company->setImage($images[0]['image']);

                $user->setCompany($company);

                try {
                    $user = $this->UserModel->createUser($user);
                } catch (\Exception $e) {
                    //email invalid
                    $objValidator->addErrorMsg($e->getMessage());
                    $this->view->form_js = $objValidator->form_js();
                    $this->view->render('admin/users/add_company', true);
                    exit();
                }
                Session::set_flash_data('form_ok', 'Partenerul a fost adaugat');
                header('Location:' . URL . 'admin/users/add_company');

                exit();
            }
        } else {
            header('Location:' . URL . 'admin/users/add_company');
        }
    }

    public function edit_company($params) {
        $user = $this->UserModel->getCompanyByPk($params[0]);
        $this->view->user = $user;
        $this->populate_form($user);
        $this->view->render('admin/users/edit_company', true);
    }

    public function editCompanySubmit() {
        if (isset($_POST['id_user'])) {

            if ($_FILES['image']['name'][0]) {
                $images = $this->upload_images($_FILES['image'], "application_uploads/categories/" . $_POST['id_user'], false);
                $_POST['image'] = $images;
            }
            $this->UserModel->updateCompany($_POST);
            
            Session::set_flash_data('form_ok', 'Partenerul a fost editat');
            header('Location:' . URL . 'admin/users/edit_company/' . $_POST['id_user']);
            exit();
        } else {
            exit("Nu umbla cu smecherii...");
        }
    }

    public function delete_user($params) {

        $this->UserModel->deleteUser($params[0]);

        header('Location:' . $this->getRefPage());
    }

    private function validate_company() {
        $rules = array(
            "email" => array(
                "require" => true,
                "email" => true
            ),
            "company_name" => array(
                "require" => true
            ),
            "latitude" => array(
                "require" => true,
            ),
            "longitude" => array(
                "require" => true,
            ),
            "location" => array(
                "require" => true,
            ),
            "password" => array(
                "require" => true,
                "minlength" => 6
            ),
            "company_name" => array("Require" => true, "minlength" => 5),
        );



        $messages = array(
            "email" => array(
                "require" => "E-mail obligatoriu",
                "email" => "E-mail invalid"
            ),
            "password" => array(
                "require" => "Introduceti parola min 6 caractere",
                "minlength" => "Introduceti parola min 6 caractere"
            ),
            "location" => array(
                "require" => "Introduceti locatia",
            ),
            "company_name" => array(
                "require" => "Introduceti denumirea companiei",
            ),
            "latitude" => array(
                "require" => "Introduceti latitudinea",
            ),
            "longitude" => array(
                "require" => "Introduceti longitudinea",
            ),
            "company_name" => array(
                "Require" => "Nume companie obligatoriu",
                "minlength" => "Nume companie obligatoriu min 5 caractere")
        );

        $objValidator = new \NeoMvc\Libs\Validator($rules, $messages);
        return $objValidator;
    }

}

?>
