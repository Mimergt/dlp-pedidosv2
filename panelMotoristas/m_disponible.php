<?php

/*
    [motorman] => 102
 */


extract($_POST);

$prefijo = $wpdb->prefix;
$tabla_asignacion_motoristas = $prefijo . "a_asignacion_motoristas";

$sql = "UPDATE $tabla_asignacion_motoristas SET estado = 1 WHERE delivery_boy_id = $motorman";

$wpdb->query($wpdb->prepare($sql));
