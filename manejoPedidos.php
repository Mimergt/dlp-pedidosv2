<?php
/**
 * Plugin Name:  Manejo de ordenes y delivery version 2
 * Plugin URI:   https://github.com/Mimergt/dlp-pedidosv2
 * Version:      1.3.2.4
 * Update URI:   https://github.com/Mimergt/dlp-pedidosv2
 * GitHub Plugin URI: Mimergt/dlp-pedidosv2
 * GitHub Branch: main
 */
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

date_default_timezone_set('America/Guatemala');

remove_action('login_init', 'send_frame_options_header');

add_action( 'admin_menu', 'wporg_options_pagess' );
function wporg_options_pagess() {
    add_menu_page(
        'Manejo de Pedidos',
        'Motoristas',
        'manage_options',
        'manejo_pedidos',
        'iniciar_plugin',
        '',
        20
    );
}

function iniciar_plugin(){

    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'archivos/asignacion_a_tiendas.php';
}

require_once plugin_dir_path( __FILE__ ).'archivos/pedidos2.php';
add_shortcode( 'pedidos2', 'pedidos2' );

require_once plugin_dir_path( __FILE__ ).'archivos/pedidos21.php';
add_shortcode( 'pedidos21', 'pedidos21' );


add_action( 'admin_enqueue_scripts', 'my_enqueue' );

function my_enqueue() {
    // asigna motoristas a tiendas
    wp_enqueue_script( 'ajax-script', plugins_url( '/js/asignacion_a_tiendas.js', __FILE__ ), array('jquery') );

    if( is_page( array( 'en_camino', 'pedidos', 'pedidos2', 'pedidos21', 'panel-de-monitoreo', 'panel_motorista' ) ) ){
    // asigna motoristas a tiendas
    wp_enqueue_script( 'ajax-script', plugins_url( '/js/asignacion_a_motorista.js', __FILE__ ), array('jquery') );
  }
    // para plugin de contador
    wp_enqueue_script( 'contaDorA', plugins_url( '/js/funcionMain.js', __FILE__ ), array('jquery') );


    // para el drop de los motoritas por tienda
    wp_enqueue_script( 'motoTienda', plugins_url( '/monitoreoDelivery/jquery_selector.js', __FILE__ ), array('jquery') );

    // para bloquear clientes
    wp_enqueue_script( 'btn_block_cliente', plugins_url( '/monitoreoDelivery/bloquear_cliente.js', __FILE__ ), array('jquery') );
    
    
    // para agregar la hora de salida
    wp_enqueue_script( 'btn_terminado_click', plugins_url( '/hora_envio/hora_envio.js', __FILE__ ), array('jquery') );
    
    
    
    
    wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );


  //  wp_enqueue_script( 'mapas_seguimiento', plugins_url( '/mapasSeguimiento/mapa_seguimiento_pedidos.js', __FILE__ ), array('jquery')  ,null, false);

   
    // $order->update_meta_data( '_custom_meta_key', 'value' );

}



add_action( 'wp_ajax_asignaTienda', 'asignaTienda' );
function asignaTienda() {
    global $wpdb;
    $cual =  $_POST['cual'] ;
    $tienda = $_POST['tienda'];
    require_once plugin_dir_path( __FILE__ ).'includes/asignaTienda.php';

    wp_die();
}


add_action( 'wp_enqueue_scripts', 'enqueue_usuario' );

