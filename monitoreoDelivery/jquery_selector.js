jQuery(document).ready(function($) {

    $("#selector_tiendas").on('change', function(){

$("#motoristas_activos").html('');
$("#motoristas_en_ruta").html('');
$("#motoristas_inactivos").html('');



        let cual = $(this).val();

        var data = {
            'action': 'busco_motoristas',
            'tienda': cual
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function(response) {
          //  console.log(response);
/*
[{"delivery_boy_id":"3460","user_nicename":"dliver3","estado":"1"},{"delivery_boy_id":"88","user_nicename":"motorista1","estado":"1"}]
*/

        let object = JSON.parse(response);
        $(object).each(function (i, val) {
console.log(val);

if(val.estado == "1"){
$("#motoristas_activos").append('<h4><span class="badge badge-primary">'+val.user_nicename+'</span></h4>');
}


if(val.estado == "0"){
$("#motoristas_inactivos").append('<h4><span class="badge badge-primary">'+val.user_nicename+'</span></h4>');
}
        });
        });
    })
}) ;

//  echo "<h4><span class='badge badge-primary'>$v->user_nicename</span></h4>";
