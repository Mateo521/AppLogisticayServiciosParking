<?php
get_header();


get_template_part('template-parts/estacionamiento_actual');

if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
get_template_part('template-parts/egreso');
}

get_footer();
?>