<?php
extract($_POST);

$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo . "a_asignacion_motoristas";

$order = wc_get_order($cualPedido);

$order->update_status('motorista_rechaza');

$tabla_asignacion_tiendas = $prefijo . "a_asignacion_pedidos";

$sql = "DELETE FROM $tabla_asignacion_tiendas WHERE delivery_boy_id = $motorman AND  pedido_id = $cualPedido ";

$wpdb->query($wpdb->prepare($sql));