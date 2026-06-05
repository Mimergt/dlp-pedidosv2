<?php

function shortcode_reviews() {
    global $wpdb;
    
    if (is_user_logged_in()) {
       
        
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $datos = get_userdata((int) $user_id);

        
        // me fijo si no pidio no mostrar el cartel
        $meta = '_pedir_reviews';
        $single = true;
        $pido_reviews = get_user_meta($user_id, $meta, $single);

        
        
        if ($pido_reviews === "") {
            $pedir_review = 1;
            add_user_meta($user_id, '_pedir_reviews', $pedir_review);
        }

          
        $mostrar = 1;
        if ($pido_reviews === "0") {
            $mostrar = 0;
        }


        $orders = wc_get_orders(array(
            'customer_id' => get_current_user_id(),
            'return' => 'ids',
        ));

     //   echo "<pre>";
     //   print_r($orders);
     //   echo "</pre>";
        
        if (!isset($orders) || empty($orders)) {
            $mostrar = 0;
        }

        $ultima_orden = $orders[0];
        $order = wc_get_order($ultima_orden);
        if($order){
        $order_status = $order->get_status();
        $order_meta = $order->get_meta('_puntaje_motorista');
       // echo "la ultima orden en la BBDD es la $ultima_orden y el meta es $order_meta y el estatus del pedido es $order_status<br>";
        }
        if (!isset($order_status) || empty($order_status) || $order_status !== 'completed') {
            $mostrar = 0;
        }
        
        if ($order_meta !== "") {
            $mostrar = 0;
        }
       
      //  $mostrar = 0;
      // echo "mostrar esta como $mostrar";
      
        //  update_post_meta( (int)$ultima_orden, '_review', '' );
        if ($mostrar === 1) {
            echo <<<AAA
    
            <div id="modal_review" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header pb-0">
                        <h3 id="pedido_ids" class="modal-title text-dark">😀 Califica tu Pedido #$ultima_orden</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>  
                    <div class="modal-body">
                        <div id="gracias_div" style="display:none">
                            <div class="row">
                                <div class="col-sm-12 pt-5 pb-5 text-center">
                                    <h2>🍔</h2>
                                    <h3>😀 Gracias!! Hemos Guardado tu selección.</h3>
                                    </div>
                            </div>
                        </div>
                        <div id="main_div">
                            <input type='hidden' id='usuario_id' value='$user_id' >
                            <input type='hidden' id='pedido_id' value='$ultima_orden'>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">     Califica tu motorista o envío                      
                                        <input type="hidden"  id="calificacion_motorista" value="0">
                                        <div class="calif">
                                            <div class="card-body text-center pb-0"> 
                                                <span class="myratings_calificacion_motorista"></span>                    
                                                <fieldset class="rating">
                                                    <input type="radio" class="califica_motorista_radio" id="star5" name="rating" value="5"  />
                                                    <label class="full" for="star5" title="Excelente"></label> 
                                                
                                                    <input type="radio" class="califica_motorista_radio"  id="star4" name="rating" value="4"  />
                                                    <label class="full" for="star4" title="4"></label> 
                                                
                                                    <input type="radio" class="califica_motorista_radio"  id="star3" name="rating" value="3" />
                                                    <label class="full" for="star3" title="Meh - 3 stars"></label>
                                            
                                                    <input type="radio" class="califica_motorista_radio"  id="star2" name="rating" value="2" />
                                                    <label class="full" for="star2" title="Kinda bad - 2 stars"></label>
                                                
                                                    <input type="radio" class="califica_motorista_radio"  id="star1" name="rating" value="1" />
                                                    <label class="full" for="star1" title="Sucks big time - 1 star"></label> 
                                                
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">     Que te pareció el producto.                      
                                        <input type="hidden"  id="calificacion_producto" value="0">
                                        <div class="calif">
                                            <div class="card-body text-center pb-0"> 
                                                <span class="myratings_calificacion_producto"></span>                    
                                                <fieldset class="rating">
                                                    <input type="radio" class="califica_producto_radio" id="star5_producto" name="rating_producto" value="5"  />
                                                    <label class="full" for="star5_producto" title="Excelente"></label> 
                                                
                                                    <input type="radio" class="califica_producto_radio"  id="star4_producto" name="rating_producto" value="4"  />
                                                    <label class="full" for="star4_producto" title="4"></label> 
                                                
                                                    <input type="radio" class="califica_producto_radio"  id="star3_producto" name="rating_producto" value="3" />
                                                    <label class="full" for="star3_producto" title="Meh - 3 stars"></label>
                                            
                                                    <input type="radio" class="califica_producto_radio"  id="star2_producto" name="rating_producto" value="2" />
                                                    <label class="full" for="star2_producto" title="Kinda bad - 2 stars"></label>
                                                
                                                    <input type="radio" class="califica_producto_radio"  id="star1_producto" name="rating_producto" value="1" />
                                                    <label class="full" for="star1_producto" title="Sucks big time - 1 star"></label> 
                                                
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">     Que te pareció nuestra plataforma.                      
                                        <input type="hidden"  id="calificacion_plataforma" value="0">
                                        <div class="calif">
                                            <div class="card-body text-center pb-0"> 
                                                <span class="myratings_calificacion_plataforma"></span>                    
                                                <fieldset class="rating">
                                                    <input type="radio" class="califica_plataforma_radio" id="star5_plataforma" name="rating_plataforma" value="5"  />
                                                    <label class="full" for="star5_plataforma" title="5"></label> 
                                                
                                                    <input type="radio" class="califica_plataforma_radio"  id="star4_plataforma" name="rating_plataforma" value="4"  />
                                                    <label class="full" for="star4_plataforma" title="4"></label> 
                                                
                                                    <input type="radio" class="califica_plataforma_radio"  id="star3_plataforma" name="rating_plataforma" value="3" />
                                                    <label class="full" for="star3_plataforma" title="3"></label>
                                            
                                                    <input type="radio" class="califica_plataforma_radio"  id="star2_plataforma" name="rating_plataforma" value="2" />
                                                    <label class="full" for="star2_plataforma" title="2"></label>
                                                
                                                    <input type="radio" class="califica_plataforma_radio"  id="star1_plataforma" name="rating_plataforma" value="1" />
                                                    <label class="full" for="star1_plataforma" title="1"></label> 
                                                
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">     Deja tu comentario.                      
                                            <div class="card-body text-center pb-0">                    
                                                <fieldset class="">
                                                    <input type="text" class="form-control" id="comentario" />
                                                </fieldset>
                                            </div>
                                    </div>
                                </div>

                                <div class="col text-center">
                                    <button class="btn btn-dark" id="boton_guardar_review" type="button">Guardar</button>
                                </div>
                            </div>
                            <div id="footer_div">
                                <div class="modal-footer pb-5">
                                    <div class="col text-center d-flex">
                                        <button class="btn btn-outline-dark btn-sm flex-fill" type="button" id="no_pedir_boton">No Calificar mas pedidos</button>
                                    </div>  		
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <script>
           jQuery(document).ready(function ($)

{

  

    $("#modal_review").modal('show');
 

})
    </script>
AAA;
        }
    } 
}
?>

