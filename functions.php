<?php

function agregar_scripts_y_estilos() {
    // Agregar scripts

   -//  wp_enqueue_style('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css');

    wp_enqueue_style( 'tailwind', get_template_directory_uri() . '/src/output.css', array() );


    wp_enqueue_style('mi-tema-estilos', get_stylesheet_uri());
    // Agregar estilos
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
}

add_action('wp_enqueue_scripts', 'agregar_scripts_y_estilos');



?>