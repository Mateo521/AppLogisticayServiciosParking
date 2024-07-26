<?php

function agregar_scripts_y_estilos() {
    // Agregar scripts

   -//  wp_enqueue_style('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css');

    wp_enqueue_style( 'tailwind', get_template_directory_uri() . '/src/output.css', array() );

//Temporal Flowbite
    wp_enqueue_style( 'flowbite', 'https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css', array() );
    

    wp_enqueue_script('instascan', get_template_directory_uri() . '/src/instascan.min.js', array() );

    wp_enqueue_script('pako', 'https://cdnjs.cloudflare.com/ajax/libs/pako/1.0.11/pako.min.js', array() );
 

    wp_enqueue_script('my-chart-js', 'https://cdn.jsdelivr.net/npm/apexcharts', array());

    wp_enqueue_style('mi-tema-estilos', get_stylesheet_uri());
    // Agregar estilos
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
}

add_action('wp_enqueue_scripts', 'agregar_scripts_y_estilos');






function restrict_admin_access_for_editors() {
    $user = wp_get_current_user();
    if ( in_array( 'editor', (array) $user->roles ) && is_admin() && !defined( 'DOING_AJAX' ) ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'admin_init', 'restrict_admin_access_for_editors' );





function redirect_subscribers_to_home() {
    $user = wp_get_current_user();
    if ( in_array( 'subscriber', (array) $user->roles ) ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'admin_init', 'redirect_subscribers_to_home' );


function restrict_admin_access_for_subscribers() {
    $user = wp_get_current_user();
    if ( in_array( 'subscriber', (array) $user->roles ) && is_admin() && !defined( 'DOING_AJAX' ) ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'admin_init', 'restrict_admin_access_for_subscribers' );



function custom_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'subscriber', $user->roles ) ) {
            return home_url();
        }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'custom_login_redirect', 10, 3 );







function restrict_access_to_logged_in_users() {
    if ( !is_user_logged_in() && !is_page('wp-login.php') && !is_admin() ) {
        wp_redirect( wp_login_url() );
        exit;
    }
}
add_action( 'template_redirect', 'restrict_access_to_logged_in_users' );
















function create_parking_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Tabla de ingresos
    $table_name_ingresos = $wpdb->prefix . 'parking_ingresos';
    $sql_ingresos = "CREATE TABLE $table_name_ingresos (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        estacionamiento tinyint(1) NOT NULL,
        categoria tinyint(1) NOT NULL,
        horario_ingreso datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Tabla de egresos
    $table_name_egresos = $wpdb->prefix . 'parking_egresos';
    $sql_egresos = "CREATE TABLE $table_name_egresos (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        estacionamiento tinyint(1) NOT NULL,
        categoria tinyint(1) NOT NULL,
        horario_egreso datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_ingresos);
    dbDelta($sql_egresos);

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_ingresos'") != $table_name_ingresos) {
        error_log('Error: tabla de ingresos no creada.');
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_egresos'") != $table_name_egresos) {
        error_log('Error: tabla de egresos no creada.');
    }
}

add_action('after_switch_theme', 'create_parking_tables');




function get_parking_ingresos() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'parking_ingresos';
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    return $results;
}






