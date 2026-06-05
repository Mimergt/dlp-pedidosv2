

jQuery(document).ready(function($) {
    $(".actualizaBtn").on('click', function(){
        let cual = $(this).attr('data-id');
        let tienda= $("#resto_"+cual).val();

    var data = {
        'action': 'asignaTienda',
        'cual': cual ,
        'tienda': tienda
    };
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
    jQuery.post(ajax_object.ajax_url, data, function(response) {
        console.log(response);

    });
});



    $(document).ready(function(){
        $('.pi').picker();
    });


}) ;
