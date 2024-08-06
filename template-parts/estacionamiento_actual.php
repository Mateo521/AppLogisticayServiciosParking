<?php
global $selected_estacionamiento;
global $selected_categoria;



$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_role = $current_user->roles[0];
global $wpdb;
$table_name = $wpdb->prefix . 'estacionamientos';
$estacionamiento = $wpdb->get_var($wpdb->prepare("SELECT estacionamiento FROM $table_name WHERE user_id = %d", $user_id));



if ($user_role === 'author') {
    $selected_estacionamiento = $estacionamiento;
    $selected_categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
} else {
    $selected_estacionamiento = isset($_GET['estacionamiento']) ? intval($_GET['estacionamiento']) : null;
    $selected_categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
}
$current_page = max(1, get_query_var('page', 1));
$per_page = 10;
$offset = ($current_page - 1) * $per_page;
global $wpdb;
$table_name = $wpdb->prefix . 'parking_ingresos';

$query = "SELECT * FROM $table_name";
$where_clauses = array();
$params = array();

if ($selected_estacionamiento !== null) {
    $where_clauses[] = "estacionamiento = %d";
    $params[] = $selected_estacionamiento;
}

if ($selected_categoria !== null) {
    $where_clauses[] = "categoria = %d";
    $params[] = $selected_categoria;
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY id DESC";
$params[] = $per_page;
$params[] = $offset;
$query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);

// Preparar la consulta con los parámetros
$prepared_query = $wpdb->prepare($query, ...$params);
$ingresos = $wpdb->get_results($prepared_query, ARRAY_A);

// Obtener el número total de registros
$count_query = "SELECT COUNT(*) FROM $table_name";
if (!empty($where_clauses)) {
    $count_query .= " WHERE " . implode(" AND ", $where_clauses);
}


$prepared_count_query = $wpdb->prepare($count_query, ...array_slice($params, 0, count($params) - 2));
$total_items = $wpdb->get_var($prepared_count_query);


function get_total_vehicles($selected_estacionamiento = null)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'parking_ingresos';

    // Iniciar la consulta base para contar el total de vehículos
    $count_query = "SELECT count(*) FROM $table_name";
    $conditions = array();

    if ($selected_estacionamiento) {
        $conditions[] = $wpdb->prepare("estacionamiento = %d", $selected_estacionamiento);
    }

    if (!empty($conditions)) {
        $count_query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Ejecutar la consulta y obtener el resultado
    $count = $wpdb->get_var($count_query);

    return $count;
}


function get_total_items_by_category_and_estacionamiento($selected_estacionamiento = null, $selected_categoria = null)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'parking_ingresos';

    // Iniciar la consulta base para contar elementos
    $count_query = "SELECT count(*) FROM $table_name";
    $conditions = array();

    // Agregar condiciones según los parámetros proporcionados
    if ($selected_estacionamiento !== null) {
        $conditions[] = $wpdb->prepare("estacionamiento = %d", $selected_estacionamiento);
    }

    if ($selected_categoria !== null) {
        $conditions[] = $wpdb->prepare("categoria = %d", $selected_categoria);
    }

    if (!empty($conditions)) {
        $count_query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Ejecutar la consulta y obtener el resultado
    $count = $wpdb->get_var($count_query);

    return $count;
}






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

    $total_count = $wpdb->get_var($total_count_query);

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


$current_datetime = new DateTime();
$yesterday = clone $current_datetime;
$yesterday->modify('-1 day');
$yesterday_end = $yesterday->format('Y-m-d') . ' 23:59:59';
$today_start = $current_datetime->format('Y-m-d') . ' 00:00:00';

// Verificar si hay vehículos ingresados en el estacionamiento antes del inicio del día actual
global $wpdb;
$table_name_ingresos = $wpdb->prefix . 'parking_ingresos';
$query = $wpdb->prepare(
    "SELECT COUNT(*) FROM $table_name_ingresos WHERE estacionamiento = %d AND horario_ingreso <= %s",
    $selected_estacionamiento,
    $yesterday_end
);
$vehicles_left_yesterday = $wpdb->get_var($query);


$show_modal = ($vehicles_left_yesterday > 0);



