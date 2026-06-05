<?php

function footer_seguimiento($variables){
  global $wpdb;

  extract($variables);

  $order_id = (int)$pedido_id;
  $order =  new WC_Order( $order_id );

  $tienda_id = get_post_meta($order_id, 'extra_store_name', true);

  $tienda_nombre = get_the_title( $tienda_id );

    $telefono_tienda = get_post_meta( $tienda_id, 'extra_store_phone', true );


$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";
$tabla_asignacion_tiendas = $prefijo."a_asignacion_pedidos";
$tabla_usuarios = $prefijo."users";

$sql  = "SELECT a.delivery_boy_id, b.*  FROM $tabla_asignacion_motoristas a JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                JOIN $tabla_asignacion_tiendas c ON a.delivery_boy_id = c.delivery_boy_id  WHERE c.pedido_id = $order_id ORDER BY c.id DESC LIMIT 1";
$ds = $wpdb->get_row($sql);
$motorista_id = $ds->delivery_boy_id;
$motorista_nombre = $ds->user_nicename;

    echo "<div class='row'>&nbsp;</div>
         <div class='row'>
          <div class='col-12 text-left'>
                      <a class='btn btn-primary' href='tel:+$telefono_tienda'>$tienda_nombre: $telefono_tienda</a>
        </div>
         </div>";



}
