<?php
global $selected_estacionamiento;

// Obtener el número de página actual
$current_page = max(1, get_query_var('page', 1));

// Número de resultados por página
$per_page = 10;

// Calcular el offset
$offset = ($current_page - 1) * $per_page;



$table_egresos = $wpdb->prefix . 'parking_egresos';
$query = "SELECT * FROM $table_egresos";

// Obtener el estacionamiento seleccionado
if ($selected_estacionamiento) {
    $query .= $wpdb->prepare(" WHERE estacionamiento = %d", $selected_estacionamiento);
}

// Obtener la fecha seleccionada del formulario
$selected_fecha = isset($_GET['fecha']) ? sanitize_text_field($_GET['fecha']) : '';

if ($selected_fecha) {
    // Convertir la fecha de dd/mm/yyyy a yyyy-mm-dd
    $fecha_parts = explode('/', $selected_fecha);
    if (count($fecha_parts) == 3) {
        $fecha_convertida = $fecha_parts[2] . '-' . $fecha_parts[1] . '-' . $fecha_parts[0];
        
        // Convertir la fecha a un formato adecuado para comparar (inicio y fin del día)
        $fecha_inicio = $fecha_convertida . " 00:00:00";

 
        $fecha_fin = $fecha_convertida . " 23:59:59";
   
        if ($selected_estacionamiento) {
            // Si ya hay una condición de WHERE, agregar la condición de fecha con AND
            $query .= $wpdb->prepare(" AND horario_egreso BETWEEN %s AND %s", $fecha_inicio, $fecha_fin);
        } else {
            // Si no hay una condición de WHERE, agregar la condición de fecha directamente
            $query .= $wpdb->prepare(" WHERE horario_egreso BETWEEN %s AND %s", $fecha_inicio, $fecha_fin);
        }

     
    }
}

$query .= " ORDER BY id DESC";
$query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);

$egresos = $wpdb->get_results($query, ARRAY_A);





// Obtener el número total de registros
$count_query = "SELECT COUNT(*) FROM $table_egresos";
$where_conditions = array();

// Condición para el estacionamiento seleccionado
if ($selected_estacionamiento) {
    $where_conditions[] = $wpdb->prepare("estacionamiento = %d", $selected_estacionamiento);
}

// Condición para la fecha seleccionada
$selected_fecha = isset($_GET['fecha']) ? sanitize_text_field($_GET['fecha']) : '';

if ($selected_fecha) {
    // Convertir la fecha de dd/mm/yyyy a yyyy-mm-dd
    $fecha_parts = explode('/', $selected_fecha);
    if (count($fecha_parts) == 3) {
        $fecha_convertida = $fecha_parts[2] . '-' . $fecha_parts[1] . '-' . $fecha_parts[0];
        
        // Convertir la fecha a un formato adecuado para comparar (inicio y fin del día)
        $fecha_inicio = $fecha_convertida . " 00:00:00";
        $fecha_fin = $fecha_convertida . " 23:59:59";
        
        // Agregar la condición de fecha
        $where_conditions[] = $wpdb->prepare("horario_egreso BETWEEN %s AND %s", $fecha_inicio, $fecha_fin);
    }
}

// Combinar las condiciones en la cláusula WHERE
if (!empty($where_conditions)) {
    $count_query .= " WHERE " . implode(" AND ", $where_conditions);
}

// Obtener el número total de registros con las condiciones aplicadas
$total_e_items = $wpdb->get_var($count_query);

// Calcular el número total de páginas
$total_pages = ceil($total_e_items / $per_page);


$categorias = [
    0 => "Personal docente",
    1 => "Personal no docente",
    2 => "Estudiantes",
    3 => "Visitas"
];

$estacionamientos = [
    1 => "Bloque III",
    2 => "Bloque IV",
    3 => "Subsuelo y Rectorado",
    4 => "Chacabuco y Pedernera"
];

?>