if ($show_modal && $selected_estacionamiento && get_total_vehicles($selected_estacionamiento) > 0) {
    echo "<script>
  
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('popup-modal');
        document.body.classList.add('overflow-hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); 
               modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');


         
    });
</script>";
}
$total_pages = ceil($total_items / $per_page);

$categorias = [
    0 => "Personal docente",
    1 => "Personal no docente",
    2 => "Alumnos",
    3 => "Visitas"
];


$estacionamientos = [
    1 => "Bloque III",
    2 => "Bloque IV",
    3 => "Subsuelo y Rectorado",
    4 => "Chacabuco y Pedernera"
];
$current_categoria = isset($categorias[$selected_categoria]) ? $categorias[$selected_categoria] : "No seleccionado";
$current_estacionamiento = isset($estacionamientos[$selected_estacionamiento]) ? $estacionamientos[$selected_estacionamiento] : "No seleccionado";
?>




<button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="block hidden text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
    Nota
</button>

<div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">No, cerrar</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Nota: todavía se encuentran
                    <?php echo get_total_vehicles($selected_estacionamiento); ?> vehículos en el estacionamiento actual
                    del día
                    anterior. Desea restablecer los datos de ingreso vehicular?
                </h3>
                <button id="confirm-delete" data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                    Si, estoy seguro
                </button>
                <button data-modal-hide="popup-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                    cerrar</button>
            </div>
        </div>
    </div>
</div>



<div class="flex justify-center">
    <div class="max-w-screen-2xl w-full">
        <div class="flex justify-between w-full px-5 py-3 items-center flex-wrap gap-3">
            <p id="current-estacionamiento" class="p-3 text-gray-500 dark:text-gray-400">
                Actual estacionamiento:
                <strong class="font-semibold text-gray-900 dark:text-white"><?php echo $current_estacionamiento; ?></strong>.
            </p>
            <p id="current-categoria" class="p-3 text-gray-500 dark:text-gray-400">
                Actual categoría:
                <strong class="font-semibold text-gray-900 dark:text-white"><?php echo $current_categoria; ?></strong>.
            </p>

    



                <button id="dropdownDefaultButton2" data-dropdown-toggle="dropdown2" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm h-max px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                    <?php echo $current_categoria; ?>
                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div id="dropdown2" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton2">
                        <li>
                            <a href="#" data-value="2" class="dropdown-item menu1 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Alumnos</a>
                        </li>
                        <li>
                            <a href="#" data-value="3" class="dropdown-item menu1 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Visitas</a>
                        </li>
                        <li>
                            <a href="#" data-value="0" class="dropdown-item menu1 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Personal
                                docente</a>
                        </li>
                        <li>
                            <a href="#" data-value="1" class="dropdown-item  menu1 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Personal
                                no docente</a>
                        </li>
                    </ul>
                </div>





                <?php if (current_user_can('editor') || current_user_can('administrator')) : ?>


                <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm h-max px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                    <?php echo $current_estacionamiento; ?> (admin)
                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                        <li>
                            <a href="#" data-value="1" class="dropdown-item menu0 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Bloque
                                III</a>
                        </li>
                        <li>
                            <a href="#" data-value="2" class="dropdown-item menu0 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Bloque
                                IV</a>
                        </li>
                        <li>
                            <a href="#" data-value="3" class="dropdown-item menu0 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Subsuelo
                                y Rectorado</a>
                        </li>
                        <li>
                            <a href="#" data-value="4" class="dropdown-item menu0 block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Chacabuco
                                y Pedernera</a>
                        </li>
                    </ul>
                </div>

            <?php elseif (current_user_can('author')) : ?>

                <!--div class="text-white bg-gray-700 font-medium rounded-lg text-sm h-max px-5 py-2.5 text-center inline-flex items-center">
                    Estacionamiento: <?php echo $current_estacionamiento; ?>
                </div-->

            <?php endif; ?>





        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="md:px-3 px-2 py-3">ID</th>
                        <th scope="col" class="md:px-3 px-2 py-3">Estacionamiento</th>
                        <th scope="col" class="md:px-3 px-2 py-3">Categoría</th>
                        <th scope="col" class="md:px-3 px-2 py-3">Horario de ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ingresos)) : ?>
                        <?php foreach ($ingresos as $ingreso) : ?>
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?php echo esc_html($ingreso['id']); ?>
                                </th>
                                <td class="px-3 py-4">
                                    <?php echo esc_html($estacionamientos[$ingreso['estacionamiento']] ?? 'Desconocido'); ?>
                                </td>
                                <td class="px-3 py-4">
                                    <?php echo esc_html($categorias[$ingreso['categoria']] ?? 'Desconocido'); ?>
                                </td>
                                <td class="px-3 py-4">
                                    <?php
                                    $date_format = get_option('date_format');
                                    $time_format = get_option('time_format');
                                    $datetime_format = $date_format . ' ' . $time_format;

                                    // Asumiendo que $egreso['horario_egreso'] está en formato 'Y-m-d H:i:s' en GMT
                                    $horario_ingreso_local = date_i18n($datetime_format, strtotime($ingreso['horario_ingreso']));

                                    echo esc_html($horario_ingreso_local);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center">No hay datos disponibles</td>
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
    </div>
