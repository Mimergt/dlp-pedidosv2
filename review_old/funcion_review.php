<?php

extract($_POST);


if($seguir_recibiendo === "0"){    
          $pedir_review = 0;
          var_dump(update_user_meta( (int)$usuario_id, '_pedir_reviews', $pedir_review));
          
}

if($seguir_recibiendo === "1"){
    update_post_meta( (int)$pedido_id, '_puntaje_pedido', $puntaje );
}

echo 1;