<div class="flex justify-center">
    <div class="max-w-screen-2xl w-full">


        <div class="flex items-center justify-between pt-10 pb-2 px-2">


            <p class=" px-3 text-gray-500 dark:text-gray-400"> <strong
                    class="font-semibold text-gray-900 dark:text-white">Egresos</strong></p>

            <form method="GET" action="" class="flex gap-2 items-center">
                <div class="relative max-w-sm">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input id="datepicker-autohide" datepicker datepicker-autohide datepicker-format="dd/mm/yyyy" name="fecha" type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Seleccionar fecha...">
                </div>
                <button type="submit" class=" bg-blue-500 text-white px-4 py-2 rounded-lg">Buscar</button>
            </form>


        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="md:px-3 px-2 py-3">ID</th>
                        <th scope="col" class="md:px-3 px-2 py-3">Estacionamiento</th>
                        <th scope="col" class="md:px-3 px-2 py-3">Categoría</th>
                        <th scope="col" class="md:px-3 px-2 py-3">Horario de egreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($egresos)): ?>
                        <?php foreach ($egresos as $egreso): ?>
                            <tr
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?php echo esc_html($egreso['id']); ?>
                                </th>
                                <td class="px-3 py-4">
                                    <?php echo esc_html($estacionamientos[$egreso['estacionamiento']] ?? 'Desconocido'); ?>
                                </td>
                                <td class="px-3 py-4">
                                    <?php echo esc_html($categorias[$egreso['categoria']] ?? 'Desconocido'); ?>
                                </td>
                                <td class="px-3 py-4">
                                    <?php
                                    $date_format = get_option('date_format');
                                    $time_format = get_option('time_format');
                                    $datetime_format = $date_format . ' ' . $time_format;

                                    // Asumiendo que $egreso['horario_egreso'] está en formato 'Y-m-d H:i:s' en GMT
                                    $horario_egreso_local = date_i18n($datetime_format, strtotime($egreso['horario_egreso']));

                                    echo esc_html($horario_egreso_local);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center">No hay datos disponibles</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
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



        <div class="px-5 w-full bg-white rounded-lg shadow dark:bg-gray-800 py-24">
            <div class="flex justify-between p-4 md:p-6 pb-0 md:pb-0">
                <div>
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">Egresos hasta la
                        fecha</h5>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">Por día</p>
                </div>
                <div id="percentage-change"
                    class="flex items-center px-2.5 py-0.5 text-base font-semibold text-green-500 dark:text-green-500 text-center">
                </div>
            </div>
            <div id="labels-chart" class="px-2.5"></div>
            <div
                class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-5 p-4 md:p-6 pt-0 md:pt-0">
                <div class="flex justify-between items-center pt-5">
                    <form method="GET" action="">
                        <div id="date-range-picker" date-rangepicker datepicker-format="dd/mm/yyyy"
                            class="flex items-center">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <input id="datepicker-range-start" name="start" type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Seleccione fecha de inicio">
                            </div>
                            <span class="mx-4 text-gray-500"> a </span>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <input id="datepicker-range-end" name="end" type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Seleccione fecha de fin">
                            </div>
                            <button type="submit"
                                class="ml-4 mx-4 bg-blue-500 text-white px-4 py-2 rounded-lg">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



    </div>
</div>
</div>




</div>
</div>
<?php
global $wpdb;
$table_egresos = $wpdb->prefix . 'parking_egresos';

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Obtener los parámetros de la consulta si están disponibles
$start_date = isset($_GET['start']) ? $_GET['start'] : '';
$end_date = isset($_GET['end']) ? $_GET['end'] : '';
$selected_estacionamiento = isset($_GET['estacionamiento']) ? $_GET['estacionamiento'] : '';

// Convertir las fechas al formato adecuado
if (!empty($start_date)) {
    $start_date = DateTime::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
}
if (!empty($end_date)) {
    $end_date = DateTime::createFromFormat('d/m/Y', $end_date)->format('Y-m-d');
}

// Si no se establece un intervalo de fechas, mostrar los últimos 7 días desde la actualidad
if (empty($start_date) && empty($end_date)) {
    $end_date = date('Y-m-d'); // Hoy
    $start_date = date('Y-m-d', strtotime('-6 days')); // Hace 7 días
}

