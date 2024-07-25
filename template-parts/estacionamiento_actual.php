<?php
// Obtener el valor del estacionamiento seleccionado, si existe
global $selected_estacionamiento;

$selected_estacionamiento = isset($_GET['estacionamiento']) ? intval($_GET['estacionamiento']) : null;

// Obtener el número de página actual
$current_page = max(1, get_query_var('page', 1));

// Número de resultados por página
$per_page = 10;

// Calcular el offset
$offset = ($current_page - 1) * $per_page;

// Ajustar la consulta según el estacionamiento seleccionado
global $wpdb;
$table_name = $wpdb->prefix . 'parking_ingresos';

$query = "SELECT * FROM $table_name";
if ($selected_estacionamiento) {
    $query .= $wpdb->prepare(" WHERE estacionamiento = %d", $selected_estacionamiento);
}
$query .= " ORDER BY id DESC";
$query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);
$ingresos = $wpdb->get_results($query, ARRAY_A);

// Obtener el número total de registros
$count_query = "SELECT COUNT(*) FROM $table_name";
if ($selected_estacionamiento) {
    $count_query .= $wpdb->prepare(" WHERE estacionamiento = %d", $selected_estacionamiento);
}
$total_items = $wpdb->get_var($count_query);





function get_total_items($selected_estacionamiento = null, $selected_categoria = null)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'parking_ingresos';

    // Iniciar la consulta base para contar el total de elementos
    $total_count_query = "SELECT count(*) FROM $table_name";
    $total_conditions = array();

    // Agregar condiciones según los parámetros proporcionados
    if ($selected_estacionamiento) {
        $total_conditions[] = $wpdb->prepare("estacionamiento = %d", $selected_estacionamiento);
    }

    if (!empty($total_conditions)) {
        $total_count_query .= " WHERE " . implode(" AND ", $total_conditions);
    }

    // Ejecutar la consulta y obtener el resultado
    $total_count = $wpdb->get_var($total_count_query);

    // Iniciar la consulta base para contar elementos de la categoría seleccionada
    $count_query = "SELECT count(*) FROM $table_name";
    $conditions = $total_conditions; // Usar las mismas condiciones del total

    if ($selected_categoria !== null) {
        $conditions[] = $wpdb->prepare("categoria = %d", $selected_categoria);
    }

    if (!empty($conditions)) {
        $count_query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Ejecutar la consulta y obtener el resultado
    $count = $wpdb->get_var($count_query);

    // Calcular el porcentaje
    if ($total_count > 0) {
        return ($count * 100) / $total_count;
    } else {
        return 0;
    }
}





// Calcular el número total de páginas
$total_pages = ceil($total_items / $per_page);

// Definir nombres de estacionamientos
$estacionamientos = [
    1 => "Bloque III",
    2 => "Bloque IV",
    3 => "Subsuelo y Rectorado",
    4 => "Chacabuco y Pedernera"
];

$current_estacionamiento = isset($estacionamientos[$selected_estacionamiento]) ? $estacionamientos[$selected_estacionamiento] : "No seleccionado";
?>

