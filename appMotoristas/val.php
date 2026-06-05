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


$json = file_get_contents('php://input');
$data = json_decode($json);
// $user = 'mimer_v57w74h7';
// $password = 'b97#6gElDM';


$user = $data->usuario;
$password = $data->password;


$usr = wp_authenticate ($user, $password);

if(!$usr->ID){
  $res = array("usuario_validado" => 0);

} else {
  $res = array("usuario_validado" => $usr->ID);

}


//$a = array("valor" => 1);
echo json_encode($res);
