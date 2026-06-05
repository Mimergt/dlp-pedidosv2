jQuery(document).ready(function ($) {

    
    $('.contar').each(function () {
        contadorTiempo($(this).attr('data-id'));
    })

    $('.contar2').each(function () {
        contadorTiempo2($(this).attr('data-id'));
    })

    $(".terminarBtn").on('click', function () {
        $(this).prop('disabled', true);

        let cualPedido = $(this).attr('data-id');
        var data = {
            'action': 'termina_pedido',
            'cualPedido': cualPedido
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function (response) {
            //   console.log(response);
            location.reload(true);
        });
    })
});

function contadorTiempo2(cual) {
    var horasLabel = document.getElementById("horas2" + cual);
    var totalSeconds = document.getElementById("Totalsegundos2" + cual).value;

    setInterval(setTime, 1000);

    function setTime() {
        ++totalSeconds;
        const este = new Date(totalSeconds * 1000).toISOString().substr(11, 8)
        horasLabel.innerHTML = pad(este);
    }

}

function contadorTiempo(cual) {
    var horasLabel = document.getElementById("horas" + cual);
    var totalSeconds = document.getElementById("Totalsegundos" + cual).value;
    setInterval(setTime, 1000);

    function setTime() {
        ++totalSeconds;

        const este = new Date(totalSeconds * 1000).toISOString().substr(11, 8)
        horasLabel.innerHTML = pad(este);
        //      console.log(este);

    }

}
function pad(val) {
    var valString = val + "";
    if (valString.length < 2) {
        return "0" + valString;
    } else {
        return valString;
    }
}
