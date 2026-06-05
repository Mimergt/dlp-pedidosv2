
jQuery(document).ready(function ($) {


    $(".boton_abre_modal_order").on('click', function () {
        var la_orden = $(this).attr('data-order');
        $("#pedido_id").val(la_orden)
        $("#pedido_ids").append(la_orden)
        $("#modal_review").modal('show')
        console.log(la_orden)
    })


    $("#boton_guardar_review").on('click', function () {
        var puntaje = $("#customRange3").val()
        var usuario_id = $("#usuario_id").val()
        var pedido_id = $("#pedido_id").val()

        var data = {
            'action': 'funcion_review',
            'pedido_id': pedido_id,
            'usuario_id': usuario_id,
            'puntaje': puntaje,
            'seguir_recibiendo': 1
        };
        jQuery.post(my_ajax_object_review.ajax_url, data, function (response) {
            //console.log(response);
            $("#main_div").hide();
            $("#footer_div").hide();
            $("#gracias_div").css({display: "block"});
            setTimeout(function () {
                $("#modal_review").modal('hide')
            }, 3000);



        });
    });

    $("#no_pedir_boton").on('click', function () {

        var puntaje = 0
        var usuario_id = $("#usuario_id").val()
        var pedido_id = $("#pedido_id").val()

        var data = {
            'action': 'funcion_review',
            'pedido_id': pedido_id,
            'usuario_id': usuario_id,
            'puntaje': 0,
            'seguir_recibiendo': 1
        };

        jQuery.post(my_ajax_object_review.ajax_url, data, function (response) {
            //console.log(response);
            $("#main_div").hide();
            $("#footer_div").hide();
            $("#gracias_div").css({display: "block"});
            setTimeout(function () {
                $("#modal_review").modal('hide')
            }, 3000);
        });


    });



    $("input[type='radio']").click(function () {
        var puntaje = $(this).val()
        $("#customRange3").val(puntaje)
        var sim = $("input[type='radio']:checked").val();
        // alert(sim);
        if (sim < 3) {
            $('.myratings').css('color', '#aaa');
            $(".myratings").text(sim);
        } else {
            $('.myratings').css('color', '#212529');
            $(".myratings").text(sim);
        }
    });


});