jQuery(document).ready(function ($) {

    var cual = $(".paraMapa").val();
    jQuery('.paraMapa').each(function () {
        var identificador = jQuery(this).val();
        var latitud_tienda = $("#latitud_tienda-" + identificador).val();
        var longitud_tienda = $("#longitud_tienda-" + identificador).val();
        var latitud_destino = $("#latitud_destino-" + identificador).val();
        var longitud_destino = $("#longitud_destino-" + identificador).val();
        var motorista = $("#motorista-" + identificador).val();
        var mapaDiv = $("#mapaDiv-" + identificador).val();

        generoMapa(mapaDiv, identificador, latitud_tienda, longitud_tienda, latitud_destino, longitud_destino, motorista);


    });




    function generoMapa(mapaDiv, identificador, latitud_tienda, longitud_tienda, latitud_destino, longitud_destino, motorista) {

        var mapa_del_seguimiento = new L.Map(mapaDiv, {
            'center': [latitud_tienda, longitud_tienda],
            'zoom': 15,
        });
        /*
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: ' Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 20,
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: 'MAPBOX_TOKEN_REMOVED'
                }).addTo(mapa_del_seguimiento);
*/
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: 'delpuente &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(mapa_del_seguimiento);

        //    mapa_del_seguimiento.addControl(new L.Control.Fullscreen());

        var motoristae = L.icon({
            iconUrl: 'https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/imagenes/map-icons/motorista.png',

            iconSize: [38, 38], // size of the icon
            shadowSize: [38, 38], // size of the shadow
            iconAnchor: [38, 38], // point of the icon which will correspond to marker's location

        });
        var marker = L.marker([14.59048, -90.595843333333], { icon: motoristae }).addTo(mapa_del_seguimiento);
        //  var marker = L.marker([0, 0], {icon: motoristae}).addTo(mapa_del_seguimiento);

        var tiendaIcono = L.icon({
            iconUrl: 'https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/imagenes/map-icons/tienda.png',

            iconSize: [38, 38], // size of the icon
            shadowSize: [38, 38], // size of the shadow
            iconAnchor: [38, 38], // point of the icon which will correspond to marker's location

        });
        var tiendaIconoI = L.marker([latitud_tienda, longitud_tienda], { icon: tiendaIcono }).addTo(mapa_del_seguimiento);

        var destinoIcono = L.icon({
            iconUrl: 'https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/imagenes/map-icons/home.png',

            iconSize: [38, 38], // size of the icon
            shadowSize: [38, 38], // size of the shadow
            iconAnchor: [38, 38], // point of the icon which will correspond to marker's location

        });
        var destinoI = L.marker([latitud_destino, longitud_destino], { icon: destinoIcono }).addTo(mapa_del_seguimiento);

        $('body').on('shown.bs.modal', function () {
            console.log('aca entra chango, posta')
            setTimeout(function () {
                mapa_del_seguimiento.invalidateSize();
            }, 1000);
        });

        /*
                setTimeout(function () {
                    mapa_del_seguimiento.invalidateSize()
                }, 100);
        */
        const interval = setInterval(function () {


            $.ajax({
                type: "POST", // la variable type guarda el tipo de la peticion GET,POST,..
                url: "https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/appMotoristas/tt.php", //url guarda la ruta hacia donde se hace la peticion
                data: { "motorista": motorista }, // data recive un objeto con la informacion que se enviara al servidor
                dataType: "json",
                success: function (datos) { //success es una funcion que se utiliza si el servidor retorna informacion

                    //  console.log(datos)
                    //  return false
                    let data = {}

                    if (datos === null) {
                        data.lat = latitud_tienda
                        data.lng = longitud_tienda
                    } else {
                        data.lat = datos.lat
                        data.lng = datos.lng
                    }

                    mapa_del_seguimiento.panTo(new L.LatLng(data.lat, data.lng));
                    var newLatLng = new L.LatLng(data.lat, data.lng);
                    marker.setLatLng(newLatLng);
                    mapa_del_seguimiento.invalidateSize()



                }
            })

            /*
             fetch('https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/appMotoristas/tt.php?motorista='+motorista)
             .then(
             function(response) {
             if (response.status !== 200) {
             console.log('Looks like there was a problem. Status Code: ' +
             response.status);
             return;
             }

             response.json().then(function(data) {
             mapa_del_seguimiento.panTo(new L.LatLng(data.lat, data.lng));
             var newLatLng = new L.LatLng(data.lat, data.lng);
             marker.setLatLng(newLatLng);
             mapa_del_seguimiento.invalidateSize()
             });
             }
             )
             .catch(function(err) {
             console.log('Fetch Error :-S', err);
             });

             */
        }, 5000);


    }


});
