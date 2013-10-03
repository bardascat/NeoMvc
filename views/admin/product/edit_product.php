<script>
    $(function() {
        $("#tabs").tabs();
        load_produs_editor();
        $("input[type=submit]").button();
        $("input[type=button]").button();
        $('.fancybox').fancybox({
            'transitionIn': 'fade',
            'height': 100,
            afterShow: function() {
                $(".fancybox-inner").css({'overflow-x': 'hidden'});
            },
            beforeClose: function() {
                load_filters();
            }
        });
    });
</script>

<div id="admin_content">

    <table id='main_table' border='0' width='100%' cellpadding='0' cellspacing='0'>
        <tr>

            <? require_once('views/admin/left_menu.php'); ?> 

            <td class='content index'>
                <!-- content -->

                <form id="addProductForm" method="post" action="<?= URL ?>admin/product/editProductDo" enctype="multipart/form-data">
                    <input type="hidden" name="id_item" value="<?= $this->item->getId_item() ?>"/>
                    <div class="categoriesInput"></div>
                    <div id="submit_btn_right">
                        <input onclick="return addProduct()"  type="button" value="Salveaza" />
                    </div>
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1">Detalii</a></li>
                            <li><a href="#tabs-2">Filtre</a></li>
                            <li><a href="#tabs-3">Specificatii</a></li>
                            <li><a href="#tabs-4">Galerie Foto</a></li>
                        </ul>
                        <div id="tabs-1">

                            <table  border='0' width='100%' id='add_table'>
                                <tr>
                                    <td class='label'>
                                        <label>Nume</label>
                                    </td>
                                    <td class='input' >
                                        <input id="name" type='text' name='name'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Scurta descriere</label>
                                    </td>
                                    <td class='input' >
                                        <input id="name" type='text' name='brief'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Pret Furnizor</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='price'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Pret Vanzare</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='sale_price'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Categorie</label>
                                    </td>
                                    <td class='input'>
                                        <a class="fancybox" href="#alege_categorie">Alege Categorie Produs</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Furnizor</label>
                                    </td>
                                    <td class='input'>
                                        <select name="id_company">
                                            <option value="">Alege partener</option>
                                            <?
                                            foreach ($this->companies as $company)
                                                $companyDetails = $company->getCompanyDetails(); {
                                                ?>

                                                <option value="<?= $company->getId_user(); ?>"><?= $companyDetails->getCompany_name() ?></option>
                                            <? } ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td class='label'>
                                        <label>Descriere</label>
                                    </td>
                                    <td class='input'>
                                        <textarea id='description' name='description'></textarea>
                                    </td>
                                </tr>

                            </table>

                        </div>
                        <div id="tabs-2">
                            <table  border='0' width='100%' id='add_table' class="filters_table">

                                <?
                                
                                $filters = $this->item->getCategory()->getFilters();
                                if ($filters)
                                    foreach ($filters as $filter) {
                                        // $filter = $filterValue->getFilter();
                                        ?>
                                        <tr>
                                        <input type="hidden" name="id[]" value="<?= $this->item->getSpecValueId($filter->getId_specification()) ?>"/>
                                        <td class="label"><label><?= $filter->getName() ?></label></td>

                                        <td class="small_input <?= $filter->getId_specification() ?>">
                                            <div>
                                                <input type="text" name="<?= $filter->getId_specification() ?>"/>
                                            </div>
                                        </td>
                                        </tr>
                                    <? } else { ?>
                                    <tr>
                                        <td><h2>Alege intai categoria produsului pentru a alege filtrele</h2></td>
                                    </tr>
                                <? } ?>

                            </table>
                        </div>
                        <div id="tabs-3">
                            <table  border='0' width='100%' id='add_table' class="specs_table">
                                <?
                                $specs = $this->item->getCategory()->getSpecInfo();
                                if ($specs)
                                    foreach ($specs as $spec) {
                                        // $filter = $filterValue->getFilter();
                                        ?>
                                        <tr>
                                        <input type="hidden" name="id[]" value="<?= $this->item->getSpecValueId($spec->getId_specification()) ?>"/>
                                        <td class="label"><label><?= $spec->getName() ?></label></td>

                                        <td class="small_input <?= $spec->getId_specification() ?>">
                                            <div>
                                                <input type="text" name="<?= $spec->getId_specification() ?>"/>
                                            </div>
                                        </td>
                                        </tr>
                                    <? } else { ?>
                                    <tr>
                                        <td><h2>Alege intai categoria produsului pentru a adauga specificatiile</h2></td>
                                    </tr>
                                <? } ?>

                            </table>
                        </div>
                        <div id="tabs-4">
                            <div class='add_images'>
                                <div class='image_group'>
                                    <input type='file' name='image[]'/>

                                </div>
                            </div>
                            <div class='new_image' onclick="new_image()">Poza Noua</div>

                            <table id="pictures_table" border="0" width="100%">
                                <?
                                $photos = $this->item->getImages();
                                foreach ($photos as $photo) {
                                    ?>
                                    <tr id="<?= $photo->getId_image() ?>">
                                        <td width="400">
                                            <img height="150" src="<?= URL . $photo->getImage() ?>"/>
                                        </td>
                                        <td style="vertical-align: top">
                                            <input id="princ_<?= $photo->getId_image() ?>" type="radio" <? if ($photo->getPrimary()) echo "checked"; ?> name="primary_image" value="<?= $photo->getId_image() ?>"/>  <label for="princ_<?= $photo->getId_image() ?>">Poza Principala</label> 
                                            <a class="delete_photo" href="javascript:delete_image(<?= $photo->getId_image() ?>)">Sterge</a>
                                        </td>
                                    </tr>
                                <? } ?>
                            </table>

                        </div>

                    </div>
                </form>
                <!-- end content -->
            </td>
        </tr>
    </table>


    <div id="alege_categorie" style="width: 600px;">
        <h1>Alege din ce categorii face parte acest produs (Categoria finala)</h1>
        <? print_r($this->tree); ?>
    </div>

</div>

<script>
<?
$allSpecs=$this->item->getCategory()->getSpecifications();

if ($allSpecs)
    foreach ($allSpecs as $spec) {
        ?>
            $('.<?= $spec->getId_specification() ?>').neoSelectInput({id_filter: "<?= $spec->getId_specification() ?>", url: "http://neo.oringo.ro/admin/product/getSpecsValues"});
    <? } ?>
</script>