// Función para insertar un ingreso
function handle_insert_ingreso() {
    if (!isset($_POST['categoria_index']) || !isset($_POST['estacionamiento_index']) || !isset($_POST['action']) || $_POST['action'] !== 'insert_ingreso') {
        error_log('Invalid POST data');
        wp_redirect(home_url('/leerqr/'));
        exit;
    }

    $categoria_index = intval($_POST['categoria_index']);
    $estacionamiento = sanitize_text_field($_POST['estacionamiento_index']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'parking_ingresos';

    $result = $wpdb->insert(
        $table_name,
        array(
            'estacionamiento' => $estacionamiento,
            'categoria' => $categoria_index,
            'horario_ingreso' => current_time('mysql'),
        ),
        array(
            '%d',
            '%d',
            '%s'
        )
    );

    if ($result === false) {
        error_log('Error inserting data: ' . $wpdb->last_error);
    } else {
        error_log('Data inserted successfully');
    }

    wp_redirect(home_url('/leerqr/'));
    exit;
}
add_action('admin_post_insert_ingreso', 'handle_insert_ingreso');

// Función para eliminar el ingreso más antiguo e insertar en egresos
function handle_delete_oldest_ingreso() {
    if (!isset($_POST['categoria_index2']) || !isset($_POST['estacionamiento_index2']) || !isset($_POST['action']) || $_POST['action'] !== 'delete_oldest_ingreso') {
        echo 'Invalid POST data';
        wp_redirect(home_url('/leerqr/'));
        exit;
    }

    $categoria_index = intval($_POST['categoria_index2']);
    $estacionamiento_index = intval($_POST['estacionamiento_index2']);
    
    global $wpdb;
    $table_ingresos = $wpdb->prefix . 'parking_ingresos';
    $table_egresos = $wpdb->prefix . 'parking_egresos';

    // Obtener el ingreso más antiguo para la categoría y estacionamiento seleccionados
    $query = $wpdb->prepare(
        "SELECT * FROM $table_ingresos WHERE categoria = %d AND estacionamiento = %d ORDER BY horario_ingreso ASC LIMIT 1",
        $categoria_index,
        $estacionamiento_index
    );

    $oldest_ingreso = $wpdb->get_row($query);

    if ($oldest_ingreso) {
        // Insertar el ingreso más antiguo en la tabla de egresos
        $result_insert = $wpdb->insert(
            $table_egresos,
            array(
                'estacionamiento' => $oldest_ingreso->estacionamiento,
                'categoria' => $oldest_ingreso->categoria,
                'horario_egreso' => current_time('mysql'),
            ),
            array(
                '%d',
                '%d',
                '%s'
            )
        );

        if ($result_insert !== false) {
            // Eliminar el ingreso de la tabla de ingresos
            $result_delete = $wpdb->delete(
                $table_ingresos,
                array('id' => $oldest_ingreso->id),
                array('%d')
            );

            if ($result_delete === false) {
                error_log('Error deleting data: ' . $wpdb->last_error);
            } else {
                error_log('Deleted from ingresos ID: ' . $oldest_ingreso->id);
            }
        } else {
            error_log('Error inserting data into egresos: ' . $wpdb->last_error);
        }
    } else {
        error_log('No record found to move');
    }

    wp_redirect(home_url('/leerqr/'));
    exit;
}
add_action('admin_post_delete_oldest_ingreso', 'handle_delete_oldest_ingreso');










// Añadir soporte para la variable paged en las consultas
function custom_query_vars($vars) {
    $vars[] = 'paged';
    return $vars;
}
add_filter('query_vars', 'custom_query_vars');






// Agregar una acción para el Cron Job
add_action('wp', 'setup_transfer_cron_job');

function setup_transfer_cron_job() {
    if (!wp_next_scheduled('transfer_ingresos_event')) {
        wp_schedule_event(strtotime('23:59:00'), 'daily', 'transfer_ingresos_event');
    }
}

// La función que se ejecutará
add_action('transfer_ingresos_event', 'transfer_ingresos_to_egresos');

function transfer_ingresos_to_egresos() {
    global $wpdb;
    $table_ingresos = $wpdb->prefix . 'parking_ingresos';
    $table_egresos = $wpdb->prefix . 'parking_egresos';

    // Verificar si hay registros para transferir
    $ingresos = $wpdb->get_results("SELECT * FROM $table_ingresos", ARRAY_A);

    if (!empty($ingresos)) {
         // Asegurarse de que las claves del arreglo coincidan con los nombres de las columnas
        foreach ($ingresos as $ingreso) {
            $data = array(
                'id' => $ingreso['id'],
                'estacionamiento' => $ingreso['estacionamiento'],
                'categoria' => $ingreso['categoria'],
                'horario_egreso' => current_time('mysql'),
            );
            $wpdb->insert($table_egresos, $data);
        }

        // Eliminar los datos de la tabla de ingresos
        $wpdb->query("DELETE FROM $table_ingresos");
    }
}
// Desprogramar el evento Cron
function remove_transfer_cron_job() {
    $timestamp = wp_next_scheduled('transfer_ingresos_event');
    wp_unschedule_event($timestamp, 'transfer_ingresos_event');
}

add_action('switch_theme', 'remove_transfer_cron_job'); 

// Comprobar y transferir al inicio del día
function check_and_transfer_at_start() {
    // Establecer la zona horaria
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Obtener la hora actual
    $current_time = date('H:i');

    // Hora de apertura (07:00)
    $opening_time = '07:00';

    if ($current_time >= $opening_time && $current_time <= '07:00') {
        transfer_ingresos_to_egresos();
    }
}

// Llamar a la función de verificación al inicio del día

add_action('init', 'check_and_transfer_at_start');