</div>
<script>
    let ocupacion = 100;

    document.addEventListener('DOMContentLoaded', function() {

/*
        if (<?php echo json_encode(current_user_can('author')); ?>) {
        document.getElementById('dropdownDefaultButton').disabled = true;
    }

    */






        document.getElementById('confirm-delete').addEventListener('click', function() {
            var selectedEstacionamiento = <?php echo json_encode($selected_estacionamiento); ?>;

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'action': 'transfer_ingresos_to_egresos',
                        'selected_estacionamiento': selectedEstacionamiento
                    })
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Success:', data);
                    // Aquí puedes cerrar el modal o hacer cualquier otra cosa que necesites después de la transferencia
                    document.getElementById('popup-modal').classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    document.querySelector('.bg-gray-900/50').remove();
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });


























        const dropdownEstacionamientoItems = document.querySelectorAll('.menu0');
        const dropdownEstacionamientoButton = document.getElementById('dropdownDefaultButton');
        const currentEstacionamientoText = document.getElementById('current-estacionamiento');
        const estacionamientoInput = document.getElementById('estacionamiento_index');
        const selectedEstacionamiento = estacionamientoInput ? estacionamientoInput.value : null;

        const dropdownCategoriaItems = document.querySelectorAll('.menu1');
        const dropdownCategoriaButton = document.getElementById('dropdownDefaultButton2');
        const currentCategoriaText = document.getElementById('current-categoria');
        const categoriaInput = document.getElementById('categoria_index');
        const selectedCategoria = categoriaInput ? categoriaInput.value : null;

        // Recuperar ocupacion del local storage si existe
        if (localStorage.getItem('ocupacion')) {
            ocupacion = parseInt(localStorage.getItem('ocupacion'));

        }

        // Función para actualizar el texto del botón y el texto actual de estacionamiento
        function updateDropdownEstacionamiento(selectionText, selectionValue) {
            dropdownEstacionamientoButton.innerHTML = `${selectionText} (admin)
        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>`;
            currentEstacionamientoText.innerHTML = `Actual estacionamiento: <strong class="font-semibold text-gray-900 dark:text-white">${selectionText}</strong>.`;
        }

        // Función para actualizar el texto del botón y el texto actual de categoría
        function updateDropdownCategoria(selectionText, selectionValue) {
            dropdownCategoriaButton.innerHTML = `${selectionText} (admin)
        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>`;
            currentCategoriaText.innerHTML = `Actual categoría: <strong class="font-semibold text-gray-900 dark:text-white">${selectionText}</strong>.`;
        }

        // Inicializa el botón de estacionamiento con la opción seleccionada al cargar la página
        dropdownEstacionamientoItems.forEach(item => {
            if (item.getAttribute('data-value') === selectedEstacionamiento) {
                updateDropdownEstacionamiento(item.textContent.trim(), item.getAttribute('data-value'));
            }
        });

        // Inicializa el botón de categoría con la opción seleccionada al cargar la página
        dropdownCategoriaItems.forEach(item => {
            if (item.getAttribute('data-value') === selectedCategoria) {
                updateDropdownCategoria(item.textContent.trim(), item.getAttribute('data-value'));
            }
        });

        // Maneja la selección del dropdown de estacionamiento
        dropdownEstacionamientoItems.forEach(item => {
            item.addEventListener('click', function(e) {
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

                updateDropdownEstacionamiento(selectionText, selectionValue);
                console.log('Ocupacion actualizada:', ocupacion);


                // Redirigir a la primera página con el nuevo estacionamiento seleccionado
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('estacionamiento', selectionValue);
                currentUrl.searchParams.set('paged_ingresos', 1);
                window.location.href = currentUrl.toString();
            });
        });

        // Maneja la selección del dropdown de categoría
        dropdownCategoriaItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const selectionText = item.textContent.trim();
                const selectionValue = item.getAttribute('data-value');

                updateDropdownCategoria(selectionText, selectionValue);
                console.log('Categoría actualizada:', selectionValue);

                // Redirigir a la primera página con la nueva categoría seleccionada
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('categoria', selectionValue);
                currentUrl.searchParams.set('paged_egresos', 1);
                window.location.href = currentUrl.toString();
            });
        });



        // Renderiza el gráfico después de que la página esté completamente cargada
        if (document.getElementById("radial-chart") && typeof ApexCharts !== 'undefined') {
            const chart = new ApexCharts(document.querySelector("#radial-chart"), getChartOptions());
            chart.render();

            setInterval(function() {
                updateTimeComponent();
            }, 1000);


        }
    });

    function updateTimeComponent() {

        let now = new Date();
        let openingTime = new Date();
        openingTime.setHours(6, 0, 0, 0); // Hora de apertura


        let closingTime = new Date();
        closingTime.setHours(23, 59, 0, 0);

        let nowFormatted = now.toTimeString().split(' ')[0]; // Hora actual con segundos
        let closingTimeFormatted = closingTime.toTimeString().split(' ')[0].substring(0, 5); // Hora de cierre sin segundos
        let openingTimeFormatted = openingTime.toTimeString().split(' ')[0].substring(0, 5); // Hora de cierre sin segundos
        let timeInfo = document.getElementById("time-info");
        timeInfo.innerHTML = nowFormatted + " / " + openingTimeFormatted + " / " + closingTimeFormatted;
    }


    function getChartOptions() {




        let total = <?php echo $total_items ?>;

        let oc = document.getElementById("dispo");
        oc.innerHTML = total + "/" + ocupacion;


        let now = new Date(); // Hora actual
        let openingTime = new Date();
        openingTime.setHours(6, 0, 0, 0); // Hora de apertura

        let closingTime = new Date();
        closingTime.setHours(23, 59, 0, 0); // Hora de cierre


        let totalMinutes = (closingTime - openingTime) / (1000 * 60);

        let elapsedMinutes = (now - openingTime) / (1000 * 60);


        elapsedMinutes = Math.max(elapsedMinutes, 0);
        elapsedMinutes = Math.min(elapsedMinutes, totalMinutes);


        let closurePercentage = (elapsedMinutes / totalMinutes) * 100;

        closurePercentage = closurePercentage.toFixed(2);

        if (closurePercentage == 0) {

            closurePercentage = 100;
        }

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
                    formatter: function(value) {
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





























    <div class="text-center hidden">
        <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" type="button" data-drawer-target="drawer-swipe" data-drawer-show="drawer-swipe" data-drawer-placement="bottom" data-drawer-edge="true" data-drawer-edge-offset="bottom-[60px]" aria-controls="drawer-swipe">
            Datos
        </button>
    </div>

    <!-- drawer component -->
    <div id="drawer-swipe" class="fixed z-40 w-full overflow-y-auto bg-white border-t border-gray-200 rounded-t-lg dark:border-gray-700 dark:bg-gray-800 transition-transform bottom-0 left-0 right-0 translate-y-full bottom-[60px]" tabindex="-1" aria-labelledby="drawer-swipe-label">
        <div class="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" data-drawer-toggle="drawer-swipe">
            <span class="absolute w-8 h-1 -translate-x-1/2 bg-gray-300 rounded-lg top-3 left-1/2 dark:bg-gray-600"></span>
            <h5 id="drawer-swipe-label" class="inline-flex items-center text-base text-gray-500 dark:text-gray-400 font-medium"><svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                    <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10ZM17 13h-2v-2a1 1 0 0 0-2 0v2h-2a1 1 0 0 0 0 2h2v2a1 1 0 0 0 2 0v-2h2a1 1 0 0 0 0-2Z" />
                </svg>Datos</h5>
        </div>
        <div class="grid md:grid-cols-2 gap-4 p-4 grid-cols-1" style="height:70vh;overflow-y:scroll;">



            <div class="flex flex-col">




                <div class=" w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
                    <div class="flex justify-between mb-3">
                        <div class="flex items-center">
                            <div class="flex justify-center items-center">
                                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white pe-1">
                                    Estacionamiento actual </h5>
                                <svg data-popover-target="chart-info" data-popover-placement="bottom" class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm0 16a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Zm1-5.034V12a1 1 0 0 1-2 0v-1.418a1 1 0 0 1 1.038-.999 1.436 1.436 0 0 0 1.488-1.441 1.501 1.501 0 1 0-3-.116.986.986 0 0 1-1.037.961 1 1 0 0 1-.96-1.037A3.5 3.5 0 1 1 11 11.466Z" />
                                </svg>
                                <div data-popover id="chart-info" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                    <div class="p-3 space-y-2">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Total vehículos -
                                            Por categoría
                                        </h3>
                                        <p>



                                            <?php
                                            $total_count = get_total_items_by_category_and_estacionamiento($selected_estacionamiento, 0);
                                            echo "Personal docente: " . esc_html($total_count) . "<br>";
                                            $total_count = get_total_items_by_category_and_estacionamiento($selected_estacionamiento, 1);
                                            echo "Personal no docente: " . esc_html($total_count) . "<br>";
                                            $total_count = get_total_items_by_category_and_estacionamiento($selected_estacionamiento, 2);
                                            echo "Alumnos: " . esc_html($total_count) . "<br>";
                                            $total_count = get_total_items_by_category_and_estacionamiento($selected_estacionamiento, 3);
                                            echo "Visitas: " . esc_html($total_count);
                                            ?>







                                        </p>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Cálculo</h3>
                                        <p>Se basa unicamente por estacionamiento seleccionado en ingresos manuales o por
                                            escaneo QR</p>
                                        <a href="#" class="flex items-center font-medium text-blue-600 dark:text-blue-500 dark:hover:text-blue-600 hover:text-blue-700 hover:underline">La
                                            torta muestran los datos de ingreso. Si un vehículo egresa no se tomará en
                                            cuenta <svg class="w-2 h-2 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                                            </svg></a>
                                    </div>
                                    <div data-popper-arrow></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                        <div class="grid md:grid-cols-2 grid-cols-1 gap-3 mb-2">
                            <dl class="bg-orange-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                                <dt class="w-max h-8 px-2 rounded-full bg-orange-100 dark:bg-gray-500 text-orange-600 dark:text-orange-300 text-sm font-medium flex items-center justify-center mb-1" id="dispo">
                                </dt>
                                <dd class="text-orange-600 dark:text-orange-300 text-sm font-medium">Disponibilidad/Total
                                </dd>
                            </dl>
                            <dl id="time-component" class="bg-teal-50 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center h-[78px]">
                                <dt id="time-info" class=" h-8 rounded-full bg-teal-100 dark:bg-gray-500 text-teal-600 dark:text-teal-300 text-sm font-medium flex items-center justify-center mb-1  w-max px-2">
                                </dt>
                                <dd class="text-teal-600 dark:text-teal-300 text-sm font-medium">Hora actual / Hora apertura / Hora cierre
                                </dd>
                            </dl>
                        </div>
                        <button data-collapse-toggle="more-details" type="button" class="hover:underline text-xs text-gray-500 dark:text-gray-400 font-medium inline-flex items-center">Mostrar
                            mas detalles <svg class="w-2 h-2 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <div id="more-details" class="border-gray-200 border-t dark:border-gray-600 pt-3 mt-3 space-y-2 hidden">
                            <dl class="flex items-center justify-between">
                                <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal">Horario con mayor cantidad
                                    de vehículos en promedio:
                                </dt>
                                <dd class="bg-green-100 text-green-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-green-900 dark:text-green-300">
                                    <svg class="w-2.5 h-2.5 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                                    </svg> 12:00
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between">
                                <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal">Maxima capacidad:
                                </dt>
                                <dd class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-gray-600 dark:text-gray-300">
                                    hace 13 días</dd>
                            </dl>
                            <dl class="flex items-center justify-between">
                                <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal">Proxima apertura:</dt>
                                <dd class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded-md dark:bg-gray-600 dark:text-gray-300">
                                    Mañana</dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Radial Chart -->
                    <div class="py-6" id="radial-chart"></div>

                    <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                        <div class="flex justify-between items-center pt-5">

                            <a href="#" class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
                            <?php echo $current_estacionamiento; ?>
                                <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>



            </div>

            <div class="flex flex-col">

              


                <div class=" w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">

                    <div class="flex justify-between items-start w-full">
                        <div class="flex-col items-center">
                            <div class="flex items-center mb-1">
                                <?php
                                $total_vehicles = get_total_vehicles($selected_estacionamiento);
                                ?>

                                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white me-1">
                                    Total de vehículos: <?php echo esc_html($total_vehicles); ?>
                                </h5>
                                <svg data-popover-target="chart-info" data-popover-placement="bottom" class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm0 16a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Zm1-5.034V12a1 1 0 0 1-2 0v-1.418a1 1 0 0 1 1.038-.999 1.436 1.436 0 0 0 1.488-1.441 1.501 1.501 0 1 0-3-.116.986.986 0 0 1-1.037.961 1 1 0 0 1-.96-1.037A3.5 3.5 0 1 1 11 11.466Z" />
                                </svg>
                                <div data-popover id="chart-info" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                    <div class="p-3 space-y-2">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Activity growth -
                                            Incremental
                                        </h3>
                                        <p>Report helps navigate cumulative growth of community activities. Ideally, the
                                            chart
                                            should have a growing trend, as stagnating chart signifies a significant
                                            decrease of
                                            community activity.</p>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Calculation</h3>
                                        <p>For each date bucket, the all-time volume of activities is calculated. This means
                                            that activities in period n contain all activities up to period n, plus the
                                            activities generated by your community in period.</p>
                                        <a href="#" class="flex items-center font-medium text-blue-600 dark:text-blue-500 dark:hover:text-blue-600 hover:text-blue-700 hover:underline">Read
                                            more <svg class="w-2 h-2 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                                            </svg></a>
                                    </div>
                                    <div data-popper-arrow></div>
                                </div>
                            </div>


                        </div>
                        <div class="flex justify-end items-center">
                            <button id="widgetDropdownButton" data-dropdown-toggle="widgetDropdown" data-dropdown-placement="bottom" type="button" class="inline-flex items-center justify-center text-gray-500 w-8 h-8 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm"><svg class="w-3.5 h-3.5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                    <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                                </svg><span class="sr-only">Open dropdown</span>
                            </button>
                            <div id="widgetDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="widgetDropdownButton">
                                    <li>
                                        <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.418 17.861 1 20l2.139-6.418m4.279 4.279 10.7-10.7a3.027 3.027 0 0 0-2.14-5.165c-.802 0-1.571.319-2.139.886l-10.7 10.7m4.279 4.279-4.279-4.279m2.139 2.14 7.844-7.844m-1.426-2.853 4.279 4.279" />
                                            </svg>()
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                                                <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                            </svg>Descargar datos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5.953 7.467 6.094-2.612m.096 8.114L5.857 9.676m.305-1.192a2.581 2.581 0 1 1-5.162 0 2.581 2.581 0 0 1 5.162 0ZM17 3.84a2.581 2.581 0 1 1-5.162 0 2.581 2.581 0 0 1 5.162 0Zm0 10.322a2.581 2.581 0 1 1-5.162 0 2.581 2.581 0 0 1 5.162 0Z" />
                                            </svg>()
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"><svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                                <path d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                            </svg>()
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

                            <a href="#" class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
                            <?php echo $current_categoria; ?>
                                <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>



        </div>
    </div>















<?php





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
                    formatter: function(value) {
                        return value + "%"
                    },
                },
            },
            xaxis: {
                labels: {
                    formatter: function(value) {
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