<?php

extract($_POST);

$order_id = (int)$cual;


$order = wc_get_order($order_id);

$order->update_status('dlv');


$ahora = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - (3600 * 6));

update_post_meta($order_id, '_hora_despacho', $ahora);

