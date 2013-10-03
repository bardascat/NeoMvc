<?php

namespace NeoMvc\Models;

/**
 * Description of login_model
 * @author Bardas Catalin
 * date: Dec 29, 2011 
 */
use Doctrine\ORM\EntityManager;
use NeoMvc\Models\Entity as Entity;

class ProductsModel extends \NeoMvc\Models\Model {

    /**
     * @var Model $Model 
     */
    protected $Model;

    function __construct() {
        $this->em = $this->getConnection();
    }

    public function addProduct($_POST) {

        $item = new Entity\Item();
        $item->setName($_POST['name']);
        $item->setSlug(\NeoMvc\Controllers\controller::makeSlugs($_POST['name']));
        $item->setItem_type("product");

        foreach ($_POST['images'] as $image)
            $item->addImage($image);

        $category = $_POST['categories'][0];
        /* @var $category Entity\Category */
        $category = $this->em->find("Entity:Category", $category);
        $categoryReference = new Entity\ItemCategories();
        $categoryReference->setCategory($category);
        $item->addCategory($categoryReference);

//verificam filtrele categoriei
        $specs = $category->getSpecifications();
        if ($specs) {
            foreach ($specs as $spec) {
                if ($_POST[$spec->getId_specification()] == "")
                    exit("Eroare filtrul " . $spec->getName() . ' nu a fost completat !');
                $specValue = new Entity\SpecificationsValues();
                $specValue->setValue($_POST[$spec->getId_specification()]);
                $specValue->setSpecification($spec);
                $item->addSpecValue($specValue);
                $this->em->persist($specValue);
                $this->em->persist($specValue);
            }
        }

//cream obiectul product
        $product = new Entity\Product();
        $product->postHydrate($_POST);
        $item->setProduct($product);

//asociem partenerul
        $company = $this->em->find("Entity:User", $_POST['id_company']);
        $item->setCompany($company);

        $this->em->persist($product);
        $this->em->flush();

        return true;
    }

    public function updateProduct($_POST) {
        /* @var $item Entity\item */
        $item = $this->get_product($_POST['id_item']);
        $item->postHydrate($_POST);
        $item->setSlug(\NeoMvc\Controllers\controller::makeSlugs($_POST['name']));
        $item->getProduct()->postHydrate($_POST);

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

        //daca se modifica categoria se pierd specificatiile
        if ($item->getCategory()->getId_category() != $_POST['categories'][0]) {
            try {
                $this->em->createQuery("delete Entity:SpecificationsValues sv where sv.id_item=:id_item")
                        ->setParameter(":id_item", $item->getIdItem())
                        ->execute();
            } catch (\Doctrine\ORM\Query\QueryException $e) {
                echo "Delete specifications values error: " . $e->GetMessage();
                exit();
            }
        }
        //stergem asocirerile si le inseram din nou
        $this->em->createQuery("delete  Entity:ItemCategories c where c.id_item=:id_item")
                ->setParameter(":id_item", $_POST['id_item'])
                ->execute();

        $category = $this->em->find("Entity:Category", $_POST['categories'][0]);
        $categoryReference = new Entity\ItemCategories();
        $categoryReference->setCategory($category);
        $item->addCategory($categoryReference);




        //Adaugam specificatiile produsului
        $specs = $category->getSpecifications();

        if ($specs) {
            $step = 0;
            foreach ($specs as $spec) {
                if ($_POST[$spec->getId_specification()] == "")
                    exit("Eroare specificatia/filtrul " . $spec->getName() . ' nu a fost completat !');

//daca exista facem update
                if (isset($_POST['id'][$step]) && $_POST['id'][$step] != "") {
                    $specValue = $this->em->find("Entity:SpecificationsValues", $_POST['id'][$step]);
                } else {
                    $specValue = new Entity\SpecificationsValues();
                    $specValue->setSpecification($spec);
                }

                $specValue->setValue($_POST[$spec->getId_specification()]);
                $specValue->setSlug($spec->getSlug());
                $item->addSpecValue($specValue);
                $this->em->persist($specValue);
                $this->em->persist($spec);
                $step++;
            }
        }


        $this->em->persist($item);
        $this->em->flush();
        return true;
    }

    public function getProducts() {

        $productsRep = $this->em->getRepository("Entity:Item");
        $products = $productsRep->findBy(array("item_type" => "product"), array("id_item" => "DESC"));
        return $products;
    }

    public function getItemsBySlug($slug, $item_type = false) {
        $itemsRep = $this->em->getRepository("Entity:Item");
        if ($item_type)
            $items = $itemsRep->findBy(array("item_type" => $item_type, "slug" => $slug), array("id_item" => "DESC"));
        else
            $items = $itemsRep->findBy(array("slug" => $slug), array("id_item" => "DESC"));
        if (isset($items[0]))
            return $items[0];
        else
            return false;
    }