function enqueue_usuario() {
    if( is_page( array( 'en_camino', 'pedidos', 'pedidos2', 'pedidos21', 'panel-de-monitoreo', 'panel_motorista' ) ) ){
    // asigna motoristas a tiendas
    wp_enqueue_script( 'ajax-script', plugins_url( '/js/asignacion_a_motorista.js', __FILE__ ), array('jquery') );
}
    // motorista decision pedido
    wp_enqueue_script( 'mape', plugins_url( '/panelMotoristas/aceptacionPedido.js', __FILE__ ), array('jquery') );

    // motorista decision pedido
    wp_enqueue_script( 'maep', plugins_url( '/panelMotoristas/entregaPedido.js', __FILE__ ), array('jquery') );

    // motorista decision pedido
    wp_enqueue_script( 'panmo', plugins_url( '/monitoreoDelivery/funcionesJs.js', __FILE__ ), array('jquery') );

    // para plugin de contador
    wp_enqueue_script( 'contaDor', plugins_url( '/js/funcionMain.js', __FILE__ ), array('jquery') );

    // para el drop de los motoritas por tienda
    wp_enqueue_script( 'motoTienda', plugins_url( '/monitoreoDelivery/jquery_selector.js', __FILE__ ), array('jquery') );


    // para bloquear clientes
    wp_enqueue_script( 'btn_block_cliente', plugins_url( '/monitoreoDelivery/bloquear_cliente.js', __FILE__ ), array('jquery') );



    wp_enqueue_script( 'mapas_seguimiento1', plugins_url( 'monitoreoDelivery/mapasSeguimiento/mapas_seguimiento_pedidos.js', __FILE__ ), array('jquery')  ,null, true);

    
     // para agregar la hora de salida
    wp_enqueue_script( 'btn_terminado_click', plugins_url( '/hora_envio/hora_envio.js', __FILE__ ), array('jquery') );
    
    
    
    wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    

    // js para el mapa del miercoles
//    wp_enqueue_script( 'mapas', plugins_url( 'appMotoristas/mapaElmer.js', __FILE__ ), array('jquery') );



}
/*
function java_donde_quiero() {
    if( is_page( array( 'validaUsuarioAppMotorista', 'en_camino', 'usuarioinyectacoordenada' ) ) ){
        wp_enqueue_script('leaflet1.js', 'http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js' ,array() ,null, false);
    //    wp_enqueue_script( 'mapas', plugins_url( 'appMotoristas/mapaElmer.js', __FILE__ ), array('jquery') ,null, false);


    }
}
add_action( 'wp_enqueue_scripts', 'java_donde_quiero' );
*/


function my_enqueue_review() {

    wp_enqueue_script( 'ajax-script_review', plugins_url( '/review/main_js.js', __FILE__ ), array('jquery') );

    wp_localize_script( 'ajax-script_review', 'my_ajax_object_review', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'my_enqueue_review' );


add_action( 'wp_ajax_funcion_review', 'funcion_review' );
function funcion_review() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'review/funcion_review.php';
    wp_die();
}


add_action( 'wp_ajax_cancelar_pedido', 'cancelar_pedido' );
function cancelar_pedido() {
    global $wpdb;
    $cualPedido = isset($_POST['cualPedido']) ? absint($_POST['cualPedido']) : 0;
    $motivo = isset($_POST['motivo']) ? sanitize_textarea_field($_POST['motivo']) : '';

    if (!$cualPedido) {
        wp_send_json_error(array('message' => 'Pedido invalido'), 400);
    }

    $order = wc_get_order($cualPedido);
    if (!$order) {
        wp_send_json_error(array('message' => 'Pedido no encontrado'), 404);
    }

    try {
        $order->update_status('cancelled');

        if (!empty($motivo)) {
            $order->add_order_note('Motivo de cancelacion: '.$motivo);
            update_post_meta($cualPedido, '_motivo_cancelacion_tienda', $motivo);
        }

        $fecha = date('Y-m-d H:i:s');
        $tabla_asignacion_tiendas = $wpdb->prefix . 'a_asignacion_pedidos';
        $sql = $wpdb->prepare(
            "UPDATE $tabla_asignacion_tiendas SET status = 3, fin_asignacion = %s WHERE pedido_id = %d",
            $fecha,
            $cualPedido
        );
        $wpdb->query($sql);

        wp_send_json_success(array(
            'order_id' => $cualPedido,
            'new_status' => $order->get_status(),
        ));
    } catch (Exception $e) {
        wp_send_json_error(array('message' => $e->getMessage()), 500);
    }
}

