<?php
extract($_POST);

$prefijo = $wpdb->prefix;

$order = wc_get_order($cualPedido);

$motivo = isset($motivo) ? sanitize_textarea_field($motivo) : '';

$order->update_status('cancelled');

if (!empty($motivo)) {
	$order->add_order_note('Motivo de cancelacion: '.$motivo);
	update_post_meta($cualPedido, '_motivo_cancelacion_tienda', $motivo);
}

$fecha = date('Y-m-d H:i:s');

$tabla_asignacion_tiendas = $prefijo . "a_asignacion_pedidos";

$sql = "UPDATE $tabla_asignacion_tiendas SET status = 3, fin_asignacion = '$fecha' WHERE pedido_id = $cualPedido";

$wpdb->query($wpdb->prepare($sql));