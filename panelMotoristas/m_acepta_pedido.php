<?php

/*
 *   [cualPedido] => 2993
    [motorman] => 102
 */


extract($_POST);

$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo . "a_asignacion_motoristas";

$order = wc_get_order($cualPedido);

$order->update_status('en_camino');

$fecha = date('Y-m-d H:i:s');

$tabla_asignacion_tiendas = $prefijo . "a_asignacion_pedidos";

$sql = "UPDATE $tabla_asignacion_tiendas SET status = 1, aceptado_el = '$fecha' WHERE delivery_boy_id = $motorman AND  pedido_id = $cualPedido ";

$wpdb->query($wpdb->prepare($sql));