add_action( 'wp_ajax_busco_motoristas', 'busco_motoristas' );
function busco_motoristas() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'monitoreoDelivery/short_motoristas_tiendas.php';

    wp_die();
}

add_action( 'wp_ajax_termina_pedido', 'termina_pedido' );
function termina_pedido() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'includes/termina_pedido.php';

    wp_die();
}


add_action( 'wp_ajax_asigna_pedido', 'asigna_pedido' );
function asigna_pedido() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'includes/asignaPedidoMotorista.php';

    wp_die();
}

add_action( 'wp_ajax_bloquear_usuario_accion', 'bloquear_usuario_accion' );
function bloquear_usuario_accion() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'monitoreoDelivery/bloquear_usuario_accion.php';
    wp_die();
}


add_action( 'wp_ajax_m_rechaza_pedido', 'm_rechaza_pedido' );
function m_rechaza_pedido() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'panelMotoristas/m_rechaza_pedido.php';

    wp_die();
}


add_action( 'wp_ajax_m_acepta_pedido', 'm_acepta_pedido' );
function m_acepta_pedido() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'panelMotoristas/m_acepta_pedido.php';

    wp_die();
}

add_action( 'wp_ajax_m_no_disponible', 'm_no_disponible' );
function m_no_disponible() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'panelMotoristas/m_no_disponible.php';

    wp_die();
}

add_action( 'wp_ajax_m_disponible', 'm_disponible' );
function m_disponible() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'panelMotoristas/m_disponible.php';
    wp_die();
}

add_action( 'wp_ajax_m_pedido_entregado', 'm_pedido_entregado' );
function m_pedido_entregado() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'panelMotoristas/m_pedido_entregado.php';

    wp_die();
}


add_action( 'wp_ajax_agrego_meta_horario', 'agrego_meta_horario' );
function agrego_meta_horario() {
    global $wpdb;
    require_once plugin_dir_path( __FILE__ ).'hora_envio/hora_envio_ajax.php';

    wp_die();
}


function check_login_motorista( $user_login, $user ) {
    global $wpdb;

$u = $user->ID;


     if($user->roles[0] !== 'deliveryboy_user'){
       //echo "Hola $user_login, mira como te bloqueo tu plugin";
     } else {
       $motorman = $user->ID;
       $prefijo = $wpdb->prefix;
       $tabla_asignacion_motoristas = $prefijo . "a_asignacion_motoristas";
       $sql = "UPDATE $tabla_asignacion_motoristas SET estado = 1 WHERE delivery_boy_id = $motorman";
       $wpdb->query($wpdb->prepare($sql));

     }

}


add_action( 'clear_auth_cookie', 'return_user_data_on_logout');

function return_user_data_on_logout( $user ) {
  global $wpdb;

  $u = wp_get_current_user();

  if($u->roles[0] !== 'deliveryboy_user'){
    //echo "Hola $user_login, mira como te bloqueo tu plugin";
  } else {
    $motorman = $u->ID;
    $prefijo = $wpdb->prefix;
    $tabla_asignacion_motoristas = $prefijo . "a_asignacion_motoristas";
    $sql = "UPDATE $tabla_asignacion_motoristas SET estado = 0 WHERE delivery_boy_id = $motorman";
    $wpdb->query($wpdb->prepare($sql));

  }
}



// Esto produce un error al hacer logout
// 
//add_action('wp_login', 'check_login_motorista', 10, 2);

//function check_logout_motorista() {
//global $wpdb;

//$user = wp_get_current_user();

//$sql = "INSERT INTO test_datos SET valor = $user ";
//$wpdb->query($wpdb->prepare($sql));


//}
//add_action( 'wp_logout', 'check_logout_motorista' );






