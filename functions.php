<?php

function agregar_scripts_y_estilos() {
    // Agregar scripts

   -//  wp_enqueue_style('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css');

    wp_enqueue_style( 'tailwind', get_template_directory_uri() . '/src/output.css', array() );
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



function handle_insert_ingreso() {
    if (!isset($_POST['categoria_index']) || !isset($_POST['estacionamiento_index']) || !isset($_POST['action']) || $_POST['action'] !== 'insert_ingreso') {
        error_log('Invalid POST data');
        wp_redirect(home_url('/leerqr/'));
        exit;
    }

    $categoria_index = intval($_POST['categoria_index']);
    $estacionamiento = sanitize_text_field($_POST['estacionamiento_index']); // Obtener el valor de estacionamiento desde POST

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
            '%s', // formato para estacionamiento
            '%d', // formato para categoria
            '%s'  // formato para horario_ingreso
        )
    );

    if ($result === false) {
        error_log('Error inserting data: ' . $wpdb->last_error);
    } else {
        error_log('Data inserted successfully');
    }

    wp_redirect(home_url('/leerqr/')); // Redirige a la página principal (ajusta según sea necesario)
    exit;
}



add_action('admin_post_nopriv_insert_ingreso', 'handle_insert_ingreso');
add_action('admin_post_insert_ingreso', 'handle_insert_ingreso');



// Añadir soporte para la variable paged en las consultas
function custom_query_vars($vars) {
    $vars[] = 'paged';
    return $vars;
}
add_filter('query_vars', 'custom_query_vars');







