<?php

if (!session_id()) {
    session_start();
}


function agregar_scripts_y_estilos() {
    // Agregar scripts

   -//  wp_enqueue_style('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css');

    wp_enqueue_style( 'tailwind', get_template_directory_uri() . '/src/output.css', array() );

//Temporal Flowbite
 //   wp_enqueue_style( 'flowbite', 'https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css', array() );


    wp_enqueue_script( 'flowbite', 'https://cdn.tailwindcss.com', array() );

    

    wp_enqueue_script('instascan', get_template_directory_uri() . '/src/instascan.min.js', array() );

    wp_enqueue_script('pako', 'https://cdnjs.cloudflare.com/ajax/libs/pako/1.0.11/pako.min.js', array() );
 

    wp_enqueue_script('my-chart-js', 'https://cdn.jsdelivr.net/npm/apexcharts', array());

    wp_enqueue_style('mi-tema-estilos', get_stylesheet_uri());
    // Agregar estilos
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
}

add_action('wp_enqueue_scripts', 'agregar_scripts_y_estilos');





/*
function restrict_admin_access_for_editors() {
    $user = wp_get_current_user();
    
    // Verificar si el usuario es un editor
    if ( in_array( 'editor', (array) $user->roles ) ) {
        // Permitir el acceso a la página de administración de formularios
        $allowed_pages = array(
            'admin-post.php', // Para el manejo de formularios
            'admin-ajax.php'  // Para las peticiones AJAX
        );
        
        // Verificar la URL actual
        $current_screen = get_current_screen();
        $current_page = isset($current_screen->id) ? $current_screen->id : '';

        // Redirigir a la página de inicio si el usuario está intentando acceder a una página de administración no permitida
        if ( is_admin() && !defined( 'DOING_AJAX' ) && !in_array($current_page, $allowed_pages) ) {
            wp_redirect( home_url() );
            exit;
        }
    }
}
add_action( 'admin_init', 'restrict_admin_access_for_editors' );
*/


function redirect_editors_after_login($redirect_to, $requested_redirect_to, $user) {
    // Verificar si $user es un objeto WP_User y tiene el rol de editor
    if ($user instanceof WP_User && in_array('editor', (array) $user->roles)) {
        // Redirigir a la página principal después del inicio de sesión
        return home_url();
    }

    // En caso de que el usuario no sea editor, usar el redireccionamiento predeterminado
    return $redirect_to;
}
add_filter('login_redirect', 'redirect_editors_after_login', 10, 3);


function redirect_authors_after_login($redirect_to, $requested_redirect_to, $user) {
    // Verificar si $user es un objeto WP_User y tiene el rol de editor
    if ($user instanceof WP_User && in_array('author', (array) $user->roles)) {
        // Redirigir a la página principal después del inicio de sesión
        return home_url();
    }

    // En caso de que el usuario no sea autor, usar el redireccionamiento predeterminado
    return $redirect_to;
}
add_filter('login_redirect', 'redirect_authors_after_login', 10, 3);



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


function disable_admin_bar_for_specific_roles() {
    // Obtener el usuario actual
    $current_user = wp_get_current_user();

    // Roles para los que queremos deshabilitar la barra de administración
    $roles_to_disable = ['subscriber', 'editor' ,'author'];

    // Verificar si el usuario tiene alguno de los roles especificados
    if (array_intersect($roles_to_disable, $current_user->roles)) {
        // Deshabilitar la barra de administración
        add_filter('show_admin_bar', '__return_false');
    }
}
add_action('after_setup_theme', 'disable_admin_bar_for_specific_roles');




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






add_action('admin_post_edit_estacionamiento', 'handle_edit_estacionamiento');

