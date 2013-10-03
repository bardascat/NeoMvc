<?php

namespace NeoMvc\Controllers;

use NeoMvc\Models as Models;
use NeoMvc\Models\UserModel;
use NeoMvc\Models\Entity as Entity;
use NeoMvc\Libs\Cookie;

class UserAccount extends controller {

    private $UserModel;

    function __construct() {
        parent::__construct();
        $this->setAccessLevel(self::USER_LEVEL);
        $this->initView();
        $this->view->pageName = "Contul Meu";
        $this->UserModel = new UserModel();
        $this->add_js(array("scripts/source_fancy/jquery.fancybox.js?v=2.0.6"));
        $this->add_css(array("scripts/source_fancy/jquery.fancybox.css?v=2.0.6"));
    }

    public function setari() {
        $this->view->render("cont/setari");
    }

    public function comenzi() {
        $this->view->pageName = "Comenzile tale";
        $this->view->orders = $this->logged_user->getProductOrders();
        $this->view->render("cont/comenzi");
    }

    public function cupoane() {
        $this->view->pageName = "Cupoanele tale";
        $this->view->orders = $this->logged_user->getOfferOrders();
        $this->view->render("cont/cupoane");
    }

    /**
     * @param: $params este un array cu index 0 id-ul orderItem-ului care contine lista de vouchere
     * Genereaza un popup cu lista voucherelor
     */
    public function downloadVouchers($params) {
        if (!isset($params[0])) {
            exit("Page not found");
        }
        $ordersModel = new Models\OrdersModel();
        $orderItem = $ordersModel->getOrderItem($params[0]);
        if (!$orderItem)
            exit("Page not found");
        $this->view->orderItem = $orderItem;
        $this->view->render("popups/vouchersList", false, "popup");
    }

    /**
     * 
     * @param $voucher[0] contine id-ul voucherului ce urmeaza a fi downloadat
     */
    public function downloadVoucher($voucher) {

        header('Content-disposition: attachment; filename=huge_document.pdf');
        header('Content-type: application/pdf');
//        readfile('huge_document.pdf');
    }

}

?>
