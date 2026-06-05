jQuery(document).ready(function ($) {

    $("#btn_terminado").on('click', function () {


        var cual = $(this).attr('data-order')

        var data = {
            'action': 'agrego_meta_horario',
            'cual': cual
        };

        jQuery.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {'action': 'agrego_meta_horario', 'cual': cual},
            async: false,
            success: function (result) {

            },
            complete: function (res) {
                location.reload(true);
            }
        });



    })



})