<div class="flex justify-between w-full px-5 py-3 items-center">
    <p id="current-estacionamiento" class="p-3 text-gray-500 dark:text-gray-400">
        Actual estacionamiento:
        <strong class="font-semibold text-gray-900 dark:text-white"><?php echo $current_estacionamiento; ?></strong>.
    </p>

    <?php if (current_user_can('editor') || current_user_can('administrator')): ?>
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm h-max px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">
            <?php echo $current_estacionamiento; ?> (admin)
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4" />
            </svg>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                <li>
                    <a href="#" data-value="1"
                        class="dropdown-item block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Bloque
                        III</a>
                </li>
                <li>
                    <a href="#" data-value="2"
                        class="dropdown-item block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Bloque
                        IV</a>
                </li>
                <li>
                    <a href="#" data-value="3"
                        class="dropdown-item block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Subsuelo
                        y Rectorado</a>
                </li>
                <li>
                    <a href="#" data-value="4"
                        class="dropdown-item block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Chacabuco
                        y Pedernera</a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</div> 

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="md:px-6 px-2 py-3">ID</th>
                <th scope="col" class="md:px-6 px-2 py-3">Estacionamiento</th>
                <th scope="col" class="md:px-6 px-2 py-3">Categoría</th>
                <th scope="col" class="md:px-6 px-2 py-3">Horario de ingreso</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ingresos)): ?>
                <?php foreach ($ingresos as $ingreso): ?>
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <?php echo esc_html($ingreso['id']); ?>
                        </th>
                        <td class="px-6 py-4">
                            <?php echo esc_html($ingreso['estacionamiento']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo esc_html($ingreso['categoria']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $date_format = get_option('date_format');
                            $time_format = get_option('time_format');

                            $datetime_format = $date_format . ' ' . $time_format;
                            $horario_ingreso = date_i18n($datetime_format, strtotime($ingreso['horario_ingreso']) - 3 * 3600);

                            echo esc_html($horario_ingreso);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center">No hay datos disponibles</td>
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
<script>
    let ocupacion = 100;

    document.addEventListener('DOMContentLoaded', function () {
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        const dropdownButton = document.getElementById('dropdownDefaultButton');
        const currentEstacionamientoText = document.getElementById('current-estacionamiento');
        const estacionamientoInput = document.getElementById('estacionamiento_index');
        const selectedEstacionamiento = estacionamientoInput ? estacionamientoInput.value : null;

        // Recuperar ocupacion del local storage si existe
        if (localStorage.getItem('ocupacion')) {
            ocupacion = parseInt(localStorage.getItem('ocupacion'));
        }

        // Función para actualizar el texto del botón y el texto actual de estacionamiento
        function updateDropdown(selectionText, selectionValue) {
            dropdownButton.innerHTML = `${selectionText} (admin)
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>`;
            currentEstacionamientoText.innerHTML = `Actual estacionamiento: <strong class="font-semibold text-gray-900 dark:text-white">${selectionText}</strong>.`;
        }

        // Inicializa el botón con la opción seleccionada al cargar la página
        dropdownItems.forEach(item => {
            if (item.getAttribute('data-value') === selectedEstacionamiento) {
                updateDropdown(item.textContent.trim(), item.getAttribute('data-value'));
            }
        });

        // Maneja la selección del dropdown
        dropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const selectionText = item.textContent.trim();
                const selectionValue = item.getAttribute('data-value');
                /*
                1 Bloque III
                2 Bloque IV
                3 Subsuelo y Rectorado
                4 Chacabuco y Pedernera
                */
                switch (parseInt(selectionValue)) {
                    case 1:
                        ocupacion = 55;
                        break;
                    case 2:
                        ocupacion = 60;
                        break;
                    case 3:
                        ocupacion = 76;
                        break;
                    case 4:
                        ocupacion = 43;
                        break;
                    default:
                        ocupacion = 1;
                        break;
                }

                // Guardar ocupacion en el local storage
                localStorage.setItem('ocupacion', ocupacion);

                updateDropdown(selectionText, selectionValue);
                console.log('Ocupacion actualizada:', ocupacion);

                // Redirigir a la primera página con el nuevo estacionamiento seleccionado
                window.location.href = `<?php echo home_url('/'); ?>?estacionamiento=${selectionValue}&paged=1`;
            });
        });

        // Renderiza el gráfico después de que la página esté completamente cargada
        if (document.getElementById("radial-chart") && typeof ApexCharts !== 'undefined') {
            const chart = new ApexCharts(document.querySelector("#radial-chart"), getChartOptions());
            chart.render();
        }
    });

    function getChartOptions() {
        console.log('Ocupacion dentro de getChartOptions:', ocupacion);
        let total = <?php echo $total_items ?>;


        // Calcular el porcentaje de cierre basado en la hora actual
        let now = new Date();
        let openingTime = new Date();
        openingTime.setHours(6, 0, 0, 0); // Estacionamiento abre a las 7:00

        let closingTime = new Date();
        closingTime.setHours(23, 59, 0, 0); // Estacionamiento cierra a las 23:59

        let totalMinutes = (closingTime - openingTime) / (1000 * 60); // Total de minutos desde las 7:00 hasta las 19:00
        let elapsedMinutes = (now - openingTime) / (1000 * 60); // Minutos transcurridos desde las 7:00 hasta ahora

        elapsedMinutes = Math.max(elapsedMinutes, 0); // Asegurarse de que no sea negativo
        elapsedMinutes = Math.min(elapsedMinutes, totalMinutes); // Asegurarse de que no exceda los minutos totales

        let closurePercentage = (elapsedMinutes / totalMinutes) * 100;

        closurePercentage = closurePercentage.toFixed(2);

        let percentage = (parseInt(total) / ocupacion) * 100;
        percentage = percentage.toFixed(2);
        percentage = parseFloat(percentage)
        return {

            /*Disponibilidad - Cierre (restablecimiento de datos)*/


            series: [percentage, closurePercentage],
            colors: ["#1C64F2", "#16BDCA"],
            chart: {
                height: "380px",
                width: "100%",
                type: "radialBar",
                sparkline: {
                    enabled: true,
                },
            },
            plotOptions: {
                radialBar: {
                    track: {
                        background: '#E5E7EB',
                    },
                    dataLabels: {
                        show: false,
                    },
                    hollow: {
                        margin: 0,
                        size: "32%",
                    }
                },
            },
            grid: {
                show: false,
                strokeDashArray: 4,
                padding: {
                    left: 2,
                    right: 2,
                    top: -23,
                    bottom: -20,
                },
            },
            labels: ["Disponibilidad", "Cierre"],
            legend: {
                show: true,
                position: "bottom",
                fontFamily: "Inter, sans-serif",
            },
            tooltip: {
                enabled: true,
                x: {
                    show: false,
                },
            },
            yaxis: {
                show: false,
                labels: {
                    formatter: function (value) {
                        return value + '%';
                    }
                }
            }
        };
    }
