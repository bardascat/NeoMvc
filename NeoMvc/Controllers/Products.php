<?php

namespace NeoMvc\Controllers;

/**
 * Description of index
 * @author Bardas Catalin
 * date: Jun 28, 2013
 */
use NeoMvc\Models\ProductsModel;
use NeoMvc\Models as Models;
use NeoMvc\Models\Entity as Entity;

class Products extends controller {

    private $ProductModel = null;
    private $CategoriesModel = null;

    function __construct() {
        parent::__construct();

        $this->initView();
        $this->add_js(array("scripts/carouFredSel-6.2.1/jquery.carouFredSel-6.2.1.js"));

        $this->view->pageName = "Home";
        $this->ProductModel = new ProductsModel();
        $this->CategoriesModel = new Models\CategoriesModel();
    }

    public function index() {

        header('Location:' . URL);
    }

    /**
     * Url pattern: produse/categorie/subcategorie
     * Site-ul functioneaza cu n nivele de categorii/subcategorii
     */
    public function category($params) {
        /* @var $mainCategory Entity\Category */
        $mainCategory = $this->CategoriesModel->getCategoryBySlug($params[0]);
        $mainCategoryChilds = $this->CategoriesModel->getChilds($mainCategory->getId_category());
        unset($mainCategoryChilds[0]);

        // Verificam daca subcategoria curenta se gaseste printre subcategoriile categoriei principale, daca nu page not found
        if (isset($params[1])) {
            foreach ($mainCategoryChilds as $child) {
                if ($child[0]->getSlug() == $params[1])
                    $subcategorie = $child;
            }
            if (!isset($subcategorie))
                exit("<h2>Err(1:43) Page not Found</h2>");
            /**
             * Verificam daca subcategoria are si ea subcategorii
             * a) Daca are subcategorii afisam subcategoriile
             * b) Daca nu are subcategorii, atunci e categorie finala => afisam lista de produse din categoria respectiva.
             */
            if ($this->CategoriesModel->hasChilds($subcategorie[0]->getId_category())) {
                $this->initSubcategoriesPage($mainCategory, $subcategorie);
            }
            /*
             * Este subscategorie finala, afisam lista de produse
             */ else {
                $this->initProductsList($maincategory, $subcategorie, $params);
            }
        } else {
            /**
             * Avem doar categorie principala
             * Scoatem din childList, frunzele cu adancime mai mare decat 1
             */
            foreach ($mainCategoryChilds as $key => $child) {
                if ($child['depth'] > 1)
                    unset($mainCategoryChilds[$key]);
            }
            $this->view->childCategories = $mainCategoryChilds;
            $this->view->ParentCategory = $mainCategory;
            $this->view->render("product/category_product");
        }
    }

    private function initSubcategoriesPage($mainCategory, $subcategorie) {
        $this->add_js(array("scripts/columnizer.js"));

        $subCategoryChilds = $this->CategoriesModel->getChilds($subcategorie[0]->getId_category(), 1);
        unset($subCategoryChilds[0]);

        $this->view->childCategories = $subCategoryChilds;

        //Generam numarul de coloane ptr categorii
        $this->view->col = round(count($subCategoryChilds) / 4);

        $this->view->ParentCategory = $mainCategory;
        $this->view->render("product/sub_category_product");
    }

    private function initProductsList($maincategory, $subcategorie, $URLparams) {

        //luam filtrele din URL
        $selectedFilters = $this->getSelectedFilters($URLparams);

        //Luam produsele pe baza categoriei si a filtrelor
        $productsArray = $this->ProductModel->getProductsByCategory($subcategorie[0]->getId_category(), $selectedFilters);
        $this->subcategorie = $subcategorie[0];

        //luam intai toate filtrele din categorie
        $allFilters = $this->CategoriesModel->getDistinctFilters($subcategorie[0]->getId_category());
        //apoi pentru fiecare valoare de filtru verificam cate produse intoarce daca ar fi bifat
        $filters = $this->filterFutureResult($allFilters, $selectedFilters);
        $this->view->filters = $filters;


        $this->view->products = $productsArray['products'];
        $this->view->minPrice = $productsArray['minPrice'];
        $this->view->maxPrice = $productsArray['maxPrice'];
        $this->view->render("product/product_list");
    }

