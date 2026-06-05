<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
  echo "nono";
    die();
}

date_default_timezone_set('America/Guatemala');

$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;

$json = file_get_contents('php://input');
$data = json_decode($json);

$user = $data->usuario;
$lat = $data->lat;
$lng = $data->lng;


$ahora = date('Y-m-d  H:i:s');
$ins = $wpdb->insert('tFdF8_a_coordenadas_motoristas', array(
    'motorista' => $user,
    'lat' => "$lat",
    'lng' => "$lng",
    'fecha' => "$ahora"
));

$a = array("respuesta" => $ins);
echo json_encode($a);
