<?php
$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";

$args = array( 'post_type' => 'extra_store', 'posts_per_page'=>-1 );

$tiendas = query_posts( $args );

foreach($tiendas as $c => $v){
    $arrayTiendas[$v->ID] = $v->post_title;
}




$args = array(
    'role'    => 'deliveryboy_user',
    'orderby' => 'user_nicename',
    'order'   => 'ASC'
);
$users = get_users( $args );

foreach($users as $c => $v){
    $arrayDelivery[$v->ID] = $v->user_nicename;
}




echo <<<EOL

<div class="container">
<div class="row">
<div class="col text-center">
<h3>Asignacion de tienda</h3>
</div>
</div>
EOL;

foreach($arrayDelivery as $c => $v){
    $asignadoA = $wpdb->get_row( $wpdb->prepare( "SELECT tienda_id FROM $tabla_asignacion_motoristas  WHERE delivery_boy_id = $c" ) );
    $restoAsignado = explode(",",$asignadoA->tienda_id);




    echo <<<EOL
    <div class='row'>

                <div class='col-2'>$v</div>
                <div class='col'>
                <select id='resto_$c' class='form-control pi' multiple >
                     <option value=''>Seleccione tienda</option>
EOL;


            foreach($arrayTiendas as $q => $w){
                if(in_array($q, $restoAsignado) ){
                echo "<option value='$q' selected >$w</option>";
                } else {
                    echo "<option value='$q' > $w</option>";
                }
            }
    echo <<<EOL
            </select>
            </div>
            <div class='col'><button class='btn btn-success actualizaBtn' data-id="$c">Actualizar</button>

            </div>

            </div>
            <div class='row'>&nbsp;</div>
            <div class='row'><hr></div>
<div class='row'>&nbsp;</div>
EOL;

}

echo "</div>";
