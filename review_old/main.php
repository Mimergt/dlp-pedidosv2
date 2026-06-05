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
        $order_status = $order->get_status();
        $order_meta = $order->get_meta('_puntaje_pedido');
       // echo "la ultima orden en la BBDD es la $ultima_orden y el meta es $order_meta y el estatus del pedido es $order_status<br>";
        
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
                            <input type='hidden' id='usuario_id' value='$current_user_id' >
                            <input type='hidden' id='pedido_id' value=''>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">                           
                                        <input type="hidden"  id="customRange3" value="0">
                                        <div class="calif">
                                            <div class="card-body text-center pb-0"> 
                                                <span class="myratings"></span>                    
                                                <fieldset class="rating">
                                                    <input type="radio" id="star5" name="rating" value="5"  />
                                                    <label class="full" for="star5" title="Excelente"></label> 
                                                
                                                    <input type="radio" id="star4" name="rating" value="4"  />
                                                    <label class="full" for="star4" title="4"></label> 
                                                
                                                    <input type="radio" id="star3" name="rating" value="3" />
                                                    <label class="full" for="star3" title="Meh - 3 stars"></label>
                                            
                                                    <input type="radio" id="star2" name="rating" value="2" />
                                                    <label class="full" for="star2" title="Kinda bad - 2 stars"></label>
                                                
                                                    <input type="radio" id="star1" name="rating" value="1" />
                                                    <label class="full" for="star1" title="Sucks big time - 1 star"></label> 
                                                
                                                </fieldset>
                                            </div>
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