function handle_edit_estacionamiento() {
    /*
    if (!current_user_can('edit_users')) {
        wp_die('No tienes permisos para realizar esta acción.');
    }
*/
    $user_id = intval($_POST['user_id']);
    $estacionamiento = sanitize_text_field($_POST['estacionamiento']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'estacionamientos';

    $wpdb->update(
        $table_name,
        array('estacionamiento' => $estacionamiento),
        array('user_id' => $user_id)
    );

    wp_redirect(home_url('/ajustes/'));
    exit;
}









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
    $redirect_url = isset($_POST['redirect_url']) ? esc_url_raw($_POST['redirect_url']) : home_url('/leerqr/');
    // Validar si los campos necesarios están presentes
    if (!isset($_POST['categoria_index']) || !isset($_POST['estacionamiento_index']) || !isset($_POST['action']) || $_POST['action'] !== 'insert_ingreso') {
        error_log('Invalid POST data');
        wp_redirect($redirect_url);
        exit;
    }

    $categoria_index = intval($_POST['categoria_index']);
    $estacionamiento = intval($_POST['estacionamiento_index']);

    // Validar los valores de estacionamiento y categoría
    $valid_estacionamientos = array(1, 2, 3, 4);
    $valid_categorias = array(0, 1, 2, 3);

    if (!in_array($estacionamiento, $valid_estacionamientos, true)) {
        $_SESSION['error_message'] = 'Error: Estacionamiento inválido.';
        wp_redirect($redirect_url);
        exit;
    }

    if (!in_array($categoria_index, $valid_categorias, true)) {
        $_SESSION['error_message'] = 'Error: Categoría inválida.';
        wp_redirect($redirect_url);
        exit;
    }

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
        $_SESSION['error_message'] = 'Error al ingresar el vehículo.';
    } else {
        error_log('Data inserted successfully');
        $_SESSION['message'] = 'Vehículo ingresado con éxito';
    }

    // Obtener la URL de redirección desde el formulario

    wp_redirect($redirect_url);
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
                $_SESSION['message'] = 'Error eliminando el vehículo.';
            } else {
                error_log('Deleted from ingresos ID: ' . $oldest_ingreso->id);
                $_SESSION['message'] = 'Vehículo eliminado con éxito.';
            }
        } else {
            error_log('Error inserting data into egresos: ' . $wpdb->last_error);
            $_SESSION['message'] = 'Error insertando datos en la tabla de egresos.';
        }
    } else {
        error_log('No record found to move');
        $_SESSION['message'] = 'No se encontró ningún vehículo para eliminar.';
    }

    $redirect_url = isset($_POST['redirect_url2']) ? esc_url_raw($_POST['redirect_url2']) : home_url('/leerqr/');
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_post_delete_oldest_ingreso', 'handle_delete_oldest_ingreso');












// Añadir estilos personalizados a la página de inicio de sesión
function custom_login_stylesheet() {
   // wp_enqueue_style('custom-login', get_stylesheet_directory_uri() . '/login-style.css');
   wp_enqueue_style('custom-login', 'https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css');
   wp_enqueue_script('custom-login', 'https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js');
}

add_action('login_enqueue_scripts', 'custom_login_stylesheet');



// Cambiar el logo de la página de inicio de sesión
function my_custom_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: url('http://logisticayservicios.unsl.edu.ar/wp-content/uploads/2023/06/logo.png');
            height: 65px;
            width: 320px;
            background-size: contain;
            background-repeat: no-repeat;
        }
    </style>
    <?php
}

add_action('login_enqueue_scripts', 'my_custom_login_logo');






add_action('wp_ajax_transfer_ingresos_to_egresos', 'transfer_ingresos_to_egresos_ajax');
add_action('wp_ajax_nopriv_transfer_ingresos_to_egresos', 'transfer_ingresos_to_egresos_ajax');

function transfer_ingresos_to_egresos_ajax() {
    if (!isset($_POST['selected_estacionamiento'])) {
        wp_send_json_error('Estacionamiento no seleccionado');
    }

    $selected_estacionamiento = intval($_POST['selected_estacionamiento']);

    // Llamar a la función que transfiere los datos de ingresos a egresos
    transfer_ingresos_to_egresos($selected_estacionamiento);

    wp_send_json_success('Datos transferidos exitosamente');
}

