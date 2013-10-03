<?php

namespace NeoMvc\Controllers\Admin;

use NeoMvc\Controllers\controller;
use NeoMvc\Models\CategoriesModel;
use NeoMvc\Models\Entity as Entity;

/**
 * Description of index
 * @author Bardas Catalin
 * date: Dec 28, 2013
 */
class Categories extends controller {

    private $CategoriesModel;

    function __construct() {
        parent::__construct();
        $this->setAccessLevel(self::ADMIN_LEVEL);
        $this->CategoriesModel = new CategoriesModel();
    }

    public function add_category() {
        if ($_POST['category_name']) {

            if (count($_FILES['thumb']) > 0) {
                $images = $this->upload_images($_FILES['thumb'], "application_uploads/categories", false);
                $_POST['thumb'] = $images;
            }
            if (count($_FILES['cover']) > 0) {
                $images = $this->upload_images($_FILES['cover'], "application_uploads/categories", false);
                $_POST['cover'] = $images;
            }

            $this->CategoriesModel->addCategory($_POST);
            header('Location: ' . $this->getRefPage());
        }
    }

    public function deleteCategory() {
        if ($_POST['id_category'])
            $this->CategoriesModel->deleteCategory($_POST['id_category']);
        header('Location: ' . $this->getRefPage());
    }

    public function delete_filter() {
        $id_filter = $_POST['id_filter'];
        $this->CategoriesModel->deleteFilter($id_filter);
    }

    public function delete_spec() {
        $id_spec = $_POST['id_spec'];
        $this->CategoriesModel->deleteSpec($id_spec);
    }

    public function get_ajax_category_data() {

        if (isset($_POST['id_category'])) {

            $category_data = $this->CategoriesModel->get_ajax_category_data($_POST['id_category']);
            $filters = $this->CategoriesModel->getSpecifications($_POST['id_category'], "filter");
            $filtersHtml = '';
            foreach ($filters as $filter) {
                $filtersHtml.=' <tr id="filter_' . $filter->getId_specification() . '">
                                                <td><label>Titlu Filtru</label>
                                                <input  type="hidden" name="id_specification[]" value="' . $filter->getId_specification() . '"/>
                                                    <input  type="hidden" name="type[]" value="filter"/>
                                                </td>
                                                <td><input style="width: 250px" type="text" name="title[]" value="' . $filter->getTitle() . '"/></td>
                                                <td><label>Nume</label></td>
                                                <td><input style="width: 100px" type="text" value="' . $filter->getName() . '" name="name[]"/></td>
                                                <td><div style="height: 30px; line-height: 30px;" onclick="delete_filter(' . $filter->getId_specification() . ')" id="submitBtn">Sterge</div></td>
                                            </tr>';
            }
            $specifications = $this->CategoriesModel->getSpecifications($_POST['id_category'], "info");
            $specsHtml = '';
            foreach ($specifications as $spec) {
                $specsHtml.=' <tr id="spec' . $spec->getId_specification() . '">
                                                <td><label>Nume Specificatie</label>
                                                <input  type="hidden" name="id_specification[]" value="' . $spec->getId_specification() . '"/>
                                                    <input  type="hidden" name="type[]" value="info"/>
                                                    </td>
                                                <td><input style="width: 250px" type="text" name="name[]" value="' . $spec->getName() . '"/></td>
                                                <td><div style="height: 30px; line-height: 30px;" onclick="delete_specification(' . $spec->getId_specification() . ')" id="submitBtn">Sterge</div></td>
                                            </tr>';
            }

            echo json_encode(array("name" => $category_data->getName(), "id_category" => $category_data->getId_category(), "id_parent" => $category_data->getId_parent(), "filters" => $filtersHtml, "specifications" => $specsHtml, "images" => array("thumb" => $category_data->getThumb(), "cover" => $category_data->getCover())));
        }
    }

    public function load_filters() {
        $id_category = $_POST['id_category'];
        $specs = $this->CategoriesModel->getSpecifications($id_category);


        $filtersArray = array();
        $infosArray = array();

        $htmlFilter = "";
        $htmlInfo = "";

        $jsLoad = "";
        foreach ($specs as $spec) {
            switch ($spec->getType()) {
                case "info": {
                        $infoArray = array(
                            "name" => $spec->getName(),
                            "id_specification" => $spec->getId_specification(),
                            "slug" => $spec->getSlug()
                        );
                        $htmlInfo.='<tr>
                    <td class="label "><label>' . $spec->getName() . '</label></td>
                    <td class="small_input ' . $spec->getId_specification() . '"><div ><input type="text" name="' . $spec->getId_specification() . '"/></div>
                        </td>
                    </tr>';
                        $infosArray[] = $infoArray;
                    }break;
                case "filter": {
                        $filterArray = array(
                            "name" => $spec->getName(),
                            "id_specification" => $spec->getId_specification(),
                            "title" => $spec->getTitle(),
                            "slug" => $spec->getSlug()
                        );
                        $htmlFilter.='<tr>
                    <td class="label "><label>' . $spec->getName() . '</label></td>
                    <td class="small_input ' . $spec->getId_specification() . '"><div ><input type="text" name="' . $spec->getId_specification() . '"/></div>
                        </td>
                    </tr>';
                        $filtersArray[] = $filterArray;
                    }break;
            }
        }




        echo json_encode(array("status" => "success", "dataFilters" => $filtersArray,"dataSpecs"=>$infosArray,"htmlFilters" => $htmlFilter, "htmlSpecs" => $htmlInfo));
    }

    public function updateCategory() {

        if ($_POST['category_name']) {
            if ($_FILES['thumb']['name'][0]) {
                $images = $this->upload_images($_FILES['thumb'], "application_uploads/categories", false);
                $_POST['thumb'] = $images;
            }
            if ($_FILES['cover']['name'][0]) {
                $images = $this->upload_images($_FILES['cover'], "application_uploads/categories", false);
                $_POST['cover'] = $images;
            }

            $this->CategoriesModel->updateCategory($_POST);
            header('Location: ' . $this->getRefPage());
        }
    }

}

?>
