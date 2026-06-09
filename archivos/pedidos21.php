<?php

function pedidos21() {
    global $wpdb;
    $prefijo = $wpdb->prefix;
    $args = array(
        'post_type'        => 'extra_store',
        'meta_query' => array(
            array(
                'key' => 'extra_store_user',
                'value' => wp_get_current_user()->ID
            )
        )
    );
    $titulo = get_posts($args);
    $tablaPosts = $prefijo."posts";
    $tablaPostMeta = $prefijo."postmeta";

    $sql = "SELECT * FROM $tablaPosts WHERE post_type LIKE 'extra_store%' ";
    $stores = $wpdb->get_results($sql);
    foreach($stores as $v){
        $tienda[$v->ID] = $v->post_title;
    }

    $sql = "Select b.ID FROM $tablaPostMeta a JOIN $tablaPosts b ON a.post_id = b.ID WHERE b.post_status = 'wc-processing' AND a.meta_value =  'delivery'";
    $ordenes = $wpdb->get_results($sql);
    foreach($ordenes as $v){
        $orden[] = $v->ID;
    }


    foreach($orden as $v){
        $sql = "SELECT meta_value FROM $tablaPostMeta WHERE post_id = $v AND  meta_key = 'extra_store_name'";
        $estaTienda = $wpdb->get_row($sql);
    }

    $query_args = array('limit'=> -1,
        // 'post_status'=> $order_statuses,
        'fields'=> 'ids',
        'return' => 'ids' );

    if(!empty($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'){

/*
Array
(
    [action] => cambiarTienda
    [order_id] => 6054
    [tiendasDisponibles] => 2238
    [fabfw_add_address] =>
    [fabfw_edit_address] =>
)
*/



        $order_id = (int)$_POST['order_id'];
        $order =  new WC_Order( $order_id );



        if($_POST['action'] === 'cambiarTienda'){
            if($_POST['tiendasDisponibles'] === "0"){
                echo "<h3>Debe seleccionar una tienda para hacer el cambio.</h3>";
            } else {

                $tabla =  $wpdb->prefix.'postmeta';
                $nuevaTienda = $_POST['tiendasDisponibles'];


                $tv = $wpdb->get_row($wpdb->prepare("SELECT meta_value FROM $tabla WHERE post_id = $order_id AND meta_key = 'extra_store_name'"));
                $tienda_vieja = $tv->meta_value;

                update_post_meta($order_id, 'tienda_previa', $tienda_vieja);

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


    // $cont =  new WC_Order( $order_id );


    echo "<div class='row'><div class='col-12 text-center font-weight-bold'><h1>".$titulo[0]->post_title."</h1></div></div>";



    function get_order_detailsGustavo21($order_id){
        
        global $wpdb;

        $prefijo = $wpdb->prefix;
        $tablaPosts = $prefijo."posts";
        $tablaPostMeta = $prefijo."postmeta";




        $sql = "SELECT * FROM $tablaPosts WHERE post_type LIKE 'extra_store%' ";
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



        // Get NIT
        $nit = get_post_meta( $order_id, 'billing_nit', true );

        //Get Source: Android - iOS - Web
        $source = get_post_meta( $order_id, 'device', true );

// get_delivery_boys( $request )

        $pst = $order->get_status();
        if($pst === "rtp"){$st = 'El Cliente esta en el Restaurante.';}
        elseif ($pst === "dlv"){$st = 'Listo para entregar.';}
        elseif ($pst === "processing"){$st = 'Preparando.';}
        elseif ($pst === "eam"){$st = 'Esperando.';}
        elseif ($pst === "en_camino"){$st = 'En camino.';}
        elseif ($pst === "motorista_rechaza"){$st = 'Motorista rechaza el envio.';}
        else {$st = $pst;}

        $estado_orden = $order->get_status();
        $tipo_orden = $order_type_text;

        if($order->get_status() ==="completed" || $order->get_status() ==="trash" || $order->get_status() ==="cancelled" || $order->get_status() ==="pending" || $order->get_status() ==="failed"){} else {

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
<div class="  $tipo_orden">
<div class=" estado_todos $estado_orden ">
<nav class="navbar navbar-expand-md navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item grande">
                Orden ID: $order_id <span class="p-1 bg-dark text-white">$order_type_text</span> - <span class="p-1 bg-primary text-white">$source</span> <br>
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
        </ul>
    </div>
</nav>
ACA;





            if($order->get_status() === "processing") { $fondo = "fondoProcesando";}
            elseif($order->get_status() === "dlv" || $order->get_status() === "pedido_entregado" || $order->get_status() === "rtp") { $fondo = "fondoEnviado";}
            elseif($order->get_status() === "en_camino") { $fondo = "fondoEnCamino";}
            elseif($order->get_status() === "eam") { $fondo = "fondoEsperandoAceptacion";}
            elseif($order->get_status() === "motorista_rechaza") { $fondo = "fondoRechaza";}
            else { $fondo = "fondoProcesando";}


            echo <<<ACA

<nav class="navbar navbar-expand-lg $fondo ">
<form id="woofood_complete_order_form_$order_id" action="" method="POST" class="form-inline ml-2">
    <input type="hidden" name="action" value="cambiarTienda"/>
    <input type="hidden" name="order_id" value="$order_id"/>
    &nbsp;&nbsp;&nbsp;&nbsp;$sel&nbsp;&nbsp;&nbsp;&nbsp;
    <button class="btn btn-success" type="submit">Cambiar de Tienda</button>&nbsp;&nbsp;&nbsp;&nbsp;
</form>
ACA;

            if($order->get_status() === "processing" || $order->get_status() === "dlv") {
                echo "<button class='btn btn-danger btnCancela21' data-id='$order_id'>Cancelar pedido</button>";
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
                echo "<span style='display: block;'><b>".$curr_numn.") </b>".$item['name']." ".__('<b>Cantidad:</b>','woofood-plugin').$item['qty']."</span>";                wc_display_item_meta( $item );





                // echo "<hr/>";
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
</div>
</div>
</div>
<hr><br><br>
</div>
</div>
EOL;
        }

    }//end function


    if( current_user_can('editor') || current_user_can('multistore_user') ) {
        $currentUserRoles = wp_get_current_user()->roles;
        if (in_array('multistore_user', $currentUserRoles)) {
            $args = array(
                'post_type'        => 'extra_store',
                'meta_query' => array(
                    array(
                        'key' => 'extra_store_user',
                        'value' => wp_get_current_user()->ID,
                        'compare' => '==',
                    )
                )
            );
            $stores = get_posts($args);
            if(!empty($stores))
            {
                $store_name = $stores[0]->ID;
            }
            else
            {
                $store_name ="storethatnotexists";
            }
            $query_args["extra_store_name"] = $store_name;
        }

/*
        $sql = "Select b.ID FROM $tablaPostMeta a JOIN $tablaPosts b ON a.post_id = b.ID WHERE b.post_status LIKE 'wc-%' AND b.post_date >= DATE_SUB(NOW(),INTERVAL 16 HOUR); ";
        $ordenes = $wpdb->get_results($sql);
*/


    //    $order_list  = wc_get_orders($query_args);

// echo date('Y-m-d H:i:s');
$date = new DateTime(date('Y-m-d H:i:s')); // pass $time_date_data to here
$date->sub(new DateInterval('PT6H'));
$esta_hora = $date->format('Y-m-d H:i:s');

$date2 = new DateTime(date('Y-m-d H:i:s')); // pass $time_date_data to here
$date2->sub(new DateInterval('PT2500H'));
$inicio_hora = $date2->format('Y-m-d H:i:s');
$rango = "$inicio_hora...$esta_hora";

        $order_list = wc_get_orders( array(
       'extra_store_name' => $store_name,
       'date_created' => $rango,
       'limit' => -1,
) );

$ordenesTotales = 0;
$ordenesDlv = 0;
$ordenesPckp = 0;

        foreach($order_list as $uu) {

            $order = new WC_Order($uu);
            $uu = $order->get_id();

            $order_typeT = get_post_meta($uu, 'woofood_order_type', true);

                if ($order_typeT === "delivery") {
                    if ($order->get_status() ==="completed" || $order->get_status() ==="trash" || $order->get_status() ==="cancelled" || $order->get_status() ==="pending" || $order->get_status() ==="failed" || $order->get_status() ==="wc-failed") {

                    } else {
                  //    echo $order_id."<br>";
                        $ordenesDlv++;
                        $ordenesTotales++;
                    }
                } else {

if ($order->get_status() === "completed" || $order->get_status() === "trash" || $order->get_status() === "cancelled"|| $order->get_status() ===   "pending" || $order->get_status() === "failed") {
					
                    } else {

                        $ordenesPckp++;
                        $ordenesTotales++;
                    }

                }



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
</style>

                <table cellspacing="0" cellpadding="0" style="width: 100%;border: #4a4848;" class="fondoFiltros">
                <tr><td style="width: 33%">
<!--
                Refresco Automático
-->
                </td><td style="width: 33%">Filtrar estado de pedido</td><td>Total de Pedidos <strong>$ordenesTotales</strong></td></tr>
                <tr><td>
                <button type="button" class="btn btn-primary btn-block"  onClick="window.location.reload();">Descargar Pedidos</button>
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
<option value="eam">Esperando</option>
<option value="motorista_rechaza">Rechazado</option>
<option value="dlv">Listo Para Recoger</option>
<option value="rtp">Listo para Entregar</option>
</select>
</td>
<td>Delivery <strong>$ordenesDlv</strong></td>
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
<td>Pickup <strong>$ordenesPckp</strong></td></tr>
</table>
ETE;


        foreach($order_list as $uu){
          $order = new WC_Order($uu);
          $uu = $order->get_id();

            echo get_order_detailsGustavo21($uu);

        }

        $logOutUrl = wp_logout_url('/index.php');
        echo <<<FIN

        <div class="row">
        <div class="col-lg-12">
        <a class="btn btn-danger btn-block" id="btnSalir" href="$logOutUrl">Cerrar Sesión</a>
        </div>
        </div>

        <script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>

FIN;

        $dlp_ajax_url = esc_url( admin_url('admin-ajax.php') );
        echo <<<JS
<script>
jQuery(function($){
    var dlpAjaxUrl = '$dlp_ajax_url';

    $(document).on('click', '.btnCancela21', function () {
        var pedidoId = $(this).data('id');

        Swal.fire({
            title: 'Cancelar pedido',
            text: 'Esta accion no se puede revertir.',
            input: 'textarea',
            inputLabel: 'Motivo de cancelacion',
            inputPlaceholder: 'Escribe el motivo...',
            inputAttributes: {
                'aria-label': 'Motivo de cancelacion'
            },
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Regresar',
            didOpen: function () {
                Swal.disableConfirmButton();
                var textarea = Swal.getInput();
                if (textarea) {
                    textarea.addEventListener('input', function () {
                        if ((textarea.value || '').trim().length > 0) {
                            Swal.enableConfirmButton();
                        } else {
                            Swal.disableConfirmButton();
                        }
                    });
                }
            },
            preConfirm: function (motivo) {
                if (!motivo || !motivo.trim()) {
                    Swal.showValidationMessage('Debes escribir un motivo para cancelar');
                    return false;
                }
                return motivo.trim();
            }
        }).then(function(result){
            if (!result.isConfirmed) {
                return;
            }

            var data = {
                action: 'cancelar_pedido',
                cualPedido: pedidoId,
                motivo: result.value
            };

            $.ajax({
                url: dlpAjaxUrl,
                type: 'POST',
                dataType: 'json',
                data: data
            }).done(function(resp){
                if (resp && resp.success) {
                    location.reload(true);
                    return;
                }

                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'No se pudo cancelar el pedido';
                Swal.fire('Error', msg, 'error');
            }).fail(function(xhr){
                Swal.fire('Error', 'No se pudo cancelar el pedido ('+xhr.status+')', 'error');
            });
        });
    });
});
</script>
JS;

    }

}
