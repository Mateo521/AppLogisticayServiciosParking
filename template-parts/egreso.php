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


$total_pages = ceil($total_e_items / $per_page);
?>
 

<p class="mb-3 px-3 pt-12 text-gray-500 dark:text-gray-400"> <strong class="font-semibold text-gray-900 dark:text-white">Egresos</strong>.</p>


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="md:px-6 px-2 py-3">ID</th>
                <th scope="col" class="md:px-6 px-2 py-3">Estacionamiento</th>
                <th scope="col" class="md:px-6 px-2 py-3">Categoría</th>
                <th scope="col" class="md:px-6 px-2 py-3">Horario de egreso</th>
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



<div class="p-5 w-full bg-white rounded-lg shadow dark:bg-gray-800">
  <div class="flex justify-between p-4 md:p-6 pb-0 md:pb-0">
    <div>
      <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">$12,423</h5>
      <p class="text-base font-normal text-gray-500 dark:text-gray-400">Sales this week</p>
    </div>
    <div class="flex items-center px-2.5 py-0.5 text-base font-semibold text-green-500 dark:text-green-500 text-center">
      23%
      <svg class="w-3 h-3 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4"/>
      </svg>
    </div>
  </div>
  <div id="labels-chart" class="px-2.5"></div>
  <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-5 p-4 md:p-6 pt-0 md:pt-0">
    <div class="flex justify-between items-center pt-5">
      <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown" data-dropdown-placement="bottom" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white" type="button">
        Last 7 days
        <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </button>
      <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
          <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Yesterday</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Today</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 7 days</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 30 days</a></li>
          <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 90 days</a></li>
        </ul>
      </div>
      <a href="#" class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
        Sales Report
        <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
      </a>
    </div>
  </div>
</div>





<?php
global $wpdb;
$table_egresos = $wpdb->prefix . 'parking_egresos';

// Query para obtener los datos
$query = "SELECT DATE(horario_egreso) as date, categoria, COUNT(*) as count FROM $table_egresos";

if ($selected_estacionamiento) {
    $query .= $wpdb->prepare(" WHERE estacionamiento = %d", $selected_estacionamiento);
}
$query .= " GROUP BY DATE(horario_egreso), categoria ORDER BY DATE(horario_egreso) ASC, categoria ASC";



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
$series_data = array_map(null, ...array_values($data));

?>



<script>
   
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
      name: "Alumnos",
      data: <?php echo json_encode($series_data[2]); ?>,
      color: "#00A56B",
    },
    {
      name: "Visitas",
      data: <?php echo json_encode($series_data[3]); ?>,
      color: "#FF4560",
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
}

if (document.getElementById("labels-chart") && typeof ApexCharts !== 'undefined') {
  const chart = new ApexCharts(document.getElementById("labels-chart"), options);
  chart.render();
}
</script>