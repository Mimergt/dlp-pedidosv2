<?php
extract($_POST);

$tiendas = implode(",", $tienda);
$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";

$sql = "INSERT INTO $tabla_asignacion_motoristas SET delivery_boy_id = $cual, tienda_id = '$tiendas' ON DUPLICATE KEY UPDATE tienda_id = '$tiendas'";

$wpdb->query($sql);
