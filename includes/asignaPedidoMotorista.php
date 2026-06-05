<?php
extract($_POST);

$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";

$order = wc_get_order( $cualPedido );

$order->update_status('eam');

$fecha = date('Y-m-d H:i:s');

$tabla_asignacion_tiendas = $prefijo."a_asignacion_pedidos";

$sql = "INSERT INTO $tabla_asignacion_tiendas SET delivery_boy_id = $motorman, pedido_id = $cualPedido, status = 2, asignado_el = '$fecha'";

$wpdb->query( $wpdb->prepare($sql) );