(function($) {
    $.fn.extend({
        neoSelectInput: function(options) {
          
            var defaults = {
                url: "http://neo.oringo.ro/admin/product/getSpecsValues"
            }
            var options = $.extend(defaults, options);
            return this.each(function() {
                var o = options;
                var obj = $(this);
                var list = null;
            

                var img = $('<img style="cursor:pointer" id="arrow_img">');
                img.attr("src", "http://neo.oringo.ro/images/admin/comboxo_arrow.png");

                img.click(function() {
                    if (!list)
                        list = loadAjax(obj);
                    else {
                        list.fadeToggle(200);
                    }
                })
                obj.children('div').append(img);


            });

            function loadAjax(obj) {
                var list = $("<select><option value=''>Alege Valoarea</option></select>");
                $.ajax({
                    type: "POST",
                    url: options.url,
                    data: "id_filter=" + options.id_filter,
                    dataType: 'json',
                    success: function(result) {
                        list.append(result);
                    }})

                var width = obj.children('div').children('input').css("width");
                console.log(width);
                
                list.css({'width': 218});
                obj.append(list);

                list.change(function() {
                    obj.children('div').children('input').val(list.val());
                })

                return list;
            }
        }
    })
}
)(jQuery);