jQuery(document).ready(function ($) {


    $(".abro_modal_mapa").on('click', function(){
       
        const orden_id = $(this).attr('data-orden')
        const latitud_tienda = $(this).attr('data-lattienda')
        const longitud_tienda = $(this).attr('data-longitudtienda')    
        const latitud_destino= $(this).attr('data-latituddestino')  
        const longitud_destino= $(this).attr('data-longituddestino')  
        const motorista= $(this).attr('data-motorista')   
        
        const mapa_modal_div = 'mapa_modal_div_'+orden_id
        /*
        console.log(orden_id)
        console.log(latitud_tienda)
        console.log(longitud_tienda)
        console.log(latitud_destino)
        console.log(longitud_destino)
        console.log(motorista)
        console.log(mapa_modal_div)
*/




        const mapa_del_seguimiento_modal = new L.Map(mapa_modal_div, {
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
	}).addTo(mapa_del_seguimiento_modal);

    $("#modal_mapa_"+orden_id).modal('show')

    setTimeout(function () {
        mapa_del_seguimiento_modal.invalidateSize();
    }, 1000);

   
    $('.order-map-modal').on('hidden.bs.modal', function () {
        // do something…
        console.log('se cierra')
        //document.getElementById('weathermap').innerHTML = "<div id='map' style='width: 100%; height: 100%;'></div>";
        $(".mapa_modal_div").html('');
        if (mapa_del_seguimiento_modal != undefined) mapa_del_seguimiento_modal.remove();
      })
      

    var motoristae = L.icon({
        iconUrl: 'https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/imagenes/map-icons/motorista.png',

        iconSize: [38, 38], // size of the icon
        shadowSize: [38, 38], // size of the shadow
        iconAnchor: [38, 38], // point of the icon which will correspond to marker's location

    });
    var marker = L.marker([14.59048, -90.595843333333], { icon: motoristae }).addTo(mapa_del_seguimiento_modal);
    //  var marker = L.marker([0, 0], {icon: motoristae}).addTo(mapa_del_seguimiento);

    var tiendaIcono = L.icon({
        iconUrl: 'https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/imagenes/map-icons/tienda.png',

        iconSize: [38, 38], // size of the icon
        shadowSize: [38, 38], // size of the shadow
        iconAnchor: [38, 38], // point of the icon which will correspond to marker's location

    });
    var tiendaIconoI = L.marker([latitud_tienda, longitud_tienda], { icon: tiendaIcono }).addTo(mapa_del_seguimiento_modal);

    var destinoIcono = L.icon({
        iconUrl: 'https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/imagenes/map-icons/home.png',

        iconSize: [38, 38], // size of the icon
        shadowSize: [38, 38], // size of the shadow
        iconAnchor: [38, 38], // point of the icon which will correspond to marker's location

    });
    var destinoI = L.marker([latitud_destino, longitud_destino], { icon: destinoIcono }).addTo(mapa_del_seguimiento_modal);


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

                mapa_del_seguimiento_modal.panTo(new L.LatLng(data.lat, data.lng));
                var newLatLng = new L.LatLng(data.lat, data.lng);
                marker.setLatLng(newLatLng);

            }
        })

    }, 5000);

})
   


});
