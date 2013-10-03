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

            <td class='content index' style="background-color: #FFF;   border:1px solid #f0f0f0;">
               
                <!-- content -->
                <div class="inner_content">

                    <a id="add_category_trigger" href="#add_category" class="fancybox"></a>
                    <a id="update_category_trigger" href="#update_category" class="fancybox"></a>


                    
                    <div id="add_main_category"
                         onclick="add_category(0)">Adauga categorie principala
                    </div>  
                    <div id="category_tree">
                        <? echo $this->CategoriesAdminMenu; ?>  
                    </div>
                    <div id="clear"></div>


                    <div id="add_category" >  
                        <form method="post" id="add_category_form"
                              action="<? echo URL ?>admin/categories/add_category" enctype="multipart/form-data">   
                            <input type="hidden" class="parent_id" name="id_parent"/>     
                            <input type="hidden" value="offer" name="item_type"/>     
                            <table>
                                <tr>
                                    <td> <label>Nume</label>    </td>
                                    <td><input class="name" type="text" name="category_name"/> </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>Poza(Optional)</label>  
                                    </td>
                                    <td>
                                        <input type="file" name="image[]"/>     
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div onclick="submit_add_category()" id="submitBtn">Adauga</div> 
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <div id="remove_category"> 

                        <form method="post" action="<? echo URL ?>admin/categories/deleteCategory">  
                            <input class="id_category" name="id_category" type="hidden"/> 
                        </form>
                    </div>

                    <div id="update_category" >  
                        <form method="post" action="<? echo URL ?>admin/categories/updateCategory" enctype="multipart/form-data">  
                            <input type="hidden" class="parent_id" name="id_parent"/>   
                            <input type="hidden" class="category_id" name="id_category"/>  
                            <input type="hidden" name="item_type" value="offer"/>  
                            <input type="hidden" class="old_category_picture" name="old_category_picture"/> 
                            <table>
                                <tr>
                                    <td>
                                        <label>Nume</label>      
                                    </td>
                                    <td>
                                        <input class="name" type="text" name="category_name"/> 
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>Poza</label>    
                                    </td>
                                    <td>
                                        <input type="file" name="image[]"/>  
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <div onclick="submit_update_category()" id="submitBtn">Save</div>    
                                    </td>
                                </tr>

                            </table>
                        </form>

                    </div>
                </div>
                <!-- end content -->
            </td>
        </tr>
    </table>

</div>