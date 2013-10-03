<?php

/**
 * Description of login_model
 * @author Bardas Catalin
 * date: Dec 29, 2011 
 */

namespace NeoMvc\Models;

use Doctrine\ORM\EntityManager;
use NeoMvc\Models\Entity as Entity;

class UserModel extends \NeoMvc\Models\Model {

    function __construct() {
        parent::__construct();
        $this->em = $this->getConnection();
    }

    public function checkEmail($email) {
        $userRep = $this->em->getRepository("Entity:User");
        $user = $userRep->findBy(array("email" => $email));
        if (isset($user[0]))
            return $user[0];
        else
            return false;
    }

    public function createUser(Entity\User $user) {
        $checkEmail = $this->checkEmail($user->getEmail());
        if ($checkEmail) {
            throw new \Exception("Adresa email deja folosita", 1);
        }
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Cauta userul dupa email si parola. Parola trebuie sa fie cripata md5
     * @param type $email
     * @param type $password
     * @return User
     */
    public function find_user($username, $password) {


        $userRep = $this->em->getRepository("Entity:User");

        $user = $userRep->findBy(array("email" => $username, "password" => $password));

        if (isset($user[0]))
            return $user[0];
        else
            return false;
    }

    /**
     * Cauta user dupa id.
     * @param  id int
     * @return    User  Success: Entity/User
     * @return  false   Failed: False
     */
    public function getUserByPk($id) {
        $user = $this->em->find("Entity:User", $id);

        if (!$user)
            return false;
        else
            return $user;
    }

    public function deleteUser($id_user) {

        $dql = $this->em->createQuery("delete from Entity:User u where u.id_user='$id_user'");
        $dql->execute();
        return true;
    }

    /*     * ************************** FUNCTII PARTENER ******************** */

    public function getCompaniesList() {
        $partnerRep = $this->em->getRepository("Entity:User");
        $partnersList = $partnerRep->findBy(array("access_level" => 2), array("id_user" => "desc"));
        return $partnersList;
    }

    public function getCompanyByPk($id_company) {
        $partnerRep = $this->em->getRepository("Entity:User");
        $partner = $partnerRep->findBy(array("access_level" => 2, "id_user" => $id_company));
        if (isset($partner[0]))
            return $partner[0];
        else
            return false;
    }

    public function updateCompany($_POST) {
        /* @var $user Entity\User */
        $user = $this->em->find("Entity:User", $_POST['id_user']);
        $user->postHydrate($_POST);
        $user->setPassword(md5($_POST['real_password']));
        $user->setRealPassword($_POST['real_password']);

        /* @var $company Entity\Company */
        $company = $user->getCompanyDetails();
        $company->postHydrate($_POST);

        
        if (isset($_POST['image']))
            $company->setImage($_POST['image'][0]['image']);

        $this->em->persist($user);
        $this->em->flush();
        return true;
    }

    /*     * ************************ END PARTENER  ******************** */
}

?>
