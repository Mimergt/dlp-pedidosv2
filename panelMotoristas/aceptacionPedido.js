jQuery(document).ready(function($){

$("#rechazar").on('click', function(){

    Swal.fire({
        title: 'Esta seguro?',
        text: "No podra llevar el pedido",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar Rechazo',
        cancelButtonText: 'Cerrar esta ventana'
    }).then((result) => {
        if (result.isConfirmed) {

            let cualPedido = $(this).attr('data-id');
            let motorman = $("#motorman").val();

            var data = {
                'action': 'm_rechaza_pedido',
                'cualPedido': cualPedido ,
                'motorman': motorman
            };
            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            $.post(ajax_object.ajax_url, data, function(response) {
                console.log(response);
                // location reload
                location.reload(true);
            });


        }
    })

});

    $("#aceptar").on('click', function(){

        $(this).prop('disabled', true);

        let cualPedido = $(this).attr('data-id');
        let motorman = $("#motorman").val();

        var data = {
            'action': 'm_acepta_pedido',
            'cualPedido': cualPedido ,
            'motorman': motorman
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        $.post(ajax_object.ajax_url, data, function(response) {
            location.reload(true);
        });

    });


    $("#btnNoDisponible").on('click', function(){
        let motorman = $("#motorman").val();
        var data = {
            'action': 'm_no_disponible',
            'motorman': motorman
        };
        $.post(ajax_object.ajax_url, data, function(response) {
            // console.log(response);
            location.reload();
        });
    });

    $("#btnDisponible").on('click', function(){
        let motorman = $("#motorman").val();
        var data = {
            'action': 'm_disponible',
            'motorman': motorman
        };
        $.post(ajax_object.ajax_url, data, function(response) {
            // console.log(response);
             location.reload(true);
        });
    });




});