// Verificar si end_date está vacío y ajustar si es necesario
if (empty($end_date)) {
    // Obtener la última fecha registrada en la base de datos
    $latest_date_query = "SELECT MAX(DATE(horario_egreso)) AS latest_date FROM $table_egresos";
    if (!empty($selected_estacionamiento)) {
        $latest_date_query .= $wpdb->prepare(" WHERE estacionamiento = %d", $selected_estacionamiento);
    }
    $end_date = $wpdb->get_var($latest_date_query);
}

// Verificar si hay datos para la fecha proporcionada en $end_date
$data_check_query = "SELECT COUNT(*) FROM $table_egresos WHERE DATE(horario_egreso) = %s";
if (!empty($selected_estacionamiento)) {
    $data_check_query .= " AND estacionamiento = %d";
}
$data_check_query = $wpdb->prepare($data_check_query, $end_date, $selected_estacionamiento);
$data_exists = $wpdb->get_var($data_check_query);

// Si no hay datos para $end_date y el estacionamiento especificado, buscar la última fecha con datos antes de $end_date
if ($data_exists == 0) {
    $last_valid_date_query = "SELECT MAX(DATE(horario_egreso)) AS last_valid_date FROM $table_egresos WHERE DATE(horario_egreso) < %s";
    if (!empty($selected_estacionamiento)) {
        $last_valid_date_query .= " AND estacionamiento = %d";
    }
    $last_valid_date_query = $wpdb->prepare($last_valid_date_query, $end_date, $selected_estacionamiento);
    $end_date = $wpdb->get_var($last_valid_date_query);
}

// Consulta inicial
$query = "SELECT DATE(horario_egreso) as date, categoria, COUNT(*) as count FROM $table_egresos";

// Condiciones
$conditions = [];

// Añadir condición de estacionamiento si está definida
if (!empty($selected_estacionamiento)) {
    $conditions[] = $wpdb->prepare("estacionamiento = %d", $selected_estacionamiento);
}

// Añadir condición de rango de fechas
if (!empty($start_date) && !empty($end_date)) {
    $conditions[] = $wpdb->prepare("DATE(horario_egreso) >= %s AND DATE(horario_egreso) <= %s", $start_date, $end_date);
}

// Aplicar condiciones a la consulta
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Completar la consulta
$query .= " GROUP BY DATE(horario_egreso), categoria ORDER BY DATE(horario_egreso) ASC, categoria ASC";

// Ejecutar la consulta
$egresos = $wpdb->get_results($query, ARRAY_A);

// Procesar los datos para el gráfico
$categories = [];
$data = [];

foreach ($egresos as $row) {
    $date = $row['date'];
    $categoria = $row['categoria'];
    $count = $row['count'];

    if (!isset($data[$date])) {
        $data[$date] = [0, 0, 0, 0]; // Inicializar las 4 categorías
    }
    $data[$date][$categoria] = $count; // Asignar el conteo a la categoría correcta
}

$categories = array_keys($data);

// Inicializar series_data con arrays vacíos para cada categoría
$series_data = [
    array_fill(0, count($categories), 0), // Personal docente
    array_fill(0, count($categories), 0), // Personal no docente
    array_fill(0, count($categories), 0), // Estudiantes
    array_fill(0, count($categories), 0)  // Visitas
];

// Llenar series_data con los datos de $data
foreach ($data as $date => $counts) {
    $index = array_search($date, $categories);
    if ($index !== false) {
        foreach ($counts as $categoria => $count) {
            $series_data[$categoria][$index] = $count;
        }
    }
}

$total_data = [];
$length = count($series_data[0]);

for ($i = 0; $i < $length; $i++) {
    $sum = 0;
    foreach ($series_data as $data_set) {
        $sum += $data_set[$i];
    }
    $total_data[] = $sum;
}

// Calcular la fecha de ayer
$yesterday = date('Y-m-d', strtotime($end_date . ' -1 day'));

// Verificar si hay datos para $yesterday y ajustarlo si es necesario
$data_check_query = "SELECT COUNT(*) FROM $table_egresos WHERE DATE(horario_egreso) = %s";
if (!empty($selected_estacionamiento)) {
    $data_check_query .= " AND estacionamiento = %d";
}
$data_check_query = $wpdb->prepare($data_check_query, $yesterday, $selected_estacionamiento);
$data_exists_yesterday = $wpdb->get_var($data_check_query);

