var url = "http://neo.oringo.ro/admin/";
var urlRoot = "http://neo.oringo.ro/";

function alert(msg) {
    $.msgbox("<p>" + msg + "</p>", {
        type: "info"
    });

}

function load_produs_editor(width, height) {
    if (!width)
        width = "90%";

    if (!height)
        height = "200";
    var op = {
        filebrowserUploadUrl: urlRoot + 'controllers/uploader/upload.php?type=Files',
        width: width,
        height: height,
        toolbar:
                [
                    '/',
                    {
                        name: 'styles',
                        items: ['Source', 'FontSize', 'TextColor', 'BGColor', 'Bold', 'Italic', 'Strike']
                    },
                    {
                        name: 'insert',
                        items: ['Image', 'Table', 'PageBreak', 'Link', 'Unlink']
                    },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                    },
                ]

    }
    CKEDITOR.replace('description', op);

}
function load_offer_editor(width, height) {
    if (!width)
        width = "90%";
    if (!height)
        height = "200";
    var op = {
        filebrowserUploadUrl: urlRoot + 'controllers/uploader/upload.php?type=Files',
        width: width,
        height: height,
        toolbar:
                [
                    '/',
                    {
                        name: 'styles',
                        items: ['Source', 'FontSize', 'TextColor', 'BGColor', 'Bold', 'Italic', 'Strike']
                    },
                    {
                        name: 'insert',
                        items: ['Image', 'Table', 'PageBreak', 'Link', 'Unlink']
                    },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                    },
                ]

    }
    CKEDITOR.replace('terms', op);
    CKEDITOR.replace('benefits', op);

}

function new_image() {
    var new_image_html = "<div class='image_group'><input type='file' name='image[]'/></div>";

    $('.add_images').append(new_image_html);
}

function delete_image(id_image) {
    $.ajax({
        type: "POST",
        url: url + "product/delete_image",
        data: "id_image=" + id_image,
        dataType: 'json',
        beforeSend: function() {

        },
        success: function(result) {

            if (result.type == "success") {
                $('#pictures_table #' + id_image).fadeOut(400, function() {
                    $('#pictures_table #' + id_image).remove();
                })
            }
            else {
                alert("Eroare: " + result.msg);
            }
            console.log(result);
        }})
}




/* categories */
function add_category(parent_id) {
    $('.filters_table table tbody').empty();
    $("#add_category_trigger").fancybox().trigger('click');
    $('#add_category .parent_id').val(parent_id);

    $('#tabs1_add table tbody').empty();
    $('#tabs1_add table tbody').append('<tr><td colspan="3"  style="padding-top: 15px;"><h4> Administreaza Filtre <a style="color: #0057D5" href="javascript:add_filter(1)"> Adauga Filtru nou</a></h4></td></tr>');


    $('#tabs2_add table tbody').empty();
    $('#tabs2_add table tbody').append('<tr><td colspan="2" style="padding-top: 15px;"><h4> Administreaza Specificatii <a style="color: #0057D5" href="javascript:add_specification(1)"> Adauga Specificatie noua</a></h4></td></tr>');


}

function remove_category(cat_id) {
    $('#remove_category .id_category').val(cat_id);
    $('#remove_category form').submit();
}

function submit_add_category() {
    if ($('#add_category .name').val())
        $('#add_category_form').submit();
    else
    {
        alert("<br/>Eroare, trebuie sa introduci numele categoriei !");
    }
}

function update_category(cat_id) {
    //get category data with ajax. like a boss
    $.ajax({
        type: "POST",
        url: url + 'categories/get_ajax_category_data',
        data: "id_category=" + cat_id,
        dataType: 'json'
    }).done(function(cat) {
        $('#update_category .name').val(cat.name);
        $('#update_category .category_id').val(cat.id_category);
        $('#update_category .parent_id').val(cat.id_parent);
        $('#update_category .old_category_picture').val(cat.photo);
        $("#update_category_trigger").fancybox().trigger('click');
        
        $('#tabs1 table tbody').empty();
        $('#tabs1 table tbody').append('<tr><td colspan="3"  style="padding-top: 15px;"><h4> Administreaza Filtre <a style="color: #0057D5" href="javascript:add_filter(2)"> Adauga Filtru nou</a></h4></td></tr>');
        $('#tabs1 table tbody').append(cat.filters);

        $('#tabs2 table tbody').empty();
        $('#tabs2 table tbody').append('<tr><td colspan="2" style="padding-top: 15px;"><h4> Administreaza Specificatii <a style="color: #0057D5" href="javascript:add_specification(2)"> Adauga Specificatie noua</a></h4></td></tr>');
        $('#tabs2 table tbody').append(cat.specifications);

       
        if (cat.images.thumb) {
            $('.update_thumb').append('<a style="margin:5px;" href="' + urlRoot + cat.images.thumb + '">' + cat.images.thumb + '</a>');
        }
        else
            $('.update_thumb').empty();
        if (cat.images.cover) {
            $('.update_cover').append('<a style="margin:5px;" href="' + urlRoot + cat.images.cover + '">' + cat.images.cover + '</a>');
        }
        else
            $('.update_cover').empty();
    });

}
function submit_update_category() {
    if ($('#update_category .name').val())
        $('#update_category form').submit();
    else
    {
        alert("<br/>Eroare, trebuie sa introduci numele categoriei !");
    }
}