</script>







<style>
    .pagination {
        display: flex;
        justify-content: center;
        padding: 20px 0;
    }

    .pagination a {
        color: #0073aa;
        padding: 5px 10px;
        text-decoration: none;
    }

    .pagination a:hover {
        color: #005177;
    }

    .pagination .current {
        color: #ffffff;
        background-color: #0073aa;
        padding: 5px 10px;
    }
</style>














<?php
if (current_user_can('editor') || current_user_can('administrator')) {
    ?>



    <div class="p-5  grid md:grid-cols-2 grid-cols-1 md:gap-10 gap-2 justify-between">
        <div class="flex flex-col">

            <p> Estacionamiento actual (admin)</p>




            <div class=" w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
                <div class="flex justify-between mb-3">
                    <div class="flex items-center">
                        <div class="flex justify-center items-center">
                            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white pe-1">Your team's
                                progress</h5>
                            <svg data-popover-target="chart-info" data-popover-placement="bottom"
                                class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer ms-1"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm0 16a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Zm1-5.034V12a1 1 0 0 1-2 0v-1.418a1 1 0 0 1 1.038-.999 1.436 1.436 0 0 0 1.488-1.441 1.501 1.501 0 1 0-3-.116.986.986 0 0 1-1.037.961 1 1 0 0 1-.96-1.037A3.5 3.5 0 1 1 11 11.466Z" />
                            </svg>
                            <div data-popover id="chart-info" role="tooltip"
                                class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                <div class="p-3 space-y-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Activity growth - Incremental
                                    </h3>
                                    <p>Report helps navigate cumulative growth of community activities. Ideally, the chart
                                        should have a growing trend, as stagnating chart signifies a significant decrease of
                                        community activity.</p>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Calculation</h3>
                                    <p>For each date bucket, the all-time volume of activities is calculated. This means
                                        that activities in period n contain all activities up to period n, plus the
                                        activities generated by your community in period.</p>
                                    <a href="#"
                                        class="flex items-center font-medium text-blue-600 dark:text-blue-500 dark:hover:text-blue-600 hover:text-blue-700 hover:underline">Read
                                        more <svg class="w-2 h-2 ms-1.5 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 9 4-4-4-4" />
                                        </svg></a>
                                </div>
                                <div data-popper-arrow></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                    <div class="grid grid-cols-3 gap-3 mb-2">
                        <dl
                            class="bg-orange-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                            <dt
                                class="w-8 h-8 rounded-full bg-orange-100 dark:bg-gray-500 text-orange-600 dark:text-orange-300 text-sm font-medium flex items-center justify-center mb-1">
                                12</dt>
                            <dd class="text-orange-600 dark:text-orange-300 text-sm font-medium">To do</dd>
                        </dl>
                        <dl
                            class="bg-teal-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                            <dt
                                class="w-8 h-8 rounded-full bg-teal-100 dark:bg-gray-500 text-teal-600 dark:text-teal-300 text-sm font-medium flex items-center justify-center mb-1">
                                23</dt>
                            <dd class="text-teal-600 dark:text-teal-300 text-sm font-medium">In progress</dd>
                        </dl>
                        <dl
                            class="bg-blue-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                            <dt
                                class="w-8 h-8 rounded-full bg-blue-100 dark:bg-gray-500 text-blue-600 dark:text-blue-300 text-sm font-medium flex items-center justify-center mb-1">
                                64</dt>
                            <dd class="text-blue-600 dark:text-blue-300 text-sm font-medium">Done</dd>
                        </dl>
                    </div>
                    <button data-collapse-toggle="more-details" type="button"
                        class="hover:underline text-xs text-gray-500 dark:text-gray-400 font-medium inline-flex items-center">Show
                        more details <svg class="w-2 h-2 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <div id="more-details" class="border-gray-200 border-t dark:border-gray-600 pt-3 mt-3 space-y-2 hidden">
                        <dl class="flex items-center justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal">Average task completion rate:
                            </dt>
                            <dd
                                class="bg-green-100 text-green-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-green-900 dark:text-green-300">
                                <svg class="w-2.5 h-2.5 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                                </svg> 57%
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal">Days until sprint ends:</dt>
                            <dd
                                class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-gray-600 dark:text-gray-300">
                                13 days</dd>
                        </dl>
                        <dl class="flex items-center justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal">Next meeting:</dt>
                            <dd
                                class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-gray-600 dark:text-gray-300">
                                Thursday</dd>
                        </dl>
                    </div>
                </div>

                <!-- Radial Chart -->
                <div class="py-6" id="radial-chart"></div>

                <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                    <div class="flex justify-between items-center pt-5">
                        <!-- Button -->
                        <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown"
                            data-dropdown-placement="bottom"
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white"
                            type="button">
                            Last 7 days
                            <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <div id="lastDaysdropdown"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton">
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Yesterday</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Today</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                                        7 days</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                                        30 days</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                                        90 days</a>
                                </li>
                            </ul>
                        </div>
                        <a href="#"
                            class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
                            Progress report
                            <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 9 4-4-4-4" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>



        </div>

        <div class="flex flex-col">

            <p>Categorías actuales</p>


            <div class=" w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">

                <div class="flex justify-between items-start w-full">
                    <div class="flex-col items-center">
                        <div class="flex items-center mb-1">
                            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white me-1">Website traffic
                            </h5>
                            <svg data-popover-target="chart-info" data-popover-placement="bottom"
                                class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer ms-1"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm0 16a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Zm1-5.034V12a1 1 0 0 1-2 0v-1.418a1 1 0 0 1 1.038-.999 1.436 1.436 0 0 0 1.488-1.441 1.501 1.501 0 1 0-3-.116.986.986 0 0 1-1.037.961 1 1 0 0 1-.96-1.037A3.5 3.5 0 1 1 11 11.466Z" />
                            </svg>
                            <div data-popover id="chart-info" role="tooltip"
                                class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                <div class="p-3 space-y-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Activity growth - Incremental
                                    </h3>
                                    <p>Report helps navigate cumulative growth of community activities. Ideally, the chart
                                        should have a growing trend, as stagnating chart signifies a significant decrease of
                                        community activity.</p>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Calculation</h3>
                                    <p>For each date bucket, the all-time volume of activities is calculated. This means
                                        that activities in period n contain all activities up to period n, plus the
                                        activities generated by your community in period.</p>
                                    <a href="#"
                                        class="flex items-center font-medium text-blue-600 dark:text-blue-500 dark:hover:text-blue-600 hover:text-blue-700 hover:underline">Read
                                        more <svg class="w-2 h-2 ms-1.5 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 9 4-4-4-4" />
                                        </svg></a>
                                </div>
                                <div data-popper-arrow></div>
                            </div>
                        </div>
                        <button id="dateRangeButton" data-dropdown-toggle="dateRangeDropdown"
                            data-dropdown-ignore-click-outside-class="datepicker" type="button"
                            class="inline-flex items-center text-blue-700 dark:text-blue-600 font-medium hover:underline">31
                            Nov - 31 Dev <svg class="w-3 h-3 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <div id="dateRangeDropdown"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-80 lg:w-96 dark:bg-gray-700 dark:divide-gray-600">
                            <div class="p-3" aria-labelledby="dateRangeButton">
                                <div date-rangepicker datepicker-autohide class="flex items-center">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input name="start" type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Start date">
                                    </div>
                                    <span class="mx-2 text-gray-500 dark:text-gray-400">to</span>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input name="end" type="text"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="End date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center">
                        <button id="widgetDropdownButton" data-dropdown-toggle="widgetDropdown"
                            data-dropdown-placement="bottom" type="button"
                            class="inline-flex items-center justify-center text-gray-500 w-8 h-8 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm"><svg
                                class="w-3.5 h-3.5 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                <path
                                    d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                            </svg><span class="sr-only">Open dropdown</span>
                        </button>
                        <div id="widgetDropdown"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="widgetDropdownButton">
                                <li>
                                    <a href="#"
                                        class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg
                                            class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 21 21">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7.418 17.861 1 20l2.139-6.418m4.279 4.279 10.7-10.7a3.027 3.027 0 0 0-2.14-5.165c-.802 0-1.571.319-2.139.886l-10.7 10.7m4.279 4.279-4.279-4.279m2.139 2.14 7.844-7.844m-1.426-2.853 4.279 4.279" />
                                        </svg>Edit widget
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg
                                            class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                                            <path
                                                d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                        </svg>Download data
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg
                                            class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m5.953 7.467 6.094-2.612m.096 8.114L5.857 9.676m.305-1.192a2.581 2.581 0 1 1-5.162 0 2.581 2.581 0 0 1 5.162 0ZM17 3.84a2.581 2.581 0 1 1-5.162 0 2.581 2.581 0 0 1 5.162 0Zm0 10.322a2.581 2.581 0 1 1-5.162 0 2.581 2.581 0 0 1 5.162 0Z" />
                                        </svg>Add to repository
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg
                                            class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>Delete widget
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Line Chart -->
                <div class="py-6" id="pie-chart"></div>

                <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                    <div class="flex justify-between items-center pt-5">
                        <!-- Button -->
                        <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown"
                            data-dropdown-placement="bottom"
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white"
                            type="button">
                            Last 7 days
                            <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <div id="lastDaysdropdown"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton">
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Yesterday</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Today</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                                        7 days</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                                        30 days</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                                        90 days</a>
                                </li>
                            </ul>
                        </div>
                        <a href="#"
                            class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
                            Traffic analysis
                            <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 9 4-4-4-4" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>




    <?php

}