// activacion
function alActivar(){
    require_once plugin_dir_path( __FILE__ ).'includes/activacionClass.php';
    activation::activar();
}

register_activation_hook( __FILE__, 'alActivar');

// desactivacion
function alDesActivar(){
    require_once plugin_dir_path( __FILE__ ).'includes/desActivacionClass.php';
    deActivation::desactivar();
}

register_deactivation_hook('__FILE__', 'alDesActivar');


// shortcode para el panel de monitoreo del delivery

require_once plugin_dir_path( __FILE__ ).'monitoreoDelivery/panelMonitoreo.php';
add_shortcode( 'panelMonitoreo', 'panelMonitoreo' );


// shortcode para el panel del motorista

require_once plugin_dir_path( __FILE__ ).'panelMotoristas/panel.php';
add_shortcode( 'panelMotorista', 'panelMotorista' );


// shortcode para el seguimiento de los envios

require_once plugin_dir_path( __FILE__ ).'monitoreoDelivery/en_camino.php';
add_shortcode( 'en_camino', 'en_camino' );


// shortcode mapa en camino

require_once plugin_dir_path( __FILE__ ).'monitoreoDelivery/mapasSeguimiento/mapa_seguimiento_pedidos.php';
add_shortcode( 'mapa_seguimiento', 'mapa_seguimiento' );


// shortcode footer //
require_once plugin_dir_path( __FILE__ ).'monitoreoDelivery/mapasSeguimiento/footer_seguimiento.php';
add_shortcode( 'footer_seguimiento', 'footer_seguimiento' );




// shortcode tracker //
require_once plugin_dir_path( __FILE__ ).'short_tracker/tracker.php';
add_shortcode( 'ver_tracker', 'ver_tracker' );




