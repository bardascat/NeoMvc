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
                            Id Partener
                        </th>
                        <th>
                            Nume Companie
                        </th>
                        <th>
                            email
                        </th>
                        <th >
                            Data Creare
                        </th>
                        <th class="cell_right">

                        </th>

                    </tr>
                    <? /* @var $product Entity\Product */ foreach($this->companies as $company) { $companyDetails=$company->getCompanyDetails(); ?>
                    <tr>
                        <td width="10%"><a href="<?= URL ?>admin/users/edit_company/<?=$company->getId_user()?>"><?=$company->getId_user()?></a></td>
                        <td width="30%"><?=$companyDetails->getCompany_name()?></td>
                        <td width="30%"><?=$company->getEmail()?></td>
                        <td><?=$company->getCreatedDate()?></td>
                        
                        <td width="20%" class="list_buttons cell_right">
                            <a href="<?= URL ?>admin/users/edit_company/<?=$company->getId_user()?>">Editeaza</a>
                            <a href="<?= URL ?>admin/users/delete_user/<?=$company->getId_user()?>">Sterge</a>
                        </td>
                    </tr>
                    <? } ?>
                </table

                <!-- end content -->
            </td>
        </tr>
    </table>

</div>