    /**
     * Ne intereseaza pentru fiecare valoare de filtru curenta, cate rezultate am primit daca userul o bifeaza.
     * Logica este ca in cazul in care un filtru daca este bifat si nu intoarce niciun produs sa dispara.
     * Drept urmare pentru fiecare valoare de filtru verificam cate produse intoarce daca aceasta ar fi bifata
     * Metoda are si scopul informativ de a numara cate produse genereaza un filtru daca este bifat.
     */
    private function filterFutureResult($allFilters, $currentSelectedFilters) {
        //scoatem filtrele min si max price
        foreach ($allFilters as $filter) {
            $filterValues = $filter->getSpecificationValues();

            foreach ($filterValues as $value) {
                $newFilters = $currentSelectedFilters;

                //adaugam filtrul care ar putea fi bifat
                unset($newFilters[$filter->getSlug()]);
                $newFilters[$filter->getSlug()][] = $value->getValue();

                //   echo "<pre>";
                // print_r($newFilters);

                $availableProducts = $this->ProductModel->getNrProductsAvailable($this->subcategorie->getId_category(), $newFilters);
                $value->setPriceList($availableProducts['priceList']);
                $value->setNrProductsAvailable($availableProducts['nr_produse']);

                // print_r($availableProducts);
                if (isset($currentSelectedFilters['min_price']) && isset($currentSelectedFilters['max_price']))
                    $value->filterAvailableProductsByPrice($currentSelectedFilters['min_price'][0], $currentSelectedFilters['max_price'][0]);

                //echo "Avem: " . $value->getNrProductsAvailable();
                //echo "</pre><br/><br/><br/";
            }
        }
        //exit();


        return $allFilters;
    }

    /**
     * 
     * Pagina produsului
     */
    public function product_page($params) {
        $this->add_js(array('scripts/cloud-zoom/cloud-zoom.1.0.2.js', "scripts/thumb_scroller/jquery.thumbnailScroller.js", "scripts/scrollto.js", "scripts/jquery.rating.pack.js"));
        $this->add_css(array('scripts/thumb_scroller/jquery.thumbnailScroller.css'));
        $product = $this->ProductModel->get_product($params[0]);
        //Obitnem lista parintilor categoriei pentru breadcrumbs

        $categoryParents = $this->CategoriesModel->getParents($product->getCategory()->getId_category());

        $this->view->breadcrumbs = $categoryParents;

        $this->view->pageName = $product->getName();
        $this->view->item = $product;
        $this->view->render("product/product_page");
    }

    /**
     * Adauga review produsului
     */
    public function onSubmitAddReview() {
        if ($this->logged_user) {

            if (isset($_POST['review']) && isset($_POST['rating'])) {
                $this->ProductModel->addReview($_POST, $this->logged_user);

                $data = ' <div class="review">
                            <div class="user_name">
                                ' . $this->logged_user->getPrenume() . ' 
                                    ' . $this->logged_user->getNume() . '
                            </div>
                            <div class="date">' . date("d-m-Y") . '</div>
                                <div class="date">';

                for ($i = 1; $i <= $_POST['rating']; $i++)
                    $data.='<img src="' . URL . 'images/fill_star.png"/>';

                for ($i = 1; $i <= (5 - $_POST['rating']); $i++)
                    $data.=' <img src="' . URL . 'images/empty_star.png"/>';

                $data.='</div>
                        <div class="clearfix"></div>
                        <div class="info">
                            ' . $_POST['review'] . '
                        </div>
                        </div>';

                echo json_encode(array("type" => "success", "data" => $data));
            }
        } else {
            echo json_encode(array("type" => "error", "msg" => "Trebuie sa fiti logat pentru a posta review-uri"));
            exit();
        }
    }

    /* Metoda apelata din formul view-ului product_list pentru gestionarea Intervalului de pret
     * daca exista min si max price in urlQuery atunci le modifica daca nu le adauga
     */

    public function addFilterPrice($params) {
        $categorie = $params[0];
        $subcategorie = $params[1];

        unset($params[0]); //categorie
        unset($params[1]); //subcategorie
        $foundMinPrice = false;
        $foundMaxPrice = false;

        foreach ($params as $key => $param) {

//daca sunt deja parametri min_price si max_price in url le modificam valorile cu gelenoi din $_GET
            if (strpos($param, "min_price=") !== false) {
                $params[$key] = "min_price=" . $_GET['min_price'];
                $foundMinPrice = true;
            }
            if (strpos($param, "max_price=") !== false) {
                $params[$key] = "max_price=" . $_GET['max_price'];
                $foundMaxPrice = true;
            }
        }
//daca nu sunt le adaugam
        if (!$foundMinPrice) {
            $params[] = "min_price=" . $_GET['min_price'];
        }
        if (!$foundMaxPrice) {
            $params[] = "max_price=" . $_GET['max_price'];
        }

//reconstruim querystringul din params
        $query = "produse/" . $categorie . '/' . $subcategorie;
        foreach ($params as $param) {
            $query.='/' . $param;
        }

        header('Location:' . URL . $query);
        exit();
    }

    /**
     * intoarce un array cu filtrele selectate: Format: array("numeFiltru"=>array("Valori"))
     * @param type $filters
     * @return Array
     */
    private function getSelectedFilters($filters) {

        unset($filters[0]); //categorie
        unset($filters[1]); //subcategorie
        if (!$filters)
            return false;

        $filtersArray = array();
        foreach ($filters as $filter) {
            $arr = explode("=", $filter);
            if (!isset($arr[0]) || !isset($arr[1]))
                exit("<h1>Page not found</h1>");
            $filtersArray[$arr[0]][] = $arr[1];
        }

        return $filtersArray;
    }

}
?>
