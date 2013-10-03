<?php

namespace NeoMvc\Controllers;

/**
 * Description of index
 * @author Bardas Catalin
 * date: Jun 28, 2013
 */
use NeoMvc\Models\ProductsModel;
use NeoMvc\Models as Models;
use NeoMvc\Libs\Cookie;
use NeoMvc\Libs\NeoMail;
use NeoMvc\Models\Entity as Entity;

class NeoCart extends controller {

    /** @var products_model $Model  */
    private $ProductModel = null;
    private $CategoriesModel = null;
    private $NeoCartModel = null;

    function __construct() {
        parent::__construct();
        $this->initView();
        $this->add_js(array('scripts/placeholder.min.js'));
        $this->view->pageName = "Cos Cumparaturi";
        $this->ProductModel = new ProductsModel();
        $this->NeoCartModel = new Models\NeoCartModel();

        //$this->CategoriesModel=new Models\CategoriesModel();
    }

    public function index() {

        $cart = $this->NeoCartModel->getCart(self::getHash());
        $this->view->cart = $cart;
        $this->view->render('cart/index');
    }

    public function update_quantity() {
        if (!isset($_POST['cartItem']))
            exit("Page not found");

        $this->NeoCartModel->updateQuantity($_POST);
        header("Location:" . URL . 'cart');
    }

    public function deleteCartItem() {
        $this->NeoCartModel->deleteCartItem($_POST['cartItem']);
        header("Location:" . URL . 'cart');
    }

    public function add_to_cart($params) {
        //daca se adauga in cos cu GET
        if ($params[0]) {
            $_REQUEST['id_item'] = $params[0];
            $_REQUEST['quantity'] = 1;
        }

        $hash = self::getHash();
        $cart = $this->NeoCartModel->getCart($hash);

        //hai sa bagam produsul in shopping cart
        $this->NeoCartModel->addToCart($_REQUEST, $cart);

        controller::set_alert_message("<br/>Produsul a fost adaugat in cos");
        header("Location: " . $this->getRefPage());
        exit();
    }

    public function addFriendPopup($params) {
        $this->view->id_item = $params[0];
        $this->view->render("popups/addFriend", false, "popup");
    }

    /**
     * Rol: Adauga in cos item-uri destinate prietenilor. 
     * Observatii: Metoda primesti in POST, numele si email-u prietenilor si id-ul itemului,
     * Cantiatea reprezinta count($_POST['name'])
     */
    public function add_to_cart_friend() {
        $id_item = $_POST['id_item'];
        $this->view->id_item = $id_item;

        $nr_friends = count($_POST['name']);
        $friendsDetails = array();

        //validam daca a completat numele la toti
        for ($i = 0; $i < $nr_friends; $i++) {
            if (strlen($_POST['name'][$i]) < 1 || !filter_var($_POST['email'][$i], FILTER_VALIDATE_EMAIL)) {
                $errors = "Va rugam completati corect datele prietenilor !";
                break 1;
            }
            $friendsDetails[] = array("name" => $_POST['name'][$i], "email" => $_POST['email'][$i]);
        }
        if ($errors) {
            $this->view->errors = $errors;
            $this->view->nr_friends = $nr_friends;
            $this->view->post = $_POST;
            $this->view->render("popups/addFriend", false, "popup");
        }

        //totul e ok aici, salvam item-urile in shopping cart
        //Intai cream tipul de date pentru metoda NeoCartModel\addToCart
        $params['quantity'] = $nr_friends;
        $params['id_item'] = $id_item;
        $params['is_gift'] = true;
        $params['details'] = json_encode($friendsDetails);

        $hash = self::getHash();
        $cart = $this->NeoCartModel->getCart($hash);

        $this->NeoCartModel->addToCart($params, $cart);
        $this->view->errors = "Multumim ! Produsele au fost adaugate in cos.";
        $this->view->render("popups/addFriend", false, "popup");
    }

