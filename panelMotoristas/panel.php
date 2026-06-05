<?php

function panelMotorista()
{


    global $wpdb;

    $prefijo = $wpdb->prefix;
    $current_user = wp_get_current_user();


    if ($current_user->roles[0] !== 'deliveryboy_user') {
        wp_die('Usted no está autorizado a estar en esta pagina');
    }


    $motorista_id = $current_user->ID;

    $tabla_asignacion_tiendas = $prefijo . "a_asignacion_pedidos";
    $tabla_asignacion_motoristas = $prefijo . "a_asignacion_motoristas";

    $sql = "SELECT pedido_id, status FROM $tabla_asignacion_tiendas WHERE delivery_boy_id = $motorista_id AND status != 3 ";

    $pe = $wpdb->get_results($sql);


    $sql = "SELECT estado FROM $tabla_asignacion_motoristas WHERE delivery_boy_id = $motorista_id";
    $e = $wpdb->get_row($sql);
    /*
    echo "<pre>";
    print_r($pe);
    print_r($e);
    echo $motorista_id;
    exit;
    */

    $logOutUrl = wp_logout_url('/index.php');

    echo <<<DIV
    
<input type="hidden" id="motorman" value="$motorista_id">
DIV;

    if (empty($pe) ) {
        if ($e->estado === "1") {
           
        echo '
        <div class="row">
        <div class="col-lg-12 text-center">No tiene pedidos pendientes de llevar</div>
        </div>
        <div class="row">
        <div class="col-lg-12"><button class="btn btn-primary btn-block" onclick="javascript:location.reload();">Buscar Pedidos</button> </div>
        </div>';

        echo '
        <div class="row">
        <div class="col-lg-12">
        <button id="btnDisponible" class="btn btn-success btn-block">Marcarme DISPONIBLE</button>
        </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">
        <div class="col-lg-12">
        <a class="btn btn-danger btn-block" id="btnSalir" href="'.$logOutUrl.'">Marcarme NO DISPONIBLE / Cerrar Sesión</a>
        </div>
        </div>';
        
    }   else {
        
        echo '
        <div class="row">
        <div class="col-lg-12">
        <button id="btnDisponible" class="btn btn-success btn-block">Marcarme DISPONIBLE</button>
        </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">&nbsp;</div>
        <div class="row">
        <div class="col-lg-12">
        <a class="btn btn-danger btn-block" id="btnSalir" href="'.$logOutUrl.'">Marcarme NO DISPONIBLE / Cerrar Sesión</a>
        </div>
        </div>';
    }
}
    else {
        
        echo '
            <style>
            .fondoAceptado {
            background-color: #4a5f21 !important;
            }

            .fondoEsperandoAceptacion {
            background-color: #ea8225 !important;
            }
            </style>
            <div class="accordion" id="accordionExample">
            <div class="card">';

        foreach ($pe as $pedidoEspera) {

            

            $order_id = $pedidoEspera->pedido_id;
            $order = new WC_Order($pedidoEspera->pedido_id);
            $order_data = $order->get_data(); // The Order data

           

            // 3) Get the order items
            $items = $order->get_items();
            $order_phone = $order->get_billing_phone();
            $order_email = $order->get_billing_email();
            $order_date = $order_data['date_created']->date(get_option('date_format') . " " . get_option('time_format'));

            $order_type = get_post_meta($order_id, 'woofood_order_type', true);
            if (!$order_type) {
                $order_type = woofood_get_default_order_type();
            }
            $woofood_time_to_deliver = get_post_meta($order_id, 'woofood_time_to_deliver', true);
            $default_date_format = get_option("date_format");
            $default_time_format = get_option("time_format");
            $order_status = $order->get_status();
            $order_type_text = woofood_get_order_type_by_key($order_type);

            $shipping_address = $order->get_formatted_shipping_address();
            $billing_address = $order->get_formatted_billing_address();

            $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
            $billing_address_html = !empty($shipping_address) ? $shipping_address : $billing_address;
            $notas = $order->get_customer_note();
            $total = get_woocommerce_currency_symbol() . $order->get_total();
            $forma_pago = $order->payment_method_title;
            // Get NIT
            $nit = get_post_meta($order_id, 'billing_nit', true);


            
            if ($order_status === "completed") {
            } else {


                if ($pedidoEspera->status === "2") {
                    $fondo = "fondoEsperandoAceptacion";
                } else {
                    $fondo = "fondoAceptado";
                }
                echo <<<ACA
                    <div class="card-header $fondo" id="headingOne">
                    <div class="row">
                    <div class="col-12">
                                    
                    <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#collapse$order_id" aria-expanded="true" aria-controls="collapseOne">
                        Orden: $order_id - Dirección: $billing_address_html
                        </button>
                </div>
                </div>
                </div>
                    <div id="collapse$order_id" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">

ACA;
                if ($order_status === "eam") {
                    echo <<<ACA
                <div class="row">
                <div class="col-lg-6">
                <button class="btn btn-success btn-block"  id="aceptar" data-id="$order_id">Aceptar</button>
                </div>
                <div class="col-lg-6">
                <button class="btn btn-danger btn-block" id="rechazar" data-id="$order_id">Rechazar</button>
                </div>
                </div>
ACA;
                }
                if ($order_status === "en_camino") {


                    echo <<<ACA
                <div class="row">
                <div class="col-lg-12">
                <button class="btn btn-success btn-block"  id="entregado" data-id="$order_id">Acabo de entregar este pedido</button>
                </div>
                </div>
ACA;
                }


                echo <<<EOL
                <!-- los detalles del pedido -->
                <div class="row">&nbsp;</div>
                <div class="row">
                <div class="col-4 detalles_cliente">
                <h2>Detalles del Cliente</h2>
                <div class="col mb-3 mt-10 bg-light py-2"><h4>Nombre:</h4><h5> $name</h5></div>
                <div class="col mb-3 mt-10 bg-light py-2"><h4>NIT:</h4><h5> $nit</h5></div>
                <div class="col mb-3 mt-10 bg-light py-2"><h4>Telefono:</h4><h5> $order_phone</h5></div>
                <div class="col mb-3 mt-10 py-2"><h4>Direccion:</h4><h5> $billing_address_html</h5></div>
                <div class="col mb-3 mt-10 py-2"><h4>Notas:</h4><h5> $notas</h5></div>
                </div>
                <div class="col-8">
                <h2>Detalles del Pedido</h2>
                <div class="detalles_pedido col mb-3 mt-10 bg-light py-2">
EOL;
                $curr_numn = 1;
                foreach ($items as $item) {
                    echo "<b>" . $curr_numn . ") </b>" . $item['name'] . " " . __('<b>Cantidad:</b>', 'woofood-plugin') . $item['qty'];
                    wc_display_item_meta($item);
                    ++$curr_numn;
                }
                echo <<<ORD
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



                    <!-- los detalles del pedido -->
                    </div>
                    </div>
                    </div>
                    <br>


ORD;
            }
        }

        echo <<<FIN
</div>

FIN;
    }




    echo <<<FIN

 <script type="text/javascript">
            window.setTimeout(function(){ document.location.reload(true); }, 60000);
        </script>
FIN;
}
