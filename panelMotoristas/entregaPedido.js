jQuery(document).ready(function($){

    jQuery("#entregado").on('click', function(){

    let cualPedido = jQuery(this).attr('data-id');
    let motorman = jQuery("#motorman").val();

    var data = {
        'action': 'm_pedido_entregado',
        'cualPedido': cualPedido ,
        'motorman': motorman
    };
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function(response) {
        // console.log(response);
        location.reload(true);
    });

});

});

