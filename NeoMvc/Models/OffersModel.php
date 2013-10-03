<?php

namespace NeoMvc\Models;

/**
 * Description of login_model
 * @author Bardas Catalin
 * date: Dec 29, 2011 
 */
use Doctrine\ORM\EntityManager;
use NeoMvc\Models\Entity as Entity;
use NeoMvc\Models as Models;

class OffersModel extends \NeoMvc\Models\Model {

    /**
     * @var Model $Model 
     */
    protected $Model;

    function __construct() {
        $this->em = $this->getConnection();
    }

    public function addOffer($_POST) {
        $item = new Entity\Item();
        $item->setName($_POST['name']);
        $item->setSlug(\NeoMvc\Controllers\controller::makeSlugs($_POST['name']));
        $item->setItem_type("offer");

        foreach ($_POST['images'] as $image)
            $item->addImage($image);

        foreach ($_POST['categories'] as $category) {
            $category = $this->em->find("Entity:Category", $category);
            $categoryReference = new Entity\ItemCategories();
            $categoryReference->setCategory($category);
            $item->addCategory($categoryReference);
        }


        //cream obiectul offer
        $offer = new Entity\Offer();
        $offer->postHydrate($_POST);
        $item->setOffer($offer);

        //asociem partenerul
        $company = $this->em->find("Entity:User", $_POST['id_company']);
        $item->setCompany($company);

        $this->em->persist($item);

        $this->em->flush();

        return true;
    }
    
        public function updateOffer($_POST) {

        $item = $this->getOffer($_POST['id_item']);
        $item->postHydrate($_POST);
        $item->getOffer()->postHydrate($_POST);

        if (isset($_POST['images']))
            foreach ($_POST['images'] as $image)
                $item->addImage($image);

        //setam imaginea principala
        if (isset($_POST['primary_image'])) {

            $this->em->createQuery("update Entity:ItemImage p set p.primary_image=null where p.primary_image is not null and p.id_item=:id_item")
                    ->setParameter(":id_item", $_POST['id_item'])
                    ->execute();

            $this->em->createQuery("update Entity:ItemImage p set p.primary_image=1 where p.id_image=:id_image")
                    ->setParameter(":id_image", $_POST['primary_image'])
                    ->execute();
        }

        //stergem asocirerile si le inseram din nou in caz de update
        $this->em->createQuery("delete  Entity:ItemCategories c where c.id_item=:id_item")
                ->setParameter(":id_item", $_POST['id_item'])
                ->execute();

        foreach ($_POST['categories'] as $category) {
            $category = $this->em->find("Entity:Category", $category);
            $categoryReference = new Entity\ItemCategories();
            $categoryReference->setCategory($category);
            $item->addCategory($categoryReference);
        }

        $this->em->persist($item);
        $this->em->flush();
        return true;
    }




    public function getOffers() {

        $productsRep = $this->em->getRepository("Entity:Item");
        $products = $productsRep->findBy(array("item_type" => "offer"), array("id_item" => "DESC"));
        return $products;
    }

    public function getOffer($id_offer) {
        $offer = $this->em->find("Entity:Item", $id_offer);
        return $offer;
    }

    /**
     * Intoarce lista de oferte ce sunt stric in cateoria respectiva
     * @param type $id_category
     * @return Item
     */
    public function getOffersByCategory($id_category) {
        $dql = $this->em->createQuery("select items from Entity:Item items join items.ItemCategories c where c.id_category=:id_category and items.item_type='offer'");
        $dql->setParameter(":id_category", $id_category);

        $result = $dql->getResult();

        if (count($result) < 0)
            return false;
        else
            return $result;
    }

    /**
     * 
     * Intoarce lista de oferte ce sunt in categoria cautata sau in subcategorii
     * @param type $id_category
     * @return boolean
     */
    public function getOffersByParentCategory($id_category) {
        $categoriesModel = new Models\CategoriesModel();
        $childs = $categoriesModel->getChilds($id_category);

        $in = "";
        foreach ($childs as $child) {
            $in.=$child[0]->getId_category() . ',';
        }

        $in=substr($in,0,-1);
        
        $dql = $this->em->createQuery("select items from Entity:Item items join items.ItemCategories c where  c.id_category in ($in)");

        
        $result = $dql->getResult();
        return $result;
    }

    public function deleteOffer($id_offer) {
        $product = $this->em->getReference("Entity:Item", $id_offer);
        $this->em->remove($product);
        $this->em->flush();
    }

    public function delete_image($id_image) {
        $image = $this->em->getReference("Entity\ProductImage", $id_image);
        $this->em->remove($image);
        $this->em->flush();
    }

}

?>
