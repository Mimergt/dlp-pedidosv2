<?php

$tienda = $_POST['tienda'];



$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo."a_asignacion_motoristas";
$tabla_asignacion_tiendas = $prefijo."a_asignacion_pedidos";
$tabla_usuarios = $prefijo."users";

if($tienda === "todos"){
  $filtro = 1;
} else {
  $filtro = "FIND_IN_SET($tienda, a.tienda_id)";
}
$sql  = "SELECT DISTINCT(a.delivery_boy_id) as delivery_boy_id, b.user_nicename, a.estado  FROM $tabla_asignacion_motoristas a
                                                     JOIN $tabla_usuarios b ON a.delivery_boy_id = b.ID
                                                     WHERE  $filtro";
$d = $wpdb->get_results($sql);

echo json_encode($d);
/*
(
          [delivery_boy_id] => 3460
          [user_nicename] => dliver3
          [estado] => 1
      )

*/
