<?php
function ver_tracker($variables){

  global $wpdb;
  extract($variables);



  $order_id = (int)$pedido_id;
  $order =  new WC_Order( $order_id );
  $order_type = get_post_meta($order_id, 'woofood_order_type', true);
  $order_type_text = woofood_get_order_type_by_key($order_type);
  $order_status = $order->get_status();

  //$ruta = 'https://delpuente.com.gt/animaciones/';
  $ruta = plugin_dir_url( __FILE__ ) . 'animaciones/';

if($order_type_text === "Pickup"){
  if( $order_status === "processing"){return '<img src="'.$ruta.'PICKUP/Pickup-procesando-1.gif"><h4>Estamos preparando tu pedido.</h4>';}
  if( $order_status === "dlv" ||$order_status === "rtp"){echo '<img src="'.$ruta.'DELIVERY/Delivery-delivery-2.gif"><h4>Puedes pasar a recoger tu pedido.</h4>';complete_action_button_my_accout_order_view1( $order_id );}
  if( $order_status === "completed"){return '<img src="'.$ruta.'PICKUP/Pickup-entregado-2.gif"><h4>Tu pedido se ha entregado.</h4>';}
}

if($order_type_text === "Delivery"){
  if( $order_status === "processing"){return '<img src="'.$ruta.'DELIVERY/Delivery-procesando-1.gif" ><h4>Estamos preparando tu pedido, será entregado aproximadamente en 45 minutos.</h4>';}
  if( $order_status === "motorista_acepta"){return '<img src="'.$ruta.'DELIVERY/Delivery-delivery-2.gif"><h4>Tu pedido se ha enviado.</h4>';}
  if( $order_status === "completed"){return '<img src="'.$ruta.'DELIVERY/Delivery-entregado-3.gif"><h4>Tu pedido se ha entregado.</h4>';}
  if( $order_status === "en_camino"){return '<img src="'.$ruta.'DELIVERY/Delivery-delivery-2.gif"><h4>Tu pedido está en camino.</h4>';}
}

}

/*
u pedido se está procesando.
Tu pedido está en camino.
Tu pedido se ha entregado.

Pickup:
Tu pedido se está procesando.
Tu pedido está listo para recoger.
Tu pedido se ha entregado.
*/