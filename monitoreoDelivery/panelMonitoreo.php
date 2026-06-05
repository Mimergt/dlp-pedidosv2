<?php

function panelMonitoreo() {
    global $wpdb;



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
$current_user_id = get_current_user_id();
if ($current_user_id == "569"){
    $class_descarga = "mostrar";
}


    if(!empty($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'){

        $order_id = (int)$_POST['order_id'];
        $order =  new WC_Order( $order_id );

        if($_POST['action'] === 'cambiarTienda'){
            if($_POST['tiendasDisponibles'] === "0"){
                echo "<h3>Debe seleccionar una tienda para hacer el cambio.</h3>";
            } else {

                $tabla =  $wpdb->prefix.'postmeta';
                $nuevaTienda = $_POST['tiendasDisponibles'];
                $laOrden = $order_id;
                $wpdb->query($wpdb->prepare("UPDATE $tabla SET meta_value = $nuevaTienda WHERE post_id = '$laOrden' AND meta_key = 'extra_store_name'"));

            }
        } else {
            $aQue = $_POST['action'];
            $order->update_status( $aQue );
        }
        $_POST['action'] = '';
        unset($_POST);
    }

    function get_order_detailsGustavo2($order_id){
       
        global $wpdb;

        $prefijo = $wpdb->prefix;

        $sql = "SELECT * FROM ".$prefijo."posts WHERE post_type LIKE 'extra_store%' ";
        $stores = $wpdb->get_results($sql);
        $sel = "<select name='tiendasDisponibles' class='form-control'>";
        $sel .= "<option value='0'>Seleccione</option>";
        foreach($stores as $v){
            $sel .= "<option value='$v->ID'>$v->post_title</option>";
        }
        $sel .= "</select>";

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
        $order_city = $order->get_billing_city();



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
        //obtengo el nombre de la tienda del meta
        $tienda_name = get_post_meta($order_id, 'tienda_asignada', true);
       
        

        //busco los motoristas de esta tienda que no tengan pedido asignado
        $prefijo = $wpdb->prefix;
        $tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";
        $tabla_asignacion_tiendas = $prefijo."a_asignacion_pedidos";

        $tabla_usuarios = $prefijo."users";

        $sql  = "SELECT a.delivery_boy_id, b.user_nicename  FROM $tabla_asignacion_motoristas a JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID WHERE FIND_IN_SET('$tienda_id', a.tienda_id )  AND a.estado = 1";
        $delivery_libres = $wpdb->get_results($sql);

        $selLibres = "<option value=''>Seleccione Motorista</option>";
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
        //$billing_address_html = !empty($shipping_address) ? $shipping_address : $billing_address;

        $billing_address_html = $order->get_billing_address_2();
        $notas = $order->get_customer_note();
        $total = get_woocommerce_currency_symbol().$order->get_total();
        $forma_pago = $order->payment_method_title;

        $usuario_id = $order->get_user_id();

        // Get NIT
        $nit = get_post_meta( $order_id, 'billing_nit', true );

        //Get Source: Android - iOS - Web
        $source = get_post_meta( $order_id, 'device', true );


// get_delivery_boys( $request )

        $pst = $order->get_status();
        if($pst === "rtp"){$st = 'El Cliente esta en el Restaurante.';}
        elseif ($pst === "dlv"){$st = 'Listo para entregar.';}
        elseif ($pst === "processing"){$st = 'Preparando.';}
        elseif ($pst === "eam"){$st = 'Esperando aceptación motorista.';}
        elseif ($pst === "en_camino"){$st = 'En camino.';}
        elseif ($pst === "motorista_rechaza"){$st = 'Motorista rechaza el envio.';}
        else {$st = $pst;}

        $estado_orden = $order->get_status();
        $tipo_orden = $order_type_text;

        if($order->get_status() ==="completed" || $order->get_status() ==="trash" || $order->get_status() ==="cancelled" || $order->get_status() ==="pending" || $order->get_status() ==="refunded" ){} else {

            if($order_type_text === "Delivery"){
                $lineaDireccion = $billing_address_html;
            } else {
                $lineaDireccion = '';
            }


            $ahora = strtotime(date('Y-m-d H:i:s')) - (3600 * 6);
            $hora1 = $order->get_date_created()->format ('Y-m-d H:i:s');
            $inicio = strtotime($hora1);
            $tiempoH = gmdate("H:i:s", ($ahora - $inicio));
            $todosLosSegundos = ($ahora - $inicio);


            echo <<<ACA
<div class=" todas_las_tiendas  $tienda_id">
<div class="  $tipo_orden">
<div class=" estado_todos $estado_orden ">
<nav class="navbar navbar-expand-md navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item grande">
                Orden ID: $order_id <span class="p-1 bg-dark text-white">$order_type_text</span> - <span class="p-1 bg-primary text-white">$source</span> -  <span class="p-1 bg-danger text-white">$tienda_name</span> <br>
                <span class="woofood-icon-location"></span> $lineaDireccion - $order_city <br>
                <span class="woofood-icon-phone"></span> $order_phone <br>

            </li>
            <li class="nav-item grande">
                &nbsp;&nbsp;&nbsp;&nbsp;Status: <b>$st</b>
            </li>
            <li>&nbsp;&nbsp;</li>
            <li class="nav-item grande contar" data-id="$order_id">
            <input type="hidden" id="Totalsegundos$order_id" value="$todosLosSegundos">
              <span class="">Tiempo Total</span>
              <span class=""  id="horas$order_id">$tiempoH</span>


            </li>
        </ul>
        <ul class="navbar-nav">


            <li class="nav-item">
               <button data-toggle="collapse" data-target="#det$order_id" class="btn btn-success">Detalles</button>
            </li>
            <li>&nbsp;</li>
             <li class="nav-item">
            <button class="btn btn-danger btn-block btnCancela"  data-id="$order_id" >Cancelar</button>
        </li>
        </ul>
    </div>
</nav>
ACA;

            if($order_type_text === "Delivery"){

                if($order->get_status() === "processing") { $fondo = "fondoProcesando";}
                if($order->get_status() === "dlv" || $order->get_status() === "pedido_entregado") { $fondo = "fondoEnviado";}
                if($order->get_status() === "eam") { $fondo = "fondoEsperandoAceptacion";}
                if($order->get_status() === "en_camino") { $fondo = "fondoEnCamino";}
                if($order->get_status() === "motorista_rechaza") { $fondo = "fondoRechaza";}




                echo <<<ACA

<nav class="navbar navbar-expand-lg $fondo ">

ACA;

                if($order->get_status() === "en_camino") {

                    $sql  = "SELECT b.user_nicename, c.aceptado_el  FROM $tabla_asignacion_motoristas a JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                                    JOIN $tabla_asignacion_tiendas c ON a.delivery_boy_id = c.delivery_boy_id  WHERE c.pedido_id = $order_id";
                    $ds = $wpdb->get_row($sql);

                    $hora2 = $ds->aceptado_el;
                    $inicio2 = strtotime($hora2);

                    $tiempoH2 = gmdate("H", ($ahora - $inicio2));
                    $tiempoM2 = gmdate("i", ($ahora - $inicio2));
                    $tiempoS2 = gmdate("s", ($ahora - $inicio2));

                    $todosLosSegundos2 = ($tiempoM2 * 60) + $tiempoS2;

                    echo <<<EOL

<ul class="navbar-nav mr-auto">
            <li class="nav-item grande">
                Motorista: $ds->user_nicename
            </li>
            <li class="nav-item grande">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button class="btn btn-success terminarBtn" data-id="$order_id">Orden entregada</button>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item grande contar2" data-id="$order_id">
            <input type="hidden" id="Totalsegundos2$order_id" value="$todosLosSegundos2">
               <br>
               <span class="blanca_letra">En ruta</span>
               <span class="blanca_letra"  id="horas2$order_id">$tiempoH2</span>


            </li>
        </ul>
EOL;
                }

                if($order->get_status() === "processing") {

                    echo <<<EOL

                     <select id="motormans-$order_id" class="form-control-sm mr-1">$selLibres</select>
                     <button class="btn btn-warning boton" data-id="$order_id"  >Asignar</button>&nbsp;

                     <form id="woofood_complete_order_form_$order_id" action="" method="POST" class="form-inline">
                    <input type="hidden" name="action" value="cambiarTienda"/>
                    <input type="hidden" name="order_id" value="$order_id"/>
                    &nbsp;&nbsp;&nbsp;&nbsp;$sel&nbsp;&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-success"  type="submit">Cambiar de Tienda</button>&nbsp;&nbsp;&nbsp;&nbsp;
                </form>


                <!--
                esto es para cuando haya motoristas
                <form id="woofood_complete_order_form_$order_id" action="" method="POST" class="form-inline">
                    <input type="hidden" name="action" value="completed"/>
                    <input type="hidden" name="order_id" value="$order_id"/>
                    <button class="btn btn-success"  type="submit">El pedido se há enviado</button>&nbsp;&nbsp;&nbsp;&nbsp;
                </form>
                el de abajo es el provisorio
                -->
                <form id="woofood_complete_order_form_$order_id" action="" method="POST" class="form-inline el_form">
                    <input type="hidden" name="action" value="dlv"/>
                    <input type="hidden" name="order_id" value="$order_id"/>
                    <button class="btn btn-success" id='btn_terminado' data-order='$order_id'  type="button">El pedido esta listo para enviarse</button>&nbsp;&nbsp;&nbsp;&nbsp;
                </form>
                            

EOL;
                }

                if($order->get_status() === "motorista_rechaza") {

                    $accionTipo = "dlv"; $accionBotonTexto = "Saliendo del restaurante";
                    echo <<<EOL

                     <select id="motormans-$order_id" >$selLibres</select>
                     <button class="btn btn-warning boton" data-id="$order_id"  >Asignar</button>&nbsp;

                     <form id="woofood_complete_order_form_$order_id" action="" method="POST" class="form-inline">
                    <input type="hidden" name="action" value="cambiarTienda"/>
                    <input type="hidden" name="order_id" value="$order_id"/>
                    &nbsp;&nbsp;&nbsp;&nbsp;$sel&nbsp;&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-success"  type="submit">Cambiar de Tienda</button>&nbsp;&nbsp;&nbsp;&nbsp;
                </form>

EOL;
                }

                if($order->get_status() === "eam") {
                    $accionTipo = "dlv"; $accionBotonTexto = "Saliendo del restaurante";
                    echo <<<EOL

                     <select id="motormans-$order_id" >$selLibres</select>
                     <button class="btn btn-warning boton" data-id="$order_id"  >Asignar </button>&nbsp;

EOL;
                }

                if($order->get_status() === "dlv") {
                    echo <<<ESE
                    <form id="woofood_complete_order_form_$order_id" action="" method="POST" class="form-inline">
                    <input type="hidden" name="action" value="completed"/>
                    <input type="hidden" name="order_id" value="$order_id"/>
                    <button class="btn btn-success"  type="submit">El pedido fué entregado</button>&nbsp;&nbsp;&nbsp;&nbsp;
                </form>
ESE;
                }

            }
            else {

                if($order->get_status() === "processing") { $fondo = "fondoProcesando";}
                if($order->get_status() === "dlv") { $fondo = "fondoListoParaRecoger";}
                if($order->get_status() === "rtp") { $fondo = "fondoRechaza";}

                echo <<<ACA

<nav class="navbar navbar-expand-lg $fondo">

ACA;

                if($order->get_status() === "processing") { $accionTipo = "dlv"; $accionBotonTexto = "Listo para Entregar"; }
                if($order->get_status() === "dlv" || $order->get_status() === "rtp") { $accionTipo = "completed"; $accionBotonTexto = "Finalizado / Entregado";}

                echo <<<ERE
                <form id="woofood_complete_order_form_$order_id" action="" method="POST" class="form-inline">
                    <input type="hidden" name="action" value="$accionTipo"/>
                    <input type="hidden" name="order_id" value="$order_id"/>
                    <button class="btn btn-success"  type="submit">$accionBotonTexto</button>&nbsp;&nbsp;&nbsp;&nbsp;
                </form>

ERE;


                //  echo do_shortcode('[wcpdf_download_invoice link_text="Imprimir" order_id="'.$order_id.'"]');
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
                echo "<span style='display: block;'><b>".$curr_numn.") </b>".$item['name']." ".__('<b>Cantidad:</b>','woofood-plugin').$item['qty']."</span>";
                wc_display_item_meta( $item );
                ++$curr_numn;
            }

            echo <<<EOL
</div>
</div>
</div>
<div class="row">
<div class="col-4">&nbsp;</div>
<div class="col-8"><button class="btn btn-danger btn-block btn_block_cliente" data-cliente="$usuario_id" data-pedido="$order_id">Bloquear este cliente</button></div>
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
</div>
<hr><br><br>
</div>
</div>
</div>


EOL;




        }

    }//end function

    $prefijo = $wpdb->prefix;

    $tablaPosts = $prefijo."posts";
    $tablaPostMeta = $prefijo."postmeta";

    $sql = "Select b.ID FROM $tablaPostMeta a JOIN $tablaPosts b ON a.post_id = b.ID WHERE b.post_status LIKE 'wc-%' AND b.post_date >= DATE_SUB(NOW(),INTERVAL 16 HOUR); ";
    $ordenes = $wpdb->get_results($sql);
    foreach($ordenes as $v){
        $orden[] = $v->ID;
    }
    $ordenes = array_unique($orden);


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

.oculta{
    display: none;
}

.mostrar.oculta{
    display:block!important;
}
</style>

                <table cellspacing="0" cellpadding="0" style="width: 100%;border: #4a4848;" class="fondoFiltros">
                <tr>
                <td style="width: 33%"></td>
                <td style="width: 33%">Filtrar estado de pedido</td>
                <td style="width: 33%">Filtrar por tienda:</td>
                </tr>
                <tr>
                <td>
                <button type="button" class="btn btn-primary btn-block"  onClick="window.location.reload();">Traer nuevos pedidos</button>

                <!--
                <select id="cadaCuantoRefresco" class="form-control">
                <option value="1" selected>1 Min.</option>
                <option value="2" >2 Min.</option>
                <option value="3" >3 Min.</option>
                <option value="4" >4 Min.</option>
                <option value="5" >5 Min.</option>
                </select>
                -->
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
<select id="tiendasSel" class="form-control">
$selTienda
</select>
</td>
</tr>
<tr><td>
  <!--
                <input type="hidden" id="pausarRfrsh" value="0">

                <button class="btn btn-primary btn-block" id="pausarRfrshBtn">Pausar Refresco Automático</button>
                -->
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
ETE;



   //   $class_descarga = $current_user_id;

    foreach($ordenes as $c => $v){
        echo get_order_detailsGustavo2($v);
    }
    $logOutUrl = wp_logout_url('/index.php');
    echo <<<FIN
     <div class="row">
        <div class="col-lg-6">
        <a class="btn btn-primary btn-block" href="../en_camino">Ir al seguimiento de Envios</a>
        </div>
        <div class="col-lg-6">
        <a class="btn btn-danger btn-block" id="btnSalir" href="$logOutUrl">Cerrar Sesión</a>
        </div>





        <div class="col-lg-6">
        <a class="$class_descarga btn btn-secondary btn-block mt-2 oculta" id="btnSalir" href="https://delpuente.com.gt/wp-load.php?security_token=66cf43baf4c481d9&export_id=2&action=get_data">Descargar Reporte</a>
        </div




        </div>

      <script>

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>

FIN;
}
