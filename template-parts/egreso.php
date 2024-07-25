<?php
global $selected_estacionamiento;

// Obtener el número de página actual
$current_page = max(1, get_query_var('page', 1));

// Número de resultados por página
$per_page = 10;

// Calcular el offset
$offset = ($current_page - 1) * $per_page;




// Incluir el archivo
require_once(get_template_directory() . '/template-parts/estacionamiento_actual.php');


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

function check_and_transfer() {
    // Establecer la zona horaria
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Obtener la hora actual
    $current_time = date('H:i');

    // Hora de cierre (19:00)
    $closing_time = '23:59';

    if ($current_time >= $closing_time) {
        transfer_ingresos_to_egresos();
    }
}

// Llamar a la función de verificación
check_and_transfer();

$table_egresos = $wpdb->prefix . 'parking_egresos';
$query = "SELECT * FROM $table_egresos";


if ($selected_estacionamiento) {
    $query .= $wpdb->prepare(" WHERE estacionamiento = %d", $selected_estacionamiento);
}
$query .= " ORDER BY id DESC";
$query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);
$egresos = $wpdb->get_results($query, ARRAY_A);



// Obtener el número total de registros
$count_query = "SELECT COUNT(*) FROM $table_egresos";
if ($selected_estacionamiento) {
    $count_query .= $wpdb->prepare(" WHERE estacionamiento = %d", $selected_estacionamiento);
}
$total_e_items = $wpdb->get_var($count_query);
?>


<p class="mb-3 px-3 pt-12 text-gray-500 dark:text-gray-400"> <strong class="font-semibold text-gray-900 dark:text-white">Egresos</strong>.</p>


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">ID</th>
                <th scope="col" class="px-6 py-3">Estacionamiento</th>
                <th scope="col" class="px-6 py-3">Categoría</th>
                <th scope="col" class="px-6 py-3">Horario de egreso</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($egresos)) : ?>
                <?php foreach ($egresos as $egreso) : ?>
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <?php echo esc_html($egreso['id']); ?>
                        </th>
                        <td class="px-6 py-4">
                            <?php echo esc_html($egreso['estacionamiento']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo esc_html($egreso['categoria']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $date_format = get_option('date_format');
                            $time_format = get_option('time_format');

                            $datetime_format = $date_format . ' ' . $time_format;
                            $horario_egreso = date_i18n($datetime_format, strtotime($egreso['horario_egreso']) - 3 * 3600);

                            echo esc_html($horario_egreso);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center">No hay datos disponibles</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <?php if ($total_pages > 1) : ?>
        <div class="pagination">
            <?php
            echo paginate_links(
                array(
                    'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                    'format' => '',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text' => __('&laquo; Anterior'),
                    'next_text' => __('Siguiente &raquo;'),
                )
            );
            ?>
        </div>
    <?php endif; ?>
</div>
