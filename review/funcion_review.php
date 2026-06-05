<?php
extract($_POST);

if($seguir_recibiendo === "0"){    
        $pedir_review = 0;
        update_user_meta( (int)$usuario_id, '_pedir_reviews', $pedir_review);
          
}

if($seguir_recibiendo === "1"){
    
    add_post_meta( (int)$pedido_id, '_puntaje_motorista', $calificacion_motorista );
    add_post_meta( (int)$pedido_id, '_puntaje_producto', $calificacion_producto );
    add_post_meta( (int)$pedido_id, '_puntaje_plataforma', $calificacion_plataforma );
    add_post_meta( (int)$pedido_id, '_puntaje_comentario', $comentario );
}

echo 1;