<?php

function agregar_scripts_y_estilos() {
    // Agregar scripts

   -//  wp_enqueue_style('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css');

    wp_enqueue_style( 'tailwind', get_template_directory_uri() . '/src/output.css', array() );
    wp_enqueue_script('instascan', get_template_directory_uri() . '/src/instascan.min.js', array() );

    wp_enqueue_script('pako', 'https://cdnjs.cloudflare.com/ajax/libs/pako/1.0.11/pako.min.js', array() );
 

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

?>