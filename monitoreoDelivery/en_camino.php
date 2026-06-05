<?php

function en_camino() {

    function usuario_puede()
    {
        $user = wp_get_current_user();
        if (in_array('shop_manager', (array)$user->roles)) {
            return 1;
        } else {
            return 0;
        }
    }

    if(usuario_puede() == 0){
        header("location:../index.php");
        exit;

    }
    echo <<<PRI
<div class="row">
<div class="col-lg-9">
PRI;
    global $wpdb;
    $prefijo = $wpdb->prefix;
    $tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";
    $tabla_asignacion_tiendas = $prefijo."a_asignacion_pedidos";
    $tablaPosts = $prefijo."posts";
    $tablaPostMeta = $prefijo."postmeta";
    $tabla_usuarios = $prefijo."users";

    $sql  = "SELECT DISTINCT(a.delivery_boy_id) as delivery_boy_id, b.user_nicename  FROM $tabla_asignacion_motoristas a
                                                         JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                         JOIN $tabla_asignacion_tiendas c ON a.delivery_boy_id = c.delivery_boy_id
                                        WHERE  a.estado = 1 AND (c.status = 1 AND c.fin_asignacion IS NULL) ";
    $delivery_en_camino = $wpdb->get_results($sql);

    foreach($delivery_en_camino as $v){
        $array_en_camino[] = $v->delivery_boy_id;
    }

    $sql  = "SELECT a.delivery_boy_id, b.user_nicename, a.estado  FROM $tabla_asignacion_motoristas a
                                                         JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                         WHERE  1 ";
    $delivery_todos = $wpdb->get_results($sql);


    function get_order_detailsGustavo2($order_id){

        global $wpdb;

        $options_woofood = get_option('woofood_options');
        $woofood_enable_order_accepting = $options_woofood['woofood_enable_order_accepting'];
        // 1) Get the Order object
        $order =  new WC_Order( $order_id );
        $order_data = $order->get_data(); // The Order data


        // 3) Get the order items
        $items = $order->get_items();
        $order_phone = $order->get_billing_phone();
        $order_email = $order->get_billing_email();
        $order_date  = $order_data['date_created']->date(get_option('date_format')." ".get_option('time_format'));

        $order_type = get_post_meta($order_id, 'woofood_order_type', true);

        if(!$order_type)
        {
            $order_type = woofood_get_default_order_type();
        }
        $woofood_time_to_deliver  = get_post_meta($order_id, 'woofood_time_to_deliver', true);
        $default_date_format = get_option("date_format");
        $default_time_format = get_option("time_format");

        // tengo el id de la tienda
        $tienda_id = get_post_meta($order_id, 'extra_store_name', true);
        //busco los motoristas de esta tienda que no tengan pedido asignado
        $prefijo = $wpdb->prefix;
        $tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";
        $tabla_asignacion_tiendas = $prefijo."a_asignacion_pedidos";

        $tabla_usuarios = $prefijo."users";

        $sql  = "SELECT a.delivery_boy_id, b.user_nicename  FROM $tabla_asignacion_motoristas a JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID WHERE FIND_IN_SET('$tienda_id', a.tienda_id )  AND a.estado = 1";
        $delivery_libres = $wpdb->get_results($sql);

        $selLibres = "<option value=''>Seleccione Motorman</option>";
        foreach($delivery_libres as $dl){
            $selLibres .= "<option value='".$dl->delivery_boy_id."'>".$dl->user_nicename."</option>";
        }

        if($woofood_time_to_deliver)
        {
            if($woofood_time_to_deliver!="now" && $woofood_time_to_deliver!="asap"  )
            {
                $woofood_time_to_deliver = date_i18n($default_time_format , strtotime($woofood_time_to_deliver  ) );

            }
        }
        $woofood_date_to_deliver  = get_post_meta($order_id, 'woofood_date_to_deliver', true);
        if($woofood_date_to_deliver)
        {
            if($woofood_date_to_deliver!=current_time("Y-m-d"))
            {
                $woofood_date_to_deliver = date_i18n( $default_date_format, strtotime($woofood_date_to_deliver  ) );

            }
            else
            {
                $woofood_date_to_deliver = esc_html('Today', 'woofood-plugin');
            }
        }
        $order_status = $order->get_status();
        $order_type_text = woofood_get_order_type_by_key($order_type);

        $shipping_address =   $order->get_formatted_shipping_address();
        $billing_address =   $order->get_formatted_billing_address();

        $name = $order->get_billing_first_name().' '.$order->get_billing_last_name();
        $billing_address_html = !empty($shipping_address) ? $shipping_address : $billing_address;
        $notas = $order->get_customer_note();
        $total = get_woocommerce_currency_symbol().$order->get_total();
        $forma_pago = $order->payment_method_title;



        // Get NIT
        $nit = get_post_meta( $order_id, 'billing_nit', true );

         $st = 'En camino.';

        if($order->get_status() !=="en_camino" ){} else {

            if($order_type_text === "Delivery"){
                $lineaDireccion = $billing_address_html;
            } else {
                $lineaDireccion = '';
            }
            echo <<<ACA

<nav class="navbar navbar-expand-md navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item grande">
                Orden ID: $order_id $order_type_text <br>
                $lineaDireccion
            </li>
            <li class="nav-item grande">
                &nbsp;&nbsp;&nbsp;&nbsp;Status: <b>$st</b>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
               <button data-toggle="collapse" data-target="#det$order_id" class="btn btn-success">Detalles</button>
            </li>

        </ul>
    </div>
</nav>
ACA;

            if($order_type_text === "Delivery"){

                $fondo = "fondoEnCamino";

                $sql  = "SELECT b.user_nicename, c.asignado_el  FROM $tabla_asignacion_motoristas a JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                                    JOIN $tabla_asignacion_tiendas c ON a.delivery_boy_id = c.delivery_boy_id  WHERE c.pedido_id = $order_id ORDER BY b.ID DESC LIMIT 1";
                $ds = $wpdb->get_row($sql);

                $ahora = time();
                $inicio = strtotime($ds->asignado_el);

                $tiempoH = gmdate("H", ($ahora - $inicio));
                $tiempoM = gmdate("i", ($ahora - $inicio));
                $tiempoS = gmdate("s", ($ahora - $inicio));

                $todosLosSegundos = ($tiempoM * 60) + $tiempoS;

                echo <<<ACA

<nav class="navbar navbar-expand-lg $fondo ">

ACA;

                if($order->get_status() === "en_camino") {

                    $sql  = "SELECT b.user_nicename, c.asignado_el  FROM $tabla_asignacion_motoristas a JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                                    JOIN $tabla_asignacion_tiendas c ON a.delivery_boy_id = c.delivery_boy_id  WHERE c.pedido_id = $order_id";
                    $ds = $wpdb->get_row($sql);

                    echo <<<EOL

<ul class="navbar-nav mr-auto">
            <li class="nav-item grande">
                Motorista: $ds->user_nicename
            </li>
            <!--
            <li class="nav-item grande">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button class="btn btn-success terminarBtn" data-id="$order_id">Orden entregada</button>
            </li>
            -->
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item grande contar" data-id="$order_id">
            <input type="hidden" id="Totalsegundos$order_id" value="$todosLosSegundos">
               <br>
               <span class=""  id="horas$order_id">$tiempoH</span>
              <span class="">:</span>
               <span class="blanca_letra"  id="minutos$order_id">$tiempoM</span>
               <span class="blanca_letra">:</span>
               <span class="blanca_letra" id="segundos$order_id">$tiempoS</span>
            </li>
        </ul>
EOL;
                }


            }

            echo <<<ACA

</nav>
<div id="det$order_id" class="collapse">
<div class="row">&nbsp;</div>
<div class="row">
<div class="col-4 detalles_cliente">
<h2>Detalles del Cliente</h2>
<div class="col mb-3 mt-10 bg-light py-2"><h4>Nombre:</h4><h5> $name</h5></div>
<div class="col mb-3 mt-10 bg-light py-2"><h4>NIT:</h4><h5> $nit</h5></div>
<div class="col mb-3 mt-10 bg-light py-2"><h4>Telefono:</h4><h5> $order_phone</h5></div>
<div class="col mb-3 mt-10 text-light bg-dark py-2"><h4>Direccion:</h4><h5> $billing_address_html</h5></div>
<div class="col mb-3 mt-10 text-light bg-dark py-2"><h4>Notas:</h4><h5> $notas</h5></div>
</div>
<div class="col-8">
<h2>Detalles del Pedido</h2>
<div class="detalles_pedido col mb-3 mt-10 bg-light py-2">
ACA;
            $curr_numn = 1;
            foreach ( $items as $item ) {
                echo "<b>".$curr_numn.") </b>".$item['name']." ".__('<b>Cantidad:</b>','woofood-plugin').$item['qty'];
                wc_display_item_meta( $item );
                ++$curr_numn;
            }

            echo <<<EOL
</div>
</div>
</div>
<hr>
<div class="row">
<div class="col-2"><b>Fecha del Pedido</b></div>
<div class="col-2"><b>Forma de Pago</b></div>
<div class="col-2"><b>Tipo de Orden</b></div>
<div class="col-2"><b>Fecha de Pickup</b></div>
<div class="col-2"><b>Hora de Pickup</b></div>
<div class="col-2"><b>Total</b></div>
</div>
<div class="row">
<div class="col-2">$order_date</div>
<div class="col-2">$forma_pago</div>
<div class="col-2">$order_type_text</div>
<div class="col-2">$woofood_date_to_deliver</div>
<div class="col-2">$woofood_time_to_deliver</div>
<div class="col-2">$total</div>
</div>
<div class="row">
<div class="col-12">
EOL;


echo do_shortcode( '[mapa_seguimiento pedido_id="'.$order_id.'"  tipo="0" referer="en_camino"]' );


      echo <<<EOL
</div>
</div>
</div>
<hr><br><br>

EOL;

        }

    }//end function

    $prefijo = $wpdb->prefix;

    $tablaPosts = $prefijo."posts";
    $tablaPostMeta = $prefijo."postmeta";
$sql = "Select distinct(b.ID) as ID FROM $tablaPostMeta  a JOIN $tablaPosts b ON a.post_id = b.ID WHERE b.post_status LIKE 'wc-%' AND b.post_date_gmt > NOW() - INTERVAL 12 HOUR ";
  //  $sql = "Select b.ID FROM $tablaPostMeta a JOIN $tablaPosts b ON b.post_id = a.ID WHERE b.post_status LIKE 'wc-%'  AND a.post_date > NOW() - INTERVAL 48 HOUR";
    //echo $sql;
    // $sql = "Select b.ID FROM $tablaPostMeta a JOIN $tablaPosts b ON a.post_id = b.ID WHERE b.post_status LIKE 'wc-%' AND a.post_date > NOW() - INTERVAL 48 HOUR";
    $ord = $wpdb->get_results($sql);
    foreach($ord as $v){
        $ordenes[] = $v->ID;
    }




    $sql = "SELECT * FROM $tablaPosts WHERE post_type LIKE 'extra_store%' ";
    $stores = $wpdb->get_results($sql);
    $selTienda = "<option value='todos'>Todos</option>";
    foreach($stores as $v){
        $tienda[$v->ID] = $v->post_title;
        $selTienda .= "<option value='".$v->ID."'>$v->post_title</option>";
    }

    $ordenesTotales = 0;
    $ordenesDlv = 0;
    $ordenesPckp = 0;

    $logOutUrl = wp_logout_url('/index.php');

    foreach($ordenes as $uu){
        $order_typeT = get_post_meta($uu, 'woofood_order_type', true);
        if($order_typeT === "delivery"){
            $ordenesDlv++;
        } else {
            $ordenesPckp++;
        }
        $ordenesTotales ++;
    }


    echo <<<ETE
<style>
ul {
  list-style-type: none !important;;
}
.grande{
font-size: 1.5em;
font-weight: bolder;
}
.fondoProcesando {
background-color: #C22225 !important;
}
.fondoEnviado{
background-color: #115EAB !important;
}
.fondoListoParaRecoger {
background-color: #FCB712 !important;
}
.fondoReadyPickup {
background-color: #AEC12D !important;
}
.fondoEsperandoAceptacion {
background-color: #ea8225 !important;
}
.fondoEnCamino {
background-color: #58d2aa !important;
}
.fondoRechaza {

 -webkit-animation: NAME-YOUR-ANIMATION 1s infinite; /* Safari 4+ */
  -moz-animation:    NAME-YOUR-ANIMATION 1s infinite; /* Fx 5+ */
  -o-animation:      NAME-YOUR-ANIMATION 1s infinite; /* Opera 12+ */
  animation:         NAME-YOUR-ANIMATION 1s infinite; /* IE 10+, Fx 29+ */
}

@-webkit-keyframes NAME-YOUR-ANIMATION {
0%, 49% {
    background-color: rgb(117,209,63);
    border: 3px solid #e50000;
}
50%, 100% {
    background-color: #e50000;
    border: 3px solid rgb(117,209,63);
}
}

h1, h2, h3, h4, h5, h6 {
    color: unset !important;
}

.detalles_pedido {
    font-size: 1.3em !important;
}

h1.main_title {
    display: none;
}

.fondoFiltros {
background-color: #4a4848 !important;
color: #FFFFFE;
}

.tercio{
width: 33%;;
}

table, th, td {
  border: #4a4848 !important;
}
.blanca_letra{
color: #FFFFFE;
}
</style>
<!--
                <table cellspacing="0" cellpadding="0" style="width: 100%;border: #4a4848;" class="fondoFiltros">
                <tr>
                <td style="width: 33%">Refresco Automático</td>
                <td style="width: 33%">Filtrar estado de pedido</td>
                <td style="width: 33%">Filtrar por tienda</td>
                </tr>
                <tr>
                <td>
                <select id="cadaCuantoRefresco" class="form-control">
                <option value="1" selected>1 Min.</option>
                <option value="2" >2 Min.</option>
                <option value="3" >3 Min.</option>
                <option value="4" >4 Min.</option>
                <option value="5" >5 Min.</option>
                </select>
                </td>
<td>
<select id="estadosSel" class="form-control">
<option value="estado_todos" selected>Todos</option>
<option value="processing">Preparando</option>
<option value="en_camino">En Camino</option>
<option value="eam">Esperando Aceptación Motorista</option>
<option value="motorista_rechaza">Motorista Rechaza</option>
<option value="dlv">Listo Para Recoger</option>
<option value="rtp">Listo para Entregar</option>
</select>
</td>
<td>
<select  id="tiendasSel" class="form-control">
$selTienda
</select>
</td>
</tr>
<tr><td>
                <input type="hidden" id="pausarRfrsh" value="0">
                <button class="btn btn-primary btn-block" id="pausarRfrshBtn">Pausar Refresco Automático</button>
</td>
<td>
<div class="btn-group btn-group-toggle" data-toggle="buttons">
  <label class="btn btn-primary active">
    <input type="radio" name="queTipoMostrar" value="todos" autocomplete="off" checked> Todos
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="queTipoMostrar" value="Delivery" autocomplete="off"> Delivery
  </label>
  <label class="btn btn-primary">
    <input type="radio" name="queTipoMostrar" value="Pickup" autocomplete="off"> Pickup
  </label>
</div>
</td>
</tr>
<tr>
<td>Total de Pedidos <strong>$ordenesTotales</strong></td>
<td>Delivery <strong>$ordenesDlv</strong></td>
<td>Pickup <strong>$ordenesPckp</strong></td>
</tr>

</table>
-->



        <div class="row">
        <div class="col-lg-6">
        <a class="btn btn-primary btn-block" href="../panel-de-monitoreo">Ir al panel general</a>
        </div>
        <div class="col-lg-6">
        <a class="btn btn-danger btn-block" id="btnSalir" href="$logOutUrl">Cerrar Sesión</a>
        </div>
        </div>


 <div class="row">&nbsp;</div>
 <div class="row">&nbsp;</div>

ETE;

    foreach($ordenes as $c => $v){
        echo get_order_detailsGustavo2($v);
    }

    echo <<<ETE

    </div>
<div class="col-lg-3">

    <div class="row">
<div class="col-lg-12">
<select class="form-control" id="selector_tiendas">
$selTienda
</select>
<br>
<h3>Motoristas</h3>
<div id="motoristas_activos"></div>
</div>
</div>
<!--
  <div class="row">
<div class="col-lg-12">
<h3><span class="badge badge-primary">Motoristas en Ruta</span></h3>
<div id="motoristas_en_ruta"></div>
</div>
</div>

  <div class="row">
<div class="col-lg-12">
<h3><span class="badge badge-warning">Motoristas Inactivos</span></h3>
<div id="motoristas_inactivos"></div>
</div>
</div>
-->





</div>

</div>



ETE;



}
