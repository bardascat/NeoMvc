<?php

/**
 * Description of login_model
 * @author Bardas Catalin
 * date: Dec 29, 2011 
 */

namespace NeoMvc\Models;

use Doctrine\ORM\EntityManager;
use NeoMvc\Models\Entity as Entity;

class CategoriesModel extends \NeoMvc\Models\Model {

    function __construct() {
        $this->em = $this->getConnection();
    }

    /**
     * Cauta category dupa id.
     * @param  id int
     * @return Entity\Category
     */
    public function getCategoryByPk($id) {
        $cat = $this->em->find("Entity:Category", $id);

        if (!$cat)
            return false;
        else
            return $cat;
    }

    /**
     * Cauta category dupa slug
     * @param  id int
     * @return Entity/Category

     */
    public function getCategoryBySlug($slug) {
        $rep = $this->em->getRepository("Entity:Category");
        $cat = $rep->findBy(array("slug" => $slug));

        if (!$cat)
            return false;
        else
            return $cat[0];
    }

    /**
     * Verificam daca o categorie are subcategorii
     * @param type $id_category
     * @return boolean
     */
    public function hasChilds($id_category) {
        $result = $this->em->createQuery("select 1 from Entity:Category c where c.id_parent=:id_parent")
                ->setParameter("id_parent", $id_category)
                ->getResult();
        if (empty($result))
            return false;
        else
            return true;
    }

    public function categoryExists($slug, $item_type) {
        $dql = $this->em->createQuery("select 1 from Entity:Category c where c.slug=:slug and c.item_type=:item_type");
        $dql->setParameter(':slug', $slug);
        $dql->setParameter(':item_type', $item_type);
        $res = $dql->getResult();

        return !empty($res);
    }

    public function addCategory($_POST) {
        $id_parent = $_POST['id_parent'];
        $category = new Entity\Category();

        $category->setName($_POST['category_name']);
        if ($id_parent)
            $category->setId_parent($id_parent);
        $category->setSlug(\NeoMvc\Controllers\controller::makeSlugs($_POST['category_name']));
        $category->setItem_type($_POST['item_type']);

        if (isset($_POST['thumb']))
            $category->setThumb($_POST['thumb'][0]['thumb']);

        if (isset($_POST['cover']))
            $category->setCover($_POST['cover'][0]['image']);


        //adaugam specificatii
        if (isset($_POST['name'])) {
            for ($i = 0; $i < count($_POST['name']); $i++) {
                if (strlen($_POST['name'][$i]) > 1) {
                    $spec = new Entity\Specification();
                    $spec->setName($_POST['name'][$i]);
                    $spec->setType($_POST['type'][$i]);
                    if (isset($_POST['title'][$i]))
                        $spec->setTitle($_POST['title'][$i]);

                    $category->addSpecification($spec);
                }
            }
        }

        $this->em->persist($category);
        $this->em->flush($category);
        return true;
    }

    public function deleteCategory($id_category) {
        $this->em->createQuery("delete from Entity:Category c where c.id_category='$id_category'")->execute();
        return true;
    }

    public function deleteSpec($id_spec) {
        $this->em->createQuery("delete from Entity:Specification c where c.id_specification='$id_spec'")->execute();
        return true;
    }

    public function get_ajax_category_data($id_category) {
        $cat = $this->em->find("Entity:Category", $id_category);
        return $cat;
    }

    public function getSpecifications($id_category, $type = false) {
        $cat = $this->em->getRepository("Entity:Specification");
        if ($type)
            $specs = $cat->findBy(array("id_category" => $id_category, "type" => $type));
        else
            $specs = $cat->findBy(array("id_category" => $id_category));
        return $specs;
    }

    /**
     * Intoarce filtrele unei  anumite categorii + join cu valorile filtrelor definite de produse.
     * Valorile filtrelor sunt grupate dupa nume(ne intereseaza valorile unice, nu vrem duplicate)
     * @param type $id_category
     * @param $products optional, Reprezinta lista produselor in cazul in care ne intereseaza doar filtrele anumitor produse
     * @return Entity\CategoryFilters
     */
    public function getDistinctFilters($id_category, $products = false) {
        $in = "";
        if ($products) {
            foreach ($products as $product) {
                $in.=$product->getId_item() . ',';
            }
            $in = substr($in, 0, -1);
            $query = "select c,val from Entity:Specification c join c.SpecificationValues val where c.id_category=:id_category and c.type='filter' and val.id_item in ($in) group by val.value";
        }
        else
            $query = "select c,val from Entity:Specification c join c.SpecificationValues val where c.id_category=:id_category and c.type='filter' group by val.value";
        
        try {
            $result = $this->em->createQuery($query)
                    ->setParameter("id_category", $id_category)
                    ->execute();
        } catch (\Doctrine\ORM\Query\QueryException $e) {
            echo $e->getMessage();
            exit();
        }
        return $result;
    }

