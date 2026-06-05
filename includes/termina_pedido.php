<?php
extract($_POST);

$prefijo = $wpdb->prefix;

$order = wc_get_order($cualPedido);

$order->update_status('completed');

$fecha = date('Y-m-d H:i:s');

$tabla_asignacion_tiendas = $prefijo . "a_asignacion_pedidos";

$sql = "UPDATE $tabla_asignacion_tiendas SET status = 3, fin_asignacion = '$fecha' WHERE pedido_id = $cualPedido";

$wpdb->query($wpdb->prepare($sql));