    /**
     * Cauta produse dintr-o anumita categorie in functie de filtrele cerute, maxPrice si minPrice pentru priceSlider
     * Posibil sa fie necesara o optimizare a metodei in viitor
     * @param int $id_category
     * @param array $filters
     * @return Array(Entity:Item,$minPrice,$maxPrice)
     */
    public function getProductsByCategory($id_category, $filters) {
        //$this->em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        $subquery = "(select slug,value from specifications_values where id_item=items.id_item)";
        $sqlProducts = "select items.*
               from items
               join products using(id_item)
               join item_categories using(id_item)
                where id_category='$id_category'";
        $sqlPriceRange = "select max(products.sale_price) as maxPrice, min(products.sale_price) as minPrice
               from items
               join products using(id_item)
               join item_categories using(id_item)
                where id_category='$id_category'";

        //min si max price sunt filtre speciale le salvam sperat

        if (isset($filters['min_price']) && isset($filters['max_price'])) {
            $min_price = $filters['min_price'][0];
            $max_price = $filters['max_price'][0];
            if (!$min_price)
                $min_price = 0;
        }
        unset($filters['min_price']);
        unset($filters['max_price']);

        if ($filters)
            foreach ($filters as $filterName => $filter) {
                //avem mai multe valori ale aceluias filtru
                if (count($filter) > 1) {
                    $and = "and (";
                    $step = 0;
                    foreach ($filter as $filterValue) {
                        if ($step)
                            $and.=" or ";
                        $and.="('$filterName','$filterValue') in " . $subquery;
                        $step++;
                    }
                    $and .= ")";
                    $sqlPriceRange.=$and;
                    $sqlProducts.=$and;
                } else {
                    $sqlPriceRange.=" and  ('$filterName','$filter[0]') in " . $subquery;
                    $sqlProducts.=" and  ('$filterName','$filter[0]') in " . $subquery;
                }
            }

        //Folosim query-ul pentru PriceRange pentru a determina pretul minim si maxim, din selectia actuala
        $priceRange = $this->em->getConnection()->executeQuery($sqlPriceRange)->fetchAll();

        if (is_numeric($min_price) && is_numeric($max_price)) {
            $sqlProducts.=" and products.sale_price>=$min_price and products.sale_price<=$max_price";
        }

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult("Entity:Item", "item");
        $rsm->addFieldResult("item", "id_item", "id_item");
        $rsm->addFieldResult("item", "name", "name");
        $rsm->addFieldResult("item", "created_date", "created_date");
        $rsm->addFieldResult("item", "item_type", "item_type");
        $rsm->addFieldResult("item", "active", "active");
        $rsm->addFieldResult("item", "slug", "slug");


        $query = $this->em->createNativeQuery($sqlProducts, $rsm);

        $result = $query->getResult();

        return array("products" => $result, "minPrice" => $priceRange[0]['minPrice'], "maxPrice" => $priceRange[0]['maxPrice']);
    }

    public function getNrProductsAvailable($id_category, $filters) {
        //nu ne intereseaza momentan filtrele astea
        unset($filters['min_price']);
        unset($filters['max_price']);

        //$this->em->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        $subquery = "(select slug,value from specifications_values where id_item=items.id_item)";

        $sqlProducts = "select products.sale_price
               from items
               join products using(id_item)
               join item_categories using(id_item)
                where id_category='$id_category'";


        if ($filters)
            foreach ($filters as $filterName => $filter) {
                //avem mai multe valori ale aceluias filtru
                if (count($filter) > 1) {
                    $and = "and (";
                    $step = 0;
                    foreach ($filter as $filterValue) {
                        if ($step)
                            $and.=" or ";
                        $and.="('$filterName','$filterValue') in " . $subquery;
                        $step++;
                    }
                    $and .= ")";
                    $sqlPriceRange.=$and;
                    $sqlProducts.=$and;
                } else {
                    $sqlPriceRange.=" and  ('$filterName','$filter[0]') in " . $subquery;
                    $sqlProducts.=" and  ('$filterName','$filter[0]') in " . $subquery;
                }
            }


        $result = $this->em->getConnection()->executeQuery($sqlProducts)->fetchAll();



        if (count($result) > 0) {
            $priceList = array();
            foreach ($result as $item) {
                $priceList[] = $item['sale_price'];
            }
            return (array("nr_produse" => count($result), "priceList" => $priceList));
        }
        else
            return (array("nr_produse" => 0, "priceList" => array()));
    }

    /**
     * 
     * @param type $id_product
     * @return Entity:Entity\Item
     */
    public function get_product($id_product) {
        $product = $this->em->find("Entity:Item", $id_product);
        return $product;
    }

    public function delete_product($id_product) {
        $product = $this->em->getReference("Entity:Item", $id_product);
        $this->em->remove($product);
        $this->em->flush();
    }

    public function delete_image($id_image) {
        $this->em->createQuery("delete Entity:ItemImage img where img.id_image=:id_image")
                ->setParameter(":id_image", $id_image)
                ->execute();
        return true;
    }

    /*
      public function getFilterValues($id_filter) {
      $rep = $this->em->getRepository("Entity:FiltersValues");
      $values = $this->em->createQueryBuilder()
      ->select("f")
      ->from("Entity:FiltersValues", "f")
      ->where('f.id_filter=:id_filter')
      ->setParameter(":id_filter", $id_filter)
      ->groupBy("f.value")
      ->getQuery();
      $values = $values->getResult();
      return $values;
      }
     */

    public function getSpecsValues($specification) {
        $rep = $this->em->getRepository("Entity:SpecificationsValues");
        try {
            $values = $this->em->createQueryBuilder()
                    ->select("s")
                    ->from("Entity:SpecificationsValues", "s")
                    ->where('s.id_specification=:specification')
                    ->setParameter(":specification", $specification)
                    ->groupBy("s.value")
                    ->getQuery();
            $values = $values->getResult();
        } catch (\Doctrine\ORM\Query\QueryException $e) {
            echo $e->getMessage();
        }
        return $values;
    }

    public function addReview($_POST, $user) {

        $item = $this->get_product($_POST['id_item']);

        $review = new Entity\ItemReviews();
        $review->postHydrate($_POST, array("review" => "comment"));
        $item->addReview($review);
        $user->addItemReview($review);
        $this->em->persist($item);
        $this->em->persist($user);
        $this->em->flush();
        return true;
    }

}

?>
