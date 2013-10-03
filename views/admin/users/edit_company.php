<script>
    $(function() {
        $("#tabs").tabs();
      
        $("input[type=submit]").button();
    });
</script>

<div id="admin_content">

    <table id='main_table' border='0' width='100%' cellpadding='0' cellspacing='0'>
        <tr>

            <? require_once('views/admin/left_menu.php'); ?> 

            <td class='content index'>
                <!-- content -->
                
                <form method="post" action="<?= URL ?>admin/users/editCompanySubmit"  enctype="multipart/form-data">
                    <input type="hidden" name="id_user" value="<?=$this->user->getId_user()?>"/>
                    <div id="submit_btn_right">
                        <input name="submit" type="submit" value="Salveaza" />
                    </div>
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1">Detalii Partener</a></li>
                            <li><a href="#tabs-2">Detalii Companie</a></li>
                        </ul>
                        <div id="tabs-1">

                            <table  border='0' width='100%' id='add_table'>
                                <tr>
                                    <td class='label'>
                                        <label>Nume pers. contact</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='nume'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Prenume pers. contact</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='prenume'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Email(*)</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='email'/>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class='label'>
                                        <label>Parola(*)</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='real_password'/>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <div id="tabs-2">
                             <table  border='0' width='100%' id='add_table'>
                                <tr>
                                    <td class='label'>
                                        <label>Logo Companie(*)</label>
                                    </td>
                                    <td class='input' >
                                        <input type='file' name='image[]'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Nume Companie(*)</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='company_name'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Website(*)</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='website'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Telefon</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='phone'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>CIF</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='cif'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Registrul Comertului</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='regCom'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Adresa</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='address'/>
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td class='label'>
                                        <label>Latitudine</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='latitude'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Longitudine</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='longitude'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>IBAN</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='iban'/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='label'>
                                        <label>Bank</label>
                                    </td>
                                    <td class='input' >
                                        <input type='text' name='bank'/>
                                    </td>
                                </tr>
                             </table>
                        </div>

                    </div>
                </form>
                <!-- end content -->
            </td>
        </tr>
    </table>

</div>