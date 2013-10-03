<script type="text/javascript">

$(document).ready(function(){
   $('.list_buttons').buttonset(); 
});
</script>
<div id="admin_content">

    <table id='main_table' border='0' width='100%' cellpadding='0' cellspacing='0'>
        <tr>

            <? require_once('views/admin/left_menu.php'); ?> 

            <td class='content index'>
                <!-- content -->

                <table width="100%" border="0" id="list_table" cellpadding="0" cellspcing="0">
                    <tr>
                        <th width="100" class="cell_left">
                            Id Produs
                        </th>
                        <th>
                            Nume
                        </th>
                        <th >
                            Pret
                        </th>
                        <th >
                            Adaugat La
                        </th>
                        <th class="cell_right">

                        </th>

                    </tr>
                    <? foreach($this->products as $product) { 
                        $productDetails=$product->getProduct();
                        if(!$productDetails){
                            exit("<b>EROARE: Item-ul ".$product->getId_item().' nu are niciun produs/oferta asociata</b>');
                        }
                        ?>
                    <tr>
                        <td width="10%"><a href="<?= URL ?>admin/product/edit_product/<?=$product->getId_item()?>"><?=$product->getId_item()?></a></td>
                        <td width="30%"><?=$product->getName()?></td>
                        <td width="30%"><?=$productDetails->getPrice()?> ron</td>
                        <td wdith="30%"><?=$product->getCreatedDate()?></td>
                        
                        <td width="20%" class="list_buttons cell_right">
                            <a href="<?= URL ?>admin/product/edit_product/<?=$product->getId_item()?>">Editeaza</a>
                            <a href="<?= URL ?>admin/product/delete_product/<?=$product->getId_item()?>">Sterge</a>
                        </td>
                    </tr>
                    <? } ?>
                </table

                <!-- end content -->
            </td>
        </tr>
    </table>

</div>