    public function updateCategory($_POST) {
        /* @var $category Entity\Category */
        try {
            $category = $this->em->find("Entity:Category", $_POST['id_category']);
            $category->setName($_POST['category_name']);
            
            $category->setSlug(\NeoMvc\Controllers\controller::makeSlugs($_POST['category_name']));

            if (isset($_POST['thumb']))
                $category->setThumb($_POST['thumb'][0]['thumb']);
            if (isset($_POST['cover']))
                $category->setCover($_POST['cover'][0]['image']);


            if (isset($_POST['name'])) {
                for ($i = 0; $i < count($_POST['name']); $i++) {
                    if (strlen($_POST['name'][$i]) > 1) {
                        if (isset($_POST['id_specification'][$i])) {
                            $spec = $this->em->find("Entity:Specification", $_POST['id_specification'][$i]);
                            //facem update in tabelul categories filters
                            try {
                                $this->em->createQuery("update Entity:SpecificationsValues v set v.slug=:slug where v.id_specification=:id_specification")
                                        ->setParameter(":slug", \NeoMvc\Controllers\controller::makeSlugs($_POST['name'][$i]))
                                        ->setParameter(":id_specification", $_POST['id_specification'][$i])
                                        ->execute();
                            } catch (\Doctrine\ORM\Query\QueryException $e) {
                                echo $e->getMessage();
                                exit();
                            }
                        }
                        else
                            $spec = new Entity\Specification();

                        $spec->setName($_POST['name'][$i]);

                        //filtru sau informatie
                        $spec->setType($_POST['type'][$i]);

                        //filtrele au un titlu descriptiv
                        if (isset($_POST['title'][$i]))
                            $spec->setTitle($_POST['title'][$i]);


                        $category->addSpecification($spec);
                    }
                }
            }

            $this->em->persist($category);
            $this->em->flush();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return true;
    }

    /**
     * Intorcea lista copiilor nodului id_parent
     * @param integer $id_parent
     * @param integer $max_depth (adancimea maxima in arbore, -1 pentru adancime maxima)
     * @return Array Entity\Category
     */
    public function getChilds($id_parent, $max_depth = -1) {

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult("Entity:Category", "c");
        $rsm->addFieldResult("c", "id_category", "id_category");
        $rsm->addFieldResult("c", "category_name", "name");
        $rsm->addFieldResult("c", "id_parent", "id_parent");
        $rsm->addFieldResult("c", "slug", "slug");
        $rsm->addScalarResult("depth", "depth");
        $rsm->addFieldResult("c", "thumb", "thumb");
        $rsm->addFieldResult("c", "cover", "cover");

        $query = $this->em->createNativeQuery("call category_hierarchy(:id_parent,:max_depth)", $rsm);
        $query->setParameter(":id_parent", $id_parent);
        $query->setParameter(":max_depth", $max_depth);

        $categories = $query->getResult();

        return $categories;
    }

    /**
     * Intoarce lista parintilor in ordine de jos in sus.
     * IMPORTANT: Procedura intoarce pe ultima pozitie si categoria data ca parametru
     * @return Array Entity\Category
     */
    public function getParents($id_category) {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult("Entity:Category", "c");
        $rsm->addFieldResult("c", "id_category", "id_category");
        $rsm->addFieldResult("c", "category_name", "name");
        $rsm->addFieldResult("c", "id_parent", "id_parent");
        $rsm->addFieldResult("c", "slug", "slug");
        $rsm->addScalarResult("v_depth", "depth");
        $rsm->addFieldResult("c", "thumb", "thumb");
        $rsm->addFieldResult("c", "cover", "cover");

        $query = $this->em->createNativeQuery("call get_root_categories(:id_category)", $rsm);
        $query->setParameter(":id_category", $id_category);


        $categories = $query->getResult();

        return $categories;
    }

    /**
     * Intoarce array cu categoriile principale
     * @param string  $item_type (product sau offer)
     * @return Array Of Entity\Category
     */
    public function getRootCategories($item_type, $child = false) {
        $catRep = $this->em->getRepository("Entity:Category");
        $categories = $catRep->findBy(array("id_parent" => NULL, "item_type" => $item_type), array("name" => "Asc"));

        $full_categories = array();

        //adaugam si copii daca e necesar
        if ($child) {
            foreach ($categories as $category) {
                $cat = array();
                $cat['parent'] = $category;
                $cat['childs'] = $this->getChilds($category->getId_category());
                unset($cat['childs'][0]);
                $full_categories[] = $cat;
            }
            return $full_categories;
        }

        return $categories;
    }

    /**
     * Genereaza lista - html categorilor pentru administrare
     * 
     * @param string $item_type Product sau Offer
     * 
     */
    public function createCheckboxList($item_type, $id_item = false, $id_category = false) {

        $pdoObject = $this->em->getConnection();
        $stm = $pdoObject->prepare("select * from categories where item_type=:item_type order by name ");
        $stm->bindValue(":item_type", $item_type);
        $data = $stm->execute();

        $data = $stm->fetchAll();

        if (count($data) < 1)
            return false;
        foreach ($data as $row) {

            $this->menu_array[$row['id_category']] = array('name' => ucfirst($row['name']), 'slug' => $row['slug'], 'parent' => $row['id_parent']);
        }
        //daca avem produs ca parametrul trebuie sa setam niste categorii checked
        $cRep = $this->em->getRepository("Entity:ItemCategories");
        if ($id_item) {
            $itemCategories = $cRep->findBy(array("id_item" => $id_item));
            $this->itemCategories = $itemCategories;
        } elseif ($id_category) {
            $this->checkedCategory = $id_category;
        }


        //generate menu starting with parent categories (that have a 0 parent)	
        ob_start();
        $this->generateCheckboxList(0);
        $menu = ob_get_clean();

        return $menu;
    }

    private function generateCheckboxList($parent) {

        $has_childs = false;
        //this prevents printing 'ul' if we don't have subcategories for this category
        //use global array variable instead of a local variable to lower stack memory requierment


        foreach ($this->menu_array as $key => $value) {

            if ($key == 0) {
                //main parent
                $main_parent_name = $value['name'];
            }

            if ($value['parent'] == $parent) {

                //if this is the first child print '<ul>'                       

                if ($has_childs === false) {

                    //don't print '<ul>' multiple times                             

                    $has_childs = true;
                    if ($parent == 0)
                        echo ' <ul>';
                    else
                        echo "\n<ul> \n";
                }
                $checked = "";

                if (isset($this->itemCategories))
                    foreach ($this->itemCategories as $itemCategory) {
                        if ($itemCategory->getId_category() == $key)
                            $checked = "checked";
                    }
                if (isset($this->checkedCategory)) {
                    if ($key == $this->checkedCategory)
                        $checked = "checked";
                }
                echo '<li> <div class="container">
                      
                    <input ' . $checked . '  type="checkbox" class="checkbox"  name="categories[]" value="' . $key . '">
                    <div class="name">' . $value["name"] . '</div>
                    </div>    
                    ';

                $this->generateCheckboxList($key);

                //call function again to generate nested list for subcategories belonging to this category

                echo "</li>\n";
            }
        }

        if ($has_childs === true)
            echo "\n</ul> \n\n";
    }

    /**
     * Genereaza lista -html  categorilor pentru administrare
     * 
     * @param string $item_type Product sau Offer
     * 
     */
    public function createAdminList($item_type) {

        $pdoObject = $this->em->getConnection();
        $stm = $pdoObject->prepare("select * from categories where item_type=:item_type order by name ");
        $stm->bindValue(":item_type", $item_type);
        $data = $stm->execute();

        $data = $stm->fetchAll();

        if (count($data) < 1)
            return false;
        foreach ($data as $row) {

            $this->menu_array[$row['id_category']] = array('name' => ucfirst($row['name']), 'slug' => $row['slug'], 'parent' => $row['id_parent']);
        }

        //generate menu starting with parent categories (that have a 0 parent)	

        ob_start();
        $this->generateAdminList(0);
        $menu = ob_get_clean();

        return $menu;
    }

    private function generateAdminList($parent) {

        $has_childs = false;
        //this prevents printing 'ul' if we don't have subcategories for this category
        //use global array variable instead of a local variable to lower stack memory requierment


        foreach ($this->menu_array as $key => $value) {

            if ($key == 0) {
                //main parent
                $main_parent_name = $value['name'];
            }

            if ($value['parent'] == $parent) {

                //if this is the first child print '<ul>'                       

                if ($has_childs === false) {

                    //don't print '<ul>' multiple times                             

                    $has_childs = true;
                    if ($parent == 0)
                        echo ' <ul>';
                    else
                        echo "\n<ul> \n";
                }

                echo "<li><div class='item'>
                    <div class='name'>ID=".$key.", {$value['name']}</div>
                    <div class='add' onclick='add_category(" . $key . ")'></div>
                    <div class='edit' onclick='update_category(" . $key . ")' ></div>
                    <div class='remove' onclick='remove_category(" . $key . ")'></div>
                    </div>";


                $this->generateAdminList($key);

                //call function again to generate nested list for subcategories belonging to this category

                echo "</li>\n";
            }
        }

        if ($has_childs === true)
            echo "\n</ul> \n\n";
    }

}

?>