    /**
     * Payment methods
     */
    public function process_payment() {

        if (!$this->logged_user) {
            exit("Pentru teste este necesar sa fii logat");
        }

        $errors = $this->validateForm($_POST);
        if ($errors) {
            $this->set_alert_message('<br/>' . $errors);
            $cart = $this->NeoCartModel->getCart(self::getHash());
            $this->view->cart = $cart;
            $this->view->post = $_POST;
            $this->view->render('cart/index');
        }

        switch ($_POST['payment_method']) {
            case "card": {
                    exit("Plata cu cardul nu a fost implementata, ptr test foloseste op");
                }break;
            case "op": {
                    $this->processOpPayment();
                }break;
            case "ramburs": {
                    exit("Inca nu e implementata");
                }break;
        }
    }

    private function processOpPayment() {
        /* @var $order Entity\Order */
        $order = $this->NeoCartModel->insertOrder($this->logged_user, $_POST);
        
        
        $body = "Buna ziua " . $this->logged_user->getPrenume() . ",<br/><br/>

                        Multumim ca ati folosit serviciile oringo.ro! Mai jos puteti gasi detaliile legate de comanda dvs:<br/><br/>

                        <b>Numarul comenzii dvs: <span style='color:blue'>".$order->getOrderNumber()."</span><br/>
                        Contul bancar in care trebuie sa efectuati transferul: <span style='color:blue'>RO61 RZBR 0000 0600 1378 8136</span><br/>
                        Banca: <span style='color:blue'>Raiffeisen Bank</span><br/><br/>
                        </b>

                        Transferul trebuie efectuat intr-un termen de 72 de ore de la finalizarea comenzii.<br/>
                        <b>Va rugam sa nu uitati sa mentionati numarul comenzii in detaliile transferului.</b><br/><br/>

                        Multumim si va dorim o zi placuta in continuare,<br/><br/>

                        <b>Echipa Oringo</b><br/>
                        <a href='http://www.oringo.ro'>www.oringo.ro</a><br/>
                        office@oringo.ro<br/>";

        $subject = "Comanda Oringo Finalizata";
        $email = $this->logged_user->getEmail();

        NeoMail::getInstance()->genericMail($body, $subject, $email);
        controller::set_alert_message("Comanda finalizata");
        header('Location:' . URL . 'cart');
        exit();
    }

    /**
     * Momentan principalele verificari sunt:
     * 1. A completat numele beneficiarilor
     * 2. A completat adresa de email a prietenului daca vrea sa o dea cadou
     * 3. A ales metoda deplata
     * Pentru pasul 1 si 2 verificam item-urile din shopping cart,daca sunt oferte 
     * e obligatoriu sa aiba setati beneficiarii
     */
    private function validateForm($_POST) {

        $hash = self::getHash();
        /* @var Entity\NeoCart $cart */
        $cart = $this->NeoCartModel->getCart($hash);

        $cartItems = $cart->getCartItems();

        if (!$cartItems) {
            exit("Error(1:20) Va rugam contactati administratorul: office@oringo.ro");
        }

        foreach ($cartItems as $cartItem) {
            /* @var $cartItem Entity\CartItem */
            /* @var $item Entity\Item */
            $item = $cartItem->getItem();

            if ($item->getItem_type() == "offer") {
                for ($i = 0; $i < $cartItem->getQuantity(); $i++) {
                    if ($cartItem->getIs_gift()) {
                        if (strlen($_POST['name_' . $cartItem->getId()][$i]) < 2 || !filter_var($_POST['email_' . $cartItem->getId()][$i], FILTER_VALIDATE_EMAIL)) {
                            $errors.= "Introduceti  corect datele prietenului !<br/>";
                            break 2;
                        }
                    } else
                    if (strlen($_POST['name_' . $cartItem->getId()][$i]) < 2) {
                        $errors.= "Introduceti datele beneficiarului !<br/>";
                        break 2;
                    }
                }
            }
        }
        //exit();
        if (!isset($_POST['payment_method']))
            $errors.='Alegeti metoda de plata';

        return $errors;
    }

}

?>
