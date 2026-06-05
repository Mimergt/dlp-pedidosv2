
jQuery(document).ready(function ($) {
    console.log('hola Elmer, que tal?')

    $(".boton_abre_modal_order").on('click', function () {
        var la_orden = $(this).attr('data-order');
        $("#pedido_id").val(la_orden)
        $("#pedido_ids").append(la_orden)
        $("#modal_review").modal('show')
        console.log(la_orden)
    })


    $("#boton_guardar_review").on('click', function () {
       
        var calificacion_motorista = $("#calificacion_motorista").val()
        var calificacion_producto = $("#calificacion_producto").val()
        var calificacion_plataforma = $("#calificacion_plataforma").val()
        var comentario = $("#comentario").val()
        var usuario_id = $("#usuario_id").val()
        var pedido_id = $("#pedido_id").val()

        var data = {
            'action': 'funcion_review',
            'pedido_id': pedido_id,
            'usuario_id': usuario_id,
            'calificacion_motorista': calificacion_motorista,
            'calificacion_producto': calificacion_producto,
            'calificacion_plataforma': calificacion_plataforma,
            'comentario': comentario,
            'seguir_recibiendo': 1
        };
        
        jQuery.post(my_ajax_object_review.ajax_url, data, function (response) {
            console.log(response);
            $("#main_div").hide();
            $("#footer_div").hide();
            $("#gracias_div").css({display: "block"});
            setTimeout(function () {
                $("#modal_review").modal('hide')
            }, 3000);



        });
    });

    $("#no_pedir_boton").on('click', function () {

        var usuario_id = $("#usuario_id").val()
        var pedido_id = $("#pedido_id").val()

        var data = {
            'action': 'funcion_review',
            'pedido_id': pedido_id,
            'usuario_id': usuario_id,
            'seguir_recibiendo': 0
        };
    
        jQuery.post(my_ajax_object_review.ajax_url, data, function (response) {
            
            $("#main_div").hide();
            $("#footer_div").hide();
            $("#gracias_div").css({display: "block"});
            setTimeout(function () {
                $("#modal_review").modal('hide')
            }, 3000);
        });


    });



    $(".califica_motorista_radio").on('click' ,function () {       
        var puntaje = $(this).val()
        $("#calificacion_motorista").val(puntaje)
        var sim = puntaje;
        // alert(sim);
        if (sim < 3) {
            $('.myratings_calificacion_motorista').css('color', '#aaa');
            $(".myratings_calificacion_motorista").text(sim);
        } else {
            $('.myratings_calificacion_motorista').css('color', '#212529');
            $(".myratings_calificacion_motorista").text(sim);
        }
    });

    $(".califica_producto_radio").on('click' ,function () {       
        var puntaje = $(this).val()
        $("#calificacion_producto").val(puntaje)
        var sim = puntaje;
        // alert(sim);
        if (sim < 3) {
            $('.myratings_calificacion_producto').css('color', '#aaa');
            $(".myratings_calificacion_producto").text(sim);
        } else {
            $('.myratings_calificacion_producto').css('color', '#212529');
            $(".myratings_calificacion_producto").text(sim);
        }
    });


    $(".califica_plataforma_radio").on('click' ,function () {       
        var puntaje = $(this).val()
        $("#calificacion_plataforma").val(puntaje)
        var sim = puntaje;
        // alert(sim);
        if (sim < 3) {
            $('.myratings_calificacion_plataforma').css('color', '#aaa');
            $(".myratings_calificacion_plataforma").text(sim);
        } else {
            $('.myratings_calificacion_plataforma').css('color', '#212529');
            $(".myratings_calificacion_plataforma").text(sim);
        }
    });

    



});