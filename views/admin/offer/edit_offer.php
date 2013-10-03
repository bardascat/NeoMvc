<script>
    $(function() {
        $("#tabs").tabs();
        load_offer_editor();
        $("input[type=submit]").button();
        $("input[type=button]").button();

        $('.fancybox').fancybox({
            'transitionIn': 'fade',
            'height': 100,
            afterShow: function() {
                $(".fancybox-inner").css({'overflow-x': 'hidden'});
            }
        });
        $(".datepicker").datepicker({dateFormat: 'dd-mm-yy'});

    });
</script>

<div id="admin_content">

    <table id='main_table' border='0' width='100%' cellpadding='0' cellspacing='0'>
        <tr>

            <? require_once('views/admin/left_menu.php'); ?> 

            <td class='content index'>
                <!-- content -->

                <form id="addProductForm" method="post" action="<?= URL ?>admin/offer/editOfferDo" enctype="multipart/form-data">
                    <input type="hidden" name="id_item" value="<?= $this->item->getId_item() ?>"/>
                    <div class="categoriesInput">
                    </div>
                    <div id="submit_btn_right">
                        <input onclick="addProduct()" type="button" value="Salveaza" />
                    </div>
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1">Detalii</a></li>
                            <li><a href="#tabs-2">Atribute</a></li>
                            <li><a href="#tabs-3">Date</a></li>
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
                                        <label>Scurta descrierie</label>
                                    </td>
                                    <td class='input' >
                                        <input id="name" type='text' name='brief'/>
                                    </td>
                                </tr>


                                <tr>
                                    <td class='label'>
                                        <label>Termeni</label>
                                    </td>
                                    <td class='input'>
                                        <textarea id='terms' name='terms'></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Beneficii</label>
                                    </td>
                                    <td class='input'>
                                        <textarea id='benefits' name='benefits'></textarea>
                                    </td>
                                </tr>

                            </table>

                        </div>
                        <div id="tabs-2">

                            <table  border='0' width='100%' id='add_table'>
                                <tr>
                                    <td class="label">
                                        Pret Intreg
                                    </td>
                                    <td class='small_input'>
                                        <input type="text" name="price"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">
                                        Pret Redus
                                    </td>
                                    <td class='small_input'>
                                        <input type="text" name="sale_price"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">
                                        Comision Oringo
                                    </td>
                                    <td class='small_input'>
                                        <input type="text" name="commission"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Locatie(ex:Obor)</label>
                                    </td>
                                    <td class='small_input' >
                                        <input type='text' name='location'/>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="label">
                                        Vanzarile incep cu nr.
                                    </td>
                                    <td class='small_input'>
                                        <input type="text" name="startWith"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Categorie</label>
                                    </td>
                                    <td class='input'>
                                        <a class="fancybox" href="#alege_categorie">Alege Categorie Oferta</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Partener</label>
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

                            </table>

                        </div>
                        <div id="tabs-3">

                            <table  border='0' width='100%' id='add_table'>
                                <tr>
                                    <td class='label'>
                                        <label>Activa</label>
                                    </td>
                                    <td class='input' >
                                        <select name="active">
                                            <option value="1">Da</option>
                                            <option value="0">Nu</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>Oferta</b></td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Data de Inceput</label>
                                    </td>
                                    <td class='small_input' >
                                        <input class="datepicker" type="text" name="start_date"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Data de sfarsit</label>
                                    </td>
                                    <td class='small_input' >
                                        <input  class="datepicker" type="text" name="end_date"/>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2"><b>Voucher</b></td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Data de Inceput</label>
                                    </td>
                                    <td class='small_input' >
                                        <input  class="datepicker" type="text" name="voucher_start_date"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Data de sfarsit</label>
                                    </td>
                                    <td class='small_input' >
                                        <input   class="datepicker" type="text" name="voucher_end_date"/>
                                    </td>
                                </tr>

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