/*
// Incluir Bootstrap CSS
function bootstrap_css() {
    wp_enqueue_style( 'bootstrap_css',
        'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css',
        array(),
        '4.1.3'
    );
}
add_action( 'admin_enqueue_scripts', 'bootstrap_css');
*/
// Incluir picker CSS
function picker_css() {
    wp_enqueue_style( 'picker_css', plugins_url( '/css/pickerM.css', __FILE__ ) );

    wp_enqueue_style( 'leaflet.css', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', array() );
    wp_enqueue_style( 'leafletFull.css', 'https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css', array() );

  //  wp_enqueue_style( 'leaflet.css',  'http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css');

}

add_action( 'admin_enqueue_scripts', 'picker_css');

// Incluir Bootstrap JS y dependencia popper en el admin
function agregarJs() {
    wp_enqueue_script( 'popper_js',
        'https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js',
        array(),
        '1.14.3',
        true);
    
    wp_enqueue_script( 'bootstrap_js',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js',
        array('jquery','popper_js'),
        '4.5.2',
        true);

    wp_enqueue_script( 'sweet_alertAdmin',
        'https://cdn.jsdelivr.net/npm/sweetalert2@9',
        array(),
        '1.14.3',
        true);

    wp_enqueue_script( 'pickerM',
        plugins_url( '/js/pickerM.js', __FILE__ ),
        array(),
        '1.14.3',
        true);
    
    
     
        
        

  wp_enqueue_script('leaflet.js', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' ,array() ,null, false);

  wp_enqueue_script('leafletFull.js', 'https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js' ,array() ,null, false);


  //  wp_enqueue_script( 'mapas_seguimiento2', plugins_url( 'monitoreoDsdfsdfelivery/mapasSeguimiento/mapa_seguimiento_pedidos.js', __FILE__ ), array('jquery') ,null, false);

}
add_action( 'admin_enqueue_scripts', 'agregarJs');

// incluir lo mismo en el user side


// Incluir Bootstrap CSS
function bootstrap_cssUser() {
    
    wp_enqueue_style( 'bootstrap_css',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css',
        array(),
        '4.5.2'
    );

    wp_enqueue_style( 'pickerM_css',
        plugins_url( '/css/pickerM.css', __FILE__ ),
        array(),
        '4.1.3'
    );

     wp_enqueue_style( 'leaflet.css', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', array() );
     wp_enqueue_style( 'leafletFull.css', 'https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css', array() );
     
     wp_enqueue_style( 'review_awesome.css', 'https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css', array() );
     

 wp_enqueue_style( 'stylesheet', 'https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css');
 wp_enqueue_style( 'stylesheet', 'https://fonts.googleapis.com/css?family=Calibri:400,300,700');
 wp_enqueue_style( 'review_css', plugins_url( '/css/review.css', __FILE__ ), array(), '' ); 
    

     
}
add_action( 'wp_enqueue_scripts', 'bootstrap_cssUser');

// Incluir Bootstrap JS y dependencia popper en el user
function agregarJsUser() {
    wp_enqueue_script( 'popper_js',
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js',
        array(),
        '1.14.3',
        true);
    
    wp_enqueue_script( 'bootstrap_js',
        'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js',
        array('jquery','popper_js'),
        '4.5.2',
        false);

    wp_enqueue_script( 'sweet_alertUser',
        'https://cdn.jsdelivr.net/npm/sweetalert2@10',
        array(),
        '',
        false);

    wp_enqueue_script( 'pickerM',
        plugins_url( '/js/pickerM.js', __FILE__ ),
        array(),
        '1.14.3',
        true);
    
        
        
    
// wp_enqueue_script( 'mapas_seguimiento', plugins_url( '/monitoreoDelivery/mapas_seguimiento_pedidos.js', __FILE__ ), array('jquery') ,null, false);
        wp_enqueue_script('leaflet.js', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' ,array() ,null, false);
        wp_enqueue_script('leafletFull.js', 'https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js' ,array() ,null, false);


}
add_action( 'wp_enqueue_scripts', 'agregarJsUser');




/* ==================================================== app motoristas  ===================================================  */

require_once plugin_dir_path( __FILE__ ).'appMotoristas/validaUsuario.php';
add_shortcode( 'validaUsuarioAppMotorista', 'validaUsuarioAppMotorista' );







/* ==================================================== app motoristas  ===================================================  */

function geocode($address){

$USERAGENT = $_SERVER['HTTP_USER_AGENT'];
$opts = array('http'=>array('header'=>"User-Agent: $USERAGENT\r\n"));
$context = stream_context_create($opts);
$address = urlencode($address);
$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=AIzaSyD3yyERvzeZ5PAUKHxwO46W8qeyvLkeSlc";
$resp_json = file_get_contents($url, false, $context);
return $resp_json;
}

function haversineGreatCircleDistance(
$latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
// convert from degrees to radians
$latFrom = deg2rad($latitudeFrom);
$lonFrom = deg2rad($longitudeFrom);
$latTo = deg2rad($latitudeTo);
$lonTo = deg2rad($longitudeTo);

$latDelta = $latTo - $latFrom;
$lonDelta = $lonTo - $lonFrom;

$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
  cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
return $angle * $earthRadius;
}


/* ==================================================== app review  ===================================================  */





require_once plugin_dir_path( __FILE__ ).'review/main.php';
add_shortcode( 'shortcode_reviews', 'shortcode_reviews' );



// shortcode mapa en camino  test

function shortcode_agrego_js_mapa(){
    wp_enqueue_script( 'mapa_modal_js', plugins_url( 'monitoreoDelivery/mapasSeguimiento/mapa_modal.js', __FILE__ ), array('jquery')  ,null, true);
}
add_action( 'wp_enqueue_scripts', 'shortcode_agrego_js_mapa');

require_once plugin_dir_path( __FILE__ ).'monitoreoDelivery/mapasSeguimiento/mapa_modal.php';
add_shortcode( 'mapa_seguimiento_2', 'mapa_seguimiento_2' );