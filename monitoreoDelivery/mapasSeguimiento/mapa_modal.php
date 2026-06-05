<?php


function mapa_seguimiento_2($p)
{
    global $wpdb;

    $pedido_id = $p['pedido_id'];
   // $pedido_id = 37207;
   
    $order_id = (int)$pedido_id;
    $order =  new WC_Order( $order_id );
  
    $tienda_id = get_post_meta($order_id, 'extra_store_name', true);
  
  //  $dire = geocode($shipping_address);
    $latitud_tienda = get_post_meta( $tienda_id, 'extra_store_lat', true );
    $longitud_tienda = get_post_meta( $tienda_id, 'extra_store_lng', true );
  
    $shipping_address = $order->shipping_address_1.", Guatemala";
    if(!isset($shipping_address) || $shipping_address === ''){
    $shipping_address = $order->billing_address_1.", Guatemala";
  }
  
  
  $dire = geocode($shipping_address);
  
  $d = json_decode($dire);


$latitud_destino = $d->results[0]->geometry->location->lat;
$longitud_destino = $d->results[0]->geometry->location->lng;

$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";
$tabla_asignacion_tiendas = $prefijo."a_asignacion_pedidos";
$tabla_usuarios = $prefijo."users";

$sql  = "SELECT a.delivery_boy_id, b.*  FROM $tabla_asignacion_motoristas a JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                JOIN $tabla_asignacion_tiendas c ON a.delivery_boy_id = c.delivery_boy_id  WHERE c.pedido_id = $order_id ORDER BY c.id DESC LIMIT 1";
$ds = $wpdb->get_row($sql);
$motorista_id = $ds->delivery_boy_id;
$motorista_nombre = $ds->user_nicename;

$order_type = get_post_meta($order_id, 'woofood_order_type', true);
$order_type_text = woofood_get_order_type_by_key($order_type);


$order_type_text === "Delivery";

?>

    <style>
        .modal.fade {
            z-index: 10000000 !important;
        }

        .modal-dialog {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .modal-content {
            height: auto;
            min-height: 100%;
            border-radius: 0;
            background-color: #11100f !important;
            color: white;
        }
        .mapa_modal_div{
            width: 350px;
            height: 350px;
        }
    </style>
    <br>

    <button type="button" class="btn order-map seguimiento_boton abro_modal_mapa" id="" 
            data-orden="<?php echo $pedido_id;?>"       
            data-lattienda = "<?php echo $latitud_tienda;?>"       
            data-longitudtienda="<?php echo $longitud_tienda;?>"       
            data-latituddestino="<?php echo $latitud_destino;?>"       
            data-longituddestino="<?php echo $longitud_destino;?>"       
            data-motorista="<?php echo $motorista_id;?>"       
    >
        Rastrea tu pedido
    </button>

    <!-- Modal -->
    <div class="modal fade order-map-modal" id="modal_mapa_<?php echo $pedido_id;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
            <div id="mapa_modal_div_<?php echo $pedido_id;?>" class="mapa_modal_div"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

<?php

}
