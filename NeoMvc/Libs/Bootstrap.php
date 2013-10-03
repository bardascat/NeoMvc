<?php

/**
 * Description of Bootstrap
 * @author Bardas Catalin
 * date: Jul 17, 2013
 */

namespace NeoMvc\Libs;

use Doctrine\Common\ClassLoader;
use NeoMvc\Models as Models;

class Bootstrap {

    private $url = null;
    private $controller = "index";
    private $controllers_folder = "controllers/";
    private $method = "index";
    private $params = null;

    /** @var ClassLoader $ClassLoader */
    private $ClassLoader;

    private function urlMapping() {
        switch ($this->url[0]) {
            case "produse": {
                    $this->url[0] = "Products";
                }break;
            case "cupoane": {
                    $this->url[0] = "Offers";
                }break;
            case "cart": {
                    $this->url[0] = "NeoCart";
                }break;
            case "user": {
                    $this->url[0] = "User";
                }break;
            case "cont": {
                    $this->url[0] = "UserAccount";
                }break;
        }
    }

    private function init_app($admin = false, $final = false) {

        if ($admin)
            $controller_file = 'NeoMvc\Controllers\Admin\\' . $this->controller;
        else
            $controller_file = 'NeoMvc\Controllers\\' . $this->controller;
        
        if ($this->ClassLoader->canLoadClass($controller_file)) {
            $controller = new $controller_file();

            if (method_exists($controller, $this->method)) {
                define("CONTROLLER", $this->controller);
                define("METHOD", $this->method);

                $controller->{$this->method}($this->params);

                return true;
            }
        }
        if ($final) {
            $this->pageNotFound();
        }
        return false;
    }

    function __construct() {

        $this->initClassLoader();

        // fara parametri in url, incarca index
        if (isset($_GET['url'])) {
            $this->url = $_GET['url'];
            $this->url = rtrim($this->url, '/');
            $this->url = explode('/', $this->url);
        } else {
            $this->controller = "index";
            $this->init_app();
            return false;
        }


        switch ($this->url[0]) {
            //subfoldersadmin
            case "admin": {

                    $this->controllers_folder = "controllers/admin/";

                    if (isset($this->url[1]))
                        $this->controller = $this->url[1];

                    if (isset($this->url[2]))
                        $this->method = $this->url[2];

                    $this->init_params(3);

                    $this->init_app(true, true);
                }break;
            default: {
                    /* controller->method
                     * 1. Categorii -> subcategorii - > etc
                     * 3. Product slug
                     * 4. Simple page
                     */

                    //mapam denumirea controllerului din url cu clasa ex. cupoane->Offers
                    $this->urlMapping();

                    $this->controller = $this->url[0];

                    if (isset($this->url[1]))
                        $this->method = $this->url[1];

                    $this->init_params();


                    if ($this->init_app()) {
                        return true;
                    }



                    /**
                     * Verificam daca e url ptr categorii
                     */
                    if ($this->check_category()) {
                        return true;
                    }

                    /**
                     * Verificam daca e url ptr categorii
                     */
                    if ($this->check_slug()) {
                        return true;
                    }

                    $this->pageNotFound();
                }break;
        }
    }

    private function initClassLoader() {

        require "NeoMvc/Libs/vendor/autoload.php";
        $this->ClassLoader = new ClassLoader("NeoMvc");
        $this->ClassLoader->register();

        $config = new \NeoMvc\Config\configApp();
    }

    private function check_category() {

        //url format: controller/rootCategory/subCategory

        switch ($this->url[0]) {
            case "Products": {
                    $item_type = "product";
                  
                }break;
            case "Offers": {
                    $item_type = "offer";
                   
                }break;
            default: {

                    return false;
                }
        }

        if (count($this->url) < 2)
            return false;

        $root_category = $this->url[1];

        $CategoriesModel = new Models\CategoriesModel();

        $rootCategoryExists = $CategoriesModel->categoryExists($root_category, $item_type);

        //avem categorie root
        if ($rootCategoryExists) {
            //verificam daca exista subcategorie
            if (isset($this->url[2])) {
                // check for subcat
                $subcat = $this->url[2];
                $subCategoryExists = $CategoriesModel->categoryExists($subcat, $item_type);
                if (!$subCategoryExists)
                    return false;
            }

            $this->method = "category";
            $this->init_params(1);
            $this->init_app(false, true);
            return true;
        }
        else
            return false;
    }

    private function check_slug() {

        $ProductsModel = new Models\ProductsModel();
        $item = $ProductsModel->getItemsBySlug($this->url[0]);

        if ($item) {

            switch ($item->getItem_type()) {
                default: {
                        return false;
                    }break;
                case "product": {
                        $this->controller = "Products";
                        $this->method = "product_page";
                    }break;
                case "offer": {
                        $this->controller = "Offers";
                        $this->method = "offer_page";
                    }break;
            }

            //setam parametrul metodei id-ul itemului
            $this->url[1] = $item->getId_item();
            $this->init_params(1);
            
            $this->init_app(false, true);
            return true;
        }
        else
            return false;
    }

    private function check_simplepage() {
        $page = Model::getInstance()->select("select id from pages where page_name='{$this->url[0]}'", true, 0);
        if ($page) {
            $this->controller = "simple_page";
            $this->method = "index";
            $this->url[0] = $page->id;
            $this->init_params(0);
            $this->init_app();
            return true;
        }
        else
            return false;
    }

    private function pageNotFound() {
        exit("<h1>Page not found</h1>");
    }

    private function init_params($start_from = 2) {
        if (count($this->url) >= $start_from) {
            $k = 0;
            for ($i = $start_from; $i < count($this->url); $i++) {
                $this->params[$k] = $this->url[$i];
                $k++;
            }
        }
    }

}

?>
