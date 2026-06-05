

jQuery(document).ready(function ($) {

    let pausarEstado = $("#pausarRfrsh").val();
    $(".boton").on('click', function () {

        let cualPedido = $(this).attr('data-id');
        let motorman = $("#motormans-" + cualPedido).val();

        if (motorman === '') {
            return false;
        }
        var data = {
            'action': 'asigna_pedido',
            'cualPedido': cualPedido,
            'motorman': motorman
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function (response) {
            console.log(response);
            location.reload(true);
        });
    });

    $("#pausarRfrshBtn").on('click', function () {
        let pausarEstado = $("#pausarRfrsh").val();
        console.log(pausarEstado);
        if (pausarEstado == "0") {
            $("#pausarRfrsh").val('1');
            $("#pausarRfrshBtn").text('Reanudar Refresco Automático');

            $(this).removeClass("btn-primary");
            $(this).addClass("btn-warning");

        } else {
            $("#pausarRfrsh").val('0');
            $("#pausarRfrshBtn").text('Pausar Refresco Automático');
            $(this).addClass("btn-primary");
            $(this).removeClass("btn-warning");

        }

    });

    $('input[type=radio][name=queTipoMostrar]').change(function () {
        if (this.value == 'todos') {
            $(".Delivery").show();
            $(".Pickup").show();
        } else if (this.value == 'Delivery') {
            $(".Delivery").show();
            $(".Pickup").hide();
        } else {
            $(".Delivery").hide();
            $(".Pickup").show();
        }
    });


    var hi = new Date();
    const si = hi.getTime();


    const interval = setInterval(function () {

        /*
         var hf = new Date();
         const sf = hf.getTime();


         let segRef = $("#cadaCuantoRefresco").val();

         if ((sf - si) >= (parseInt(segRef) * 60000)) {
         let pausarEstado = $("#pausarRfrsh").val();
         if (pausarEstado == "0") {
         //console.log("refresca");
         location.reload(true);

         }
         }
         */
        location.reload(true);
    }, 300000);

    $("#estadosSel").on('change', function () {

        escondoEstados();

        let  estadoPedido = $("#estadosSel").val();

        if (estadoPedido == "estado_todos") {
            $(".estado_todos").show();
            return false;
        }
        if (estadoPedido == "rtp") {
            $(".rtp").show();
        }

        if (estadoPedido == "dlv") {
            $(".dlv").show();
        }
        if (estadoPedido == "processing") {
            $(".processing").show();
        }
        if (estadoPedido == "eam") {
            $(".eam").show();
        }
        if (estadoPedido == "en_camino") {
            $(".en_camino").show();
        }
        if (estadoPedido == "motorista_rechaza") {
            $(".motorista_rechaza").show();
        }


    })
    // tipoTodos tipoDelivery tipoPckp estado_procesando estado_eam estado_motorista_rechaza estado_en_camino estado_rtp estado_dlv

    function escondoEstados() {
        $(".estado_todos").hide();
        $(".rtp").hide();
        $(".dlv").hide();
        $(".processing").hide();
        $(".eam").hide();
        $(".en_camino").hide();
        $(".motorista_rechaza").hide();
    }


    $("#tiendasSel").on('change', function () {
        let cual = $(this).val();
        if (cual === 'todos') {
            $(".todas_las_tiendas").show();
        } else {
            $(".todas_las_tiendas").hide();
            $("." + cual).show();
        }
    })


    $(".btnCancela").on('click', function () {
        let cual = $(this).attr('data-id');

        Swal.fire({
            title: 'Atención',
            text: "Esta acción es irreversible",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var data = {
                    'action': 'cancelar_pedido',
                    'cualPedido': cual
                };
                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
                jQuery.post(ajax_object.ajax_url, data, function (response) {
                    // console.log(response);
                    location.reload(true);
                });
            }
        })

        /*
         var data = {
         'action': 'asigna_pedido',
         'cualPedido': cualPedido ,
         'motorman': motorman
         };
         // We can also pass the url value separately from ajaxurl for front end AJAX implementations
         jQuery.post(ajax_object.ajax_url, data, function(response) {
         console.log(response);
         location.reload(true);
         });

         */
    })
});
