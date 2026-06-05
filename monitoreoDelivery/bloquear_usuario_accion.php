<?php

extract($_POST);

if ( ! update_user_meta( $cliente_id, 'is_active', 'n', true ) ) {
   update_user_meta ( $cliente_id, 'is_active', 'n' );
}
$order_id = (int)$_POST['pedido_id'];
$order =  new WC_Order( $order_id );


$aQue = 'cancelled';
$order->update_status( $aQue );

/* echo 1 */

;
