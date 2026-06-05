<?php
extract($_POST);
global $wpdb;
$dir = 'https://delpuente.com.gt/wp-content/plugins/';


include($dir.'woocommerce/woocommerce.php');


echo "asdasdad<pre>";
var_dump($GLOBALS['woocommerce']);


$orden = (int)$_POST['order_id'];
// $order =  new WC_Order( $orden );


exit;
// $order->update_status   order_id
// $bool = WC_Order::update_status( $new_status, $note, $manual );




$order = wc_get_order( $orden );
echo "<pre>";
print_r($GLOBALS['wc_container']);