function addProduct() {

    $('#alege_categorie input[type=checkbox]').each(function() {

        if (this.checked) {
            var input = $("<input>").attr("type", "hidden").attr("name", "categories[]").val($(this).val());
            $('#addProductForm .categoriesInput').append($(input));
        }
    });

    var eroare = "";

    if ($('#name').val() == "") {
        eroare += "Eroare: introduceti numele produsului \n";
        $('#addProductForm .categoriesInput').empty();
    }

    if (eroare != "")
        alert(eroare);
    else
    {
        $('#addProductForm').submit()

    }
}

function add_filter(type) {
    var rand = 1 + Math.floor(Math.random() * 99999);
    if (type == 1)
        $('#tabs1_add table tbody').append('<tr id="filter_' + rand + '"><td><input type="hidden" name="type[]" value="filter"/><label>Titlu Filtru</label></td><td><input style="width: 250px" type="text" name="title[]"/></td><td> <label> Nume </label></td><td><input style = "width: 100px" type = "text" name = "name[]" / > </td><td > <div style = "height: 30px; line-height: 30px;" onclick = "delete_filter(' + rand + ')" id = "submitBtn" > Sterge </div></td></tr>');
    else
        $('#tabs1 table tbody').append('<tr id="filter_' + rand + '"><td><input type="hidden" name="type[]" value="filter"/><label>Titlu Filtru</label></td><td><input style="width: 250px" type="text" name="title[]"/></td><td> <label> Nume </label></td><td><input style = "width: 100px" type = "text" name = "name[]" / > </td><td > <div style = "height: 30px; line-height: 30px;" onclick = "delete_filter(' + rand + ')" id = "submitBtn" > Sterge </div></td></tr>');
}
function add_specification(type) {
    var rand = 1 + Math.floor(Math.random() * 99999);
    if (type == 1)
        $('#tabs2_add table tbody').append('<tr id="spec' + rand + '"><td><input type="hidden" name="type[]" value="info"/><label>Nume Specificatie</label></td><td><input style="width: 250px" type="text" name="name[]"/></td><td > <div style = "height: 30px; line-height: 30px;" onclick = "delete_specification(' + rand + ')" id = "submitBtn" > Sterge </div></td></tr>');
    else
        $('#tabs2 table tbody').append('<tr id="spec' + rand + '"><td><input type="hidden" name="type[]" value="info"/><label>Nume Specificatie</label></td><td><input style="width: 250px" type="text" name="name[]"/></td><td > <div style = "height: 30px; line-height: 30px;" onclick = "delete_specification(' + rand + ')" id = "submitBtn" > Sterge </div></td></tr>');
}

function load_filters() {
    var id_category = "";
    $('#alege_categorie input[type=checkbox]').each(function() {

        if (this.checked) {
            id_category = $(this).val();
        }
    });


    $.ajax({
        type: "POST",
        url: url + 'categories/load_filters',
        data: "id_category=" + id_category,
        dataType: 'json'
    }).done(function(filters) {
        if (filters.status == "success") {
            if (filters.dataFilters.length < 1) {
                alert("<br/>Atentie:Categoria aleasa nu are filtrele setate !");
                $('.filters_table').empty();
            }
            else {
                $('.filters_table').empty();
                $('.filters_table').append(filters.htmlFilters);
                for (var i = 0; i < filters.dataFilters.length; i++) {
                    $('.' + filters.dataFilters[i].id_specification).neoSelectInput({"id_filter": filters.dataFilters[i].id_specification});
                }
            }
            $('.specs_table').empty();
            if (filters.dataSpecs.length > 0) {
                $('.specs_table').append(filters.htmlSpecs);
                for (var i = 0; i < filters.dataSpecs.length; i++) {
                    $('.' + filters.dataSpecs[i].id_specification).neoSelectInput({"id_filter": filters.dataSpecs[i].id_specification});
                }
            }

        }
        else {
            $('.filters_table').empty();
            alert("Eroare incarcare filtre");
        }
    });


}

function initFilters()
{
    var id_category = "";
    $('#alege_categorie input[type=checkbox]').each(function() {

        if (this.checked) {
            id_category = $(this).val();
        }
    });
    if (id_category)
        load_filters();
}

function delete_filter(id_filter) {
    $('.filters_table #filter_' + id_filter).remove();
    //get category data with ajax. like a boss
    $.ajax({
        type: "POST",
        url: url + 'categories/delete_spec',
        data: "id_spec=" + id_filter,
    }).done(function(cat) {

    });

}
function delete_specification(id_spec) {
    $('.filters_table #spec' + id_spec).remove();

    $.ajax({
        type: "POST",
        url: url + 'categories/delete_spec',
        data: "id_spec=" + id_spec,
    }).done(function(cat) {

    });

}