<?php

namespace NeoMvc\Controllers;

use NeoMvc\Models\UserModel;
use NeoMvc\Models\Entity as Entity;
use NeoMvc\Libs\Cookie;

class User extends controller {

    private $UserModel;

    function __construct() {
    
        $this->UserModel = new UserModel();
    }

    public function getUser() {
        $userCookie = Cookie::get("loggedIn");

        if (!$userCookie || !isset($userCookie->id_user))
            return false;
        $user = $this->UserModel->getUserByPk($userCookie->id_user);
        return $user;
    }

    public function createUser($data) {
        $user = new Entity\User();

        $user->setEmail($data['email']);
        $user->setPassword(md5($data['password']));

        try {
            $user = $this->UserModel->createUser($user);
            $return = array('type' => 'success', "action" => "create_user", 'data' => array("nume" => $user->getNume(), "prenume" => $user->getPrenume(), "email" => $user->getEmail()));
        } catch (\Exception $e) {
            $return = array('type' => 'error', "action" => "create_user", 'msg' => $e->getMessage());
        }
        return $return;
    }

    public function login_user($username, $password = false) {
        /* @var   $user User  */

        $user = $this->UserModel->find_user($username, $password);
        if ($user) {
            $cookie = (object) array('id_user' => $user->getId_user(), 'email' => $user->getEmail(), 'access_level' => $user->getAccessLevel());
            Cookie::set('loggedIn', $cookie);
            return $user;
        }
        return false;
    }

    /**
     * Form submit handlers
     */
    public function ajax_login_submit() {
        $objValidator = $this->loginValidator();

        if (!isset($_POST['cont_nou'])) {
            //login
            if (!$objValidator->isValid($_POST)) {
                echo json_encode(array("type" => "error", "action" => "login", "msg" => "Datele introduse sunt incorecte"));
                exit();
            }
            $user = $this->login_user($_POST['email'], md5($_POST['password']));

            if (!$user) {
                echo json_encode(array("type" => "error", "action" => "login", "msg" => "Datele introduse sunt incorecte"));
                exit();
            } else {
                echo json_encode(array("type" => "success", "action" => "login", "data" => array("email" => $user->getEmail(), "nume" => $user->getNume(), "prenume" => $user->getPrenume())));
                exit();
            }
        } else {
            $response = $this->createUser($_POST);
            //autologin
            $user = $this->login_user($_POST['email'], md5($_POST['password']));
            echo json_encode($response);
            exit();
        }
    }

    /**
     * Validators
     */
    private function loginValidator() {
        $rules = array(
            "email" => array(
                "email" => true,
                "minlength" => 5
            ),
            "password" => array("Require" => true, "minlength" => 6)
        );
        $messages = array(
            "email" => array(
                "email" => "E-mail invalid",
                "minlength" => "E-mail incorect"
            ),
            "password" => array(
                "Require" => "Parola incorecta(6)!",
                "minlength" => "Parola incorecta(6)")
        );


        $objValidator = new \NeoMvc\Libs\Validator($rules, $messages);


        return $objValidator

        ;
    }

    public function logout() {
        Cookie::destroyCookie('loggedIn');
        header('Location: ' . URL);
    }

}

?>
