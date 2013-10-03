<?php

namespace NeoMvc\Controllers;

/**
 * Description of index
 * @author Bardas Catalin
 * date: Jun 28, 2013
 */
use NeoMvc\Models\ProductsModel;
use NeoMvc\Models as Models;

class Offers extends controller {

    private $OffersModel = null;
    private $CategoriesModel;

    function __construct() {
        parent::__construct();

        $this->initView("offer");

        $this->add_js(array("scripts/source_fancy/jquery.fancybox.js?v=2.0.6"));
        $this->add_css(array("scripts/source_fancy/jquery.fancybox.css?v=2.0.6"));

        $this->view->pageName = "Oferte";
        $this->view->setMenu = "cupoane";
        $this->OffersModel = new Models\OffersModel();
        $this->CategoriesModel = new Models\CategoriesModel();
    }

    public function index() {
        $this->view->items = $this->OffersModel->getOffers();
        $this->view->render('offer/index');
    }

    public function offer_page($params) {
        $this->add_js(array('scripts/cloud-zoom/cloud-zoom.1.0.2.js', "scripts/thumb_scroller/jquery.thumbnailScroller.js", "scripts/scrollto.js"));
        $this->add_css(array('scripts/thumb_scroller/jquery.thumbnailScroller.css'));

        $offer = $this->OffersModel->getOffer($params[0]);
        //Obitnem lista parintilor categoriei pentru breadcrumbs

        $categoryParents = $this->CategoriesModel->getParents($offer->getCategory()->getId_category());
        $this->view->breadcrumbs = $categoryParents;

        $this->view->pageName = $offer->getName();
        $this->view->item = $offer;
        $this->view->render("offer/offer_page");
    }

    public function category($params) {


        $slug = $params[0];

        if (isset($params[1])) {
            $slug = $params[1];
        }
        $category = $this->CategoriesModel->getCategoryBySlug($slug);


        $this->view->items = $this->OffersModel->getOffersByParentCategory($category->getId_category());
        $this->view->render('offer/index');
    }

}

?>
