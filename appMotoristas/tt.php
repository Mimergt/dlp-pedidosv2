<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
/*
define( 'SHORTINIT', true );

require( '../../../wp-load.php' );
*/

global $wpdb;
extract($_POST);


$sql = "SELECT lat, lng, fecha, id, motorista FROM tFdF8_a_coordenadas_motoristas WHERE motorista = $motorista ORDER BY id DESC LIMIT 1";
$ds = $wpdb->get_row($sql);

//$ds = array("lat" => 0, "lng" => 0);
echo json_encode($ds);
