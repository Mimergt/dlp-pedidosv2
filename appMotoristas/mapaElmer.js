jQuery(document).ready(function () {
return false
    var mapaa = new L.Map('mim', {
        'center': [14.59048, -90.595843333333],
        'zoom': 18,
    });

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 20,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'MAPBOX_TOKEN_REMOVED'
    }).addTo(mapaa);

    var motorista = L.icon({
        iconUrl: 'https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/imagenes/map-icons/motorista.png',
        shadowUrl: 'leaf-shadow.png',

        iconSize: [38, 38], // size of the icon
        shadowSize: [38, 38], // size of the shadow
        iconAnchor: [38, 38], // point of the icon which will correspond to marker's location

    });


    var marker = L.marker([14.59048, -90.595843333333], {icon: motorista}).addTo(mapaa);


    const interval = setInterval(function () {

        fetch('https://delpuente.com.gt/wp-content/plugins/manejoPedidos2/appMotoristas/tt.php')
                .then(
                        function (response) {
                            if (response.status !== 200) {
                                console.log('Looks like there was a problem. Status Code: ' +
                                        response.status);
                                return;
                            }

                            // Examine the text in the response
                            response.json().then(function (data) {
                                // console.log(data.respuesta);

                                mapaa.panTo(new L.LatLng(data.respuesta.lat, data.respuesta.lng));

                                var newLatLng = new L.LatLng(data.respuesta.lat, data.respuesta.lng);
                                marker.setLatLng(newLatLng);


                            });
                        }
                )
                .catch(function (err) {
                    console.log('Fetch Error :-S', err);
                });
    }, 5000);




});
