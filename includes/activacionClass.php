<?php
class activation {

    public static function activar(){

        global $wpdb;

        $prefijo = $wpdb->prefix;
        $tabla1 = $prefijo."a_asignacion_motoristas";
        $create_ddl1 = <<<EOF
        CREATE TABLE  `$tabla1` (
    id INT AUTO_INCREMENT NOT NULL,
    delivery_boy_id INT NOT NULL UNIQUE,
    tienda_id INT NOT NULL,
    estado INT NOT NULL DEFAULT '1',
    PRIMARY KEY (id)
);

EOF;

       maybe_create_table($tabla1, $create_ddl1 );

        $tabla2 = $prefijo."a_asignacion_pedidos";
        $create_ddl2 = <<<EOF
        CREATE TABLE  `$tabla2` (
    id INT AUTO_INCREMENT NOT NULL,
    delivery_boy_id INT NOT NULL,
    pedido_id INT NOT NULL,
    status INT NOT NULL,
    asignado_el DATETIME,
    aceptado_el DATETIME NULL,
    fin_asignacion DATETIME NULL,
    PRIMARY KEY (id)
);

EOF;

        maybe_create_table($tabla2, $create_ddl2 );

    }
}