// Si no hay datos para $yesterday, buscar la última fecha con datos antes de $yesterday
if ($data_exists_yesterday == 0) {
    $last_valid_date_query_yesterday = "SELECT MAX(DATE(horario_egreso)) AS last_valid_date FROM $table_egresos WHERE DATE(horario_egreso) < %s";
    if (!empty($selected_estacionamiento)) {
        $last_valid_date_query_yesterday .= " AND estacionamiento = %d";
    }
    $last_valid_date_query_yesterday = $wpdb->prepare($last_valid_date_query_yesterday, $yesterday, $selected_estacionamiento);
    $yesterday = $wpdb->get_var($last_valid_date_query_yesterday);
}

// Calcular totales
$total_today = isset($data[$end_date]) ? array_sum($data[$end_date]) : 0;
$total_yesterday = isset($data[$yesterday]) ? array_sum($data[$yesterday]) : 0;

if ($total_yesterday > 0) {
    $percentage_change = (($total_today - $total_yesterday) / $total_yesterday) * 100;
} else {
    $percentage_change = 100; // Si no hay datos de ayer, consideramos que el cambio es del 100%
}

$percentage_change = number_format($percentage_change, 2);

?>
<script>
    const percentageChange = <?php echo $percentage_change; ?>;
    document.getElementById('percentage-change').textContent = percentageChange + '%';
</script>
<?php
$categories = array_map(function ($date) {
    $dateObj = new DateTime($date);
    return $dateObj->format('d/m/Y');
}, $categories);
?>
<script>

    // Actualizar el valor del porcentaje en el componente
    const percentageElement = document.getElementById('percentage-change');


    percentageElement.innerHTML = `${percentageChange}% <svg id="arrow-s" class="w-3 h-3 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4"/></svg>`;
    const arrowss = document.getElementById('arrow-s');
    // Cambiar el color del texto según el valor del porcentaje
    if (percentageChange < 0) {
        percentageElement.classList.remove('text-green-500');
        percentageElement.classList.add('text-red-500');
        arrowss.classList.add('rotate-180');
    } else {
        percentageElement.classList.remove('text-red-500');
        percentageElement.classList.add('text-green-500');
        arrowss.classList.remove('rotate-180');
    }




    document.addEventListener('DOMContentLoaded', function () {


        const options = {
            xaxis: {
                show: true,
                categories: <?php echo json_encode($categories); ?>,
                labels: {
                    show: true,
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                    }
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                show: true,
                labels: {
                    show: true,
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                    },
                    formatter: function (value) {
                        return value;
                    }
                }
            },
            series: [
                {
                    name: "Personal docente",
                    data: <?php echo json_encode($series_data[0]); ?>,
                    color: "#1A56DB",
                },
                {
                    name: "Personal no docente",
                    data: <?php echo json_encode($series_data[1]); ?>,
                    color: "#7E3BF2",
                },
                {
                    name: "Estudiantes",
                    data: <?php echo json_encode($series_data[2]); ?>,
                    color: "#00A56B",
                },
                {
                    name: "Visitas",
                    data: <?php echo json_encode($series_data[3]); ?>,
                    color: "#FF4560",
                },
                {
                    name: "Total",
                    data: <?php echo json_encode($total_data); ?>,
                    color: "#c3ecfd",
                },
            ],
            chart: {
                sparkline: {
                    enabled: false
                },
                height: "100%",
                width: "100%",
                type: "area",
                fontFamily: "Inter, sans-serif",
                dropShadow: {
                    enabled: false,
                },
                toolbar: {
                    show: false,
                },
            },
            tooltip: {
                enabled: true,
                x: {
                    show: false,
                },
            },
            fill: {
                type: "gradient",
                gradient: {
                    opacityFrom: 0.55,
                    opacityTo: 0,
                    shade: "#1C64F2",
                    gradientToColors: ["#1C64F2"],
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: 6,
            },
            legend: {
                show: false
            },
            grid: {
                show: false,
            },
        };

        if (document.getElementById("labels-chart") && typeof ApexCharts !== 'undefined') {
            const chart = new ApexCharts(document.getElementById("labels-chart"), options);
            chart.render();
        }
    });
</script>