global $wpdb;
$table_name = $wpdb->prefix . 'parking_ingresos';

// Consulta para contar las categorías
$query = "
    SELECT categoria, COUNT(*) as count
    FROM $table_name
    WHERE estacionamiento = %d
    GROUP BY categoria
";
$prepared_query = $wpdb->prepare($query, $selected_estacionamiento);
$category_counts = $wpdb->get_results($prepared_query, ARRAY_A);







?>

<script>

    <?php
    // Tu arreglo original
    $category_items = array(
        get_total_items($selected_estacionamiento, 0),
        get_total_items($selected_estacionamiento, 1),
        get_total_items($selected_estacionamiento, 2),
        get_total_items($selected_estacionamiento, 3),
    );

    // Número de decimales deseado
    $decimals = 1;

    // Formatear cada elemento a decimal, pero mantenerlo como número
    $formatted_items = array_map(function ($item) use ($decimals) {
        return round($item, $decimals); // Usar round para mantener como número
    }, $category_items);

    // Convertir el arreglo a una cadena JSON
    $json_items = json_encode($formatted_items);
    ?>

    const categoryItems = <?php echo $json_items; ?>;

    console.log(categoryItems);

    const getChartOptions2 = () => {
        return {
            series: categoryItems,
            colors: ["#1C64F2", "#16BDCA", "#9061F9", "#EEC85C"],
            chart: {
                height: 420,
                width: "100%",
                type: "pie",
            },
            stroke: {
                colors: ["white"],
                lineCap: "",
            },
            plotOptions: {
                pie: {
                    labels: {
                        show: true,
                    },
                    size: "100%",
                    dataLabels: {
                        offset: -25
                    }
                },
            },
            labels: ["Personal docente", "Personal no docente", "Alumnos", "Visitas"],
            dataLabels: {
                enabled: true,
                style: {
                    fontFamily: "Inter, sans-serif",
                },
            },
            legend: {
                position: "bottom",
                fontFamily: "Inter, sans-serif",
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return value + "%"
                    },
                },
            },
            xaxis: {
                labels: {
                    formatter: function (value) {
                        return value + "%"
                    },
                },
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
            },
        }
    }

    if (document.getElementById("pie-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("pie-chart"), getChartOptions2());
        chart.render();
    }


</script>