function transfer_ingresos_to_egresos($selected_estacionamiento) {
    if (!$selected_estacionamiento) {
        return; // Si no hay estacionamiento seleccionado, no hacer nada
    }

    global $wpdb;
    $table_name_ingresos = $wpdb->prefix . 'parking_ingresos';
    $table_name_egresos = $wpdb->prefix . 'parking_egresos';

    // Obtener la fecha actual
    $current_date = current_time('Y-m-d');

    // Obtener todos los ingresos para el estacionamiento seleccionado que son anteriores al día actual
    $ingresos = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name_ingresos WHERE estacionamiento = %d AND DATE(horario_ingreso) < %s",
            $selected_estacionamiento,
            $current_date
        )
    );

    // Transferir cada ingreso a la tabla de egresos
    foreach ($ingresos as $ingreso) {
        $wpdb->insert(
            $table_name_egresos,
            [
                'estacionamiento' => $ingreso->estacionamiento,
                'categoria' => $ingreso->categoria,
                'horario_egreso' => current_time('mysql') // Usar la hora actual como horario de egreso
            ],
            [
                '%d',
                '%d',
                '%s'
            ]
        );

        // Eliminar el ingreso de la tabla de ingresos
        $wpdb->delete(
            $table_name_ingresos,
            ['id' => $ingreso->id],
            ['%d']
        );
    }
}





function create_estacionamientos_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'estacionamientos';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        estacionamiento int(11) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_switch_theme', 'create_estacionamientos_table');







function crear_tabla_ajustes() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'ajustes_estacionamiento';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $tabla (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        capacidad_bloque_iii int NOT NULL,
        capacidad_bloque_iv int NOT NULL,
        capacidad_subsuelo_rectorado int NOT NULL,
        capacidad_chacabuco_pedernera int NOT NULL,
        horario_ingreso time NOT NULL,
        horario_egreso time NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Insertar fila si no existe
    if ($wpdb->get_var("SELECT COUNT(*) FROM $tabla") == 0) {
        $wpdb->insert($tabla, array(
            'capacidad_bloque_iii' => 0,
            'capacidad_bloque_iv' => 0,
            'capacidad_subsuelo_rectorado' => 0,
            'capacidad_chacabuco_pedernera' => 0,
            'horario_ingreso' => '09:00:00',
            'horario_egreso' => '18:00:00'
        ));
    }
}
add_action('after_setup_theme', 'crear_tabla_ajustes');




function actualizar_capacidad_estacionamiento() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['capacidad'])) {
        global $wpdb;
        $tabla = $wpdb->prefix . 'ajustes_estacionamiento';
        
        $capacidad = intval($_POST['capacidad']);
        $categoria = sanitize_text_field($_POST['categoria']);
    
        $columna = '';
        switch ($categoria) {
            case 'Bloque III':
                $columna = 'capacidad_bloque_iii';
                break;
            case 'Bloque IV':
                $columna = 'capacidad_bloque_iv';
                break;
            case 'Subsuelo y Rectorado':
                $columna = 'capacidad_subsuelo_rectorado';
                break;
            case 'Chacabuco y Pedernera':
                $columna = 'capacidad_chacabuco_pedernera';
                break;
        }
    
        if ($columna) {
            $wpdb->update($tabla, array($columna => $capacidad), array('id' => 1));
        }
    }


    wp_redirect(home_url('/ajustes/'));
    
}
add_action('admin_post_actualizar_capacidad_estacionamiento', 'actualizar_capacidad_estacionamiento');




function actualizar_horarios_estacionamiento() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['horario_ingreso']) && isset($_POST['horario_egreso'])) {
        global $wpdb;
        $tabla = $wpdb->prefix . 'ajustes_estacionamiento';
        
        $horario_ingreso = sanitize_text_field($_POST['horario_ingreso']);
        $horario_egreso = sanitize_text_field($_POST['horario_egreso']);
    
        $wpdb->update($tabla, array(
            'horario_ingreso' => $horario_ingreso,
            'horario_egreso' => $horario_egreso
        ), array('id' => 1));
    }

    wp_redirect(home_url('/ajustes/'));
    
}
add_action('admin_post_actualizar_horarios_estacionamiento', 'actualizar_horarios_estacionamiento');


