<?php
if (!session_id()) {
    session_start();
}

get_header();

$current_url = home_url('/manual'); // o home_url('/leerqr'), dependiendo de la página actual







$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_role = $current_user->roles[0];
global $wpdb;
$table_name = $wpdb->prefix . 'estacionamientos';
$estacionamiento = $wpdb->get_var($wpdb->prepare("SELECT estacionamiento FROM $table_name WHERE user_id = %d", $user_id));














?>




<?php

if (isset($_SESSION['message'])):
    // Determinar el tipo de mensaje (éxito o fracaso)
    $message_type = strpos($_SESSION['message'], 'éxito') !== false ? 'success' : 'error';

    // Definir clases CSS según el tipo de mensaje
    $alert_classes = $message_type === 'success' ? 'text-green-800 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800' : 'text-red-800 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800';
    $icon_color = $message_type === 'success' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400';
    $button_hover_bg = $message_type === 'success' ? 'hover:bg-green-200 dark:hover:bg-gray-700' : 'hover:bg-red-200 dark:hover:bg-gray-700';
    ?>
    <div id="alert-border-3" class="flex items-center px-4 py-2 mb-4 border-t-4 <?php echo $alert_classes; ?>" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <div class="ms-3 text-sm font-medium">
            <?php echo $_SESSION['message']; ?>
        </div>
        <button type="button"
            class="ms-auto -mx-1.5 -my-1.5 bg-green-50 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 <?php echo $icon_color; ?> <?php echo $button_hover_bg; ?> inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:hover:bg-gray-700"
            data-dismiss-target="#alert-border-3" aria-label="Close">
            <span class="sr-only">Cerrar</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>




<div class="flex justify-center p-3">
    <div class="py-5">
        <a href="#"
            class="flex my-5 flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-screen-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
            <img class="object-cover w-full rounded-t-lg h-full md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                src="https://images.pexels.com/photos/2220292/pexels-photo-2220292.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                alt="" style="height:200px;">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Ingreso vehicular</h5>
                <form class="max-w-sm mx-auto py-2" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
    <input type="hidden" name="action" value="insert_ingreso">
    <input type="hidden" name="redirect_url" value="<?php echo esc_url($current_url); ?>">
    <div class="flex relative">
        <?php
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $user_role = $current_user->roles[0];

        global $wpdb;
        $table_name = $wpdb->prefix . 'estacionamientos';
        $estacionamiento = $wpdb->get_var($wpdb->prepare("SELECT estacionamiento FROM $table_name WHERE user_id = %d", $user_id));


        if ($user_role === 'author') {
            $selected_estacionamiento = $estacionamiento;
        } else {
            $selected_estacionamiento = isset($_GET['estacionamiento']) ? intval($_GET['estacionamiento']) : null;
        }
        ?>

        <?php if ($user_role != 'author'): ?>
            <button id="estacionamiento-button" data-dropdown-toggle="dropdown-estacionamiento"
                class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-500 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600"
                type="button">
                <?php echo get_estacionamiento_name($selected_estacionamiento); ?> <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <div id="dropdown-estacionamiento"
                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 absolute">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                    aria-labelledby="estacionamiento-button">
                    <li><button type="button"
                            class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                            data-value="1">Bloque III</button></li>
                    <li><button type="button"
                            class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                            data-value="2">Bloque IV</button></li>
                    <li><button type="button"
                            class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                            data-value="3">Subsuelo y Rectorado</button></li>
                    <li><button type="button"
                            class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                            data-value="4">Chacabuco y Pedernera</button></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="text-white bg-gray-700 font-medium rounded-lg text-sm h-max px-5 py-2.5 text-center inline-flex items-center">
                Estacionamiento: <?php echo get_estacionamiento_name($selected_estacionamiento); ?>
                <input type="hidden" id="estacionamiento-index" name="estacionamiento_index" value="<?php echo $selected_estacionamiento; ?>">
            </div>
        <?php endif; ?>

        <label for="categoria_index" class="sr-only">Categoría</label>
        <select id="categoria_index" name="categoria_index"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-e-lg border-s-gray-100 dark:border-s-gray-700 border-s-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="1" selected>Personal no docente</option>
            <option value="0">Personal docente</option>
            <option value="3">Visitas</option>
            <option value="2">Alumnos</option>
        </select>

        <button type="submit" class="px-2 py-2 bg-blue-600 text-white rounded-lg mx-2">Ingresar</button>
    </div>
</form>

<?php
function get_estacionamiento_name($estacionamiento_id) {
    switch ($estacionamiento_id) {
        case 1: return 'Bloque III';
        case 2: return 'Bloque IV';
        case 3: return 'Subsuelo y Rectorado';
        case 4: return 'Chacabuco y Pedernera';
        default: return 'N/A';
    }
}
?>
            </div>
        </a>


        <a href="#"
            class="flex  my-5 flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-screen-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
            <img class="object-cover w-full rounded-t-lg h-full md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                src="https://images.pexels.com/photos/1000633/pexels-photo-1000633.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                alt="" style="height:200px;">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Egreso vehicular</h5>

                <form id="form2" class="flex items-center max-w-sm mx-auto py-2" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" id="categoria_index2" name="categoria_index2" value="">
    <input type="hidden" id="estacionamiento_index2" name="estacionamiento_index2" value="">
    <input type="hidden" name="action" value="delete_oldest_ingreso">
    <input type="hidden" name="redirect_url2" value="<?php echo esc_url($current_url); ?>">

    <?php
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $user_role = $current_user->roles[0];

    global $wpdb;
    $table_name = $wpdb->prefix . 'estacionamientos';
    $estacionamiento = $wpdb->get_var($wpdb->prepare("SELECT estacionamiento FROM $table_name WHERE user_id = %d", $user_id));

    if ($user_role === 'author') {
        $selected_estacionamiento = $estacionamiento;
    } else {
        $selected_estacionamiento = isset($_GET['estacionamiento']) ? intval($_GET['estacionamiento']) : null;
    }
    ?>

    <?php if ($user_role != 'author'): ?>
        <button id="estacionamiento-button2" data-dropdown-toggle="dropdown-estacionamiento2"
            class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-500 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600"
            type="button">
            <?php echo get_estacionamiento_name($selected_estacionamiento); ?> <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="m1 1 4 4 4-4" />
            </svg>
        </button>
        <div id="dropdown-estacionamiento2"
            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 absolute">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                aria-labelledby="estacionamiento-button2">
                <li><button type="button"
                        class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                        data-value="1">Bloque III</button></li>
                <li><button type="button"
                        class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                        data-value="2">Bloque IV</button></li>
                <li><button type="button"
                        class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                        data-value="3">Subsuelo y Rectorado</button></li>
                <li><button type="button"
                        class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                        data-value="4">Chacabuco y Pedernera</button></li>
            </ul>
        </div>
    <?php else: ?>
        <div class="text-white bg-gray-700 font-medium rounded-lg text-sm h-max px-5 py-2.5 text-center inline-flex items-center">
            Estacionamiento: <?php echo get_estacionamiento_name($selected_estacionamiento); ?>
            <input type="hidden" id="estacionamiento_index2" name="estacionamiento_index2" value="<?php echo $selected_estacionamiento; ?>">
        </div>
    <?php endif; ?>

    <label for="categoria_index2" class="sr-only">Categoría</label>
    <select id="categoria_index2" name="categoria_index2"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-e-lg border-s-gray-100 dark:border-s-gray-700 border-s-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="1" selected>Personal no docente</option>
        <option value="0">Personal docente</option>
        <option value="3">Visitas</option>
        <option value="2">Alumnos</option>
    </select>

    <button id="delete-oldest-button" type="submit"
        class="p-2.5 mx-2 px-2 text-sm font-medium bg-white rounded-lg border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
        <span>Eliminar</span>
    </button>
</form>
            </div>
        </a>
    </div>
</div>










<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Función para manejar el dropdown de un formulario específico
        function setupDropdown(buttonId, dropdownId, indexId) {
            const button = document.getElementById(buttonId);
            const dropdown = document.getElementById(dropdownId);
            const dropdownItems = dropdown.querySelectorAll('.dropdown-item');
            const index = document.getElementById(indexId);

            button.addEventListener('click', () => {
                dropdown.classList.toggle('hidden');
            });

            dropdownItems.forEach(item => {
                item.addEventListener('click', (event) => {
                    button.textContent = event.target.textContent;
                    index.value = event.target.getAttribute('data-value');
                    dropdown.classList.add('hidden');
                });
            });

            document.addEventListener('click', (event) => {
                if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        // Configura los dropdowns para ambos formularios
        setupDropdown('estacionamiento-button', 'dropdown-estacionamiento', 'estacionamiento-index');
        setupDropdown('estacionamiento-button2', 'dropdown-estacionamiento2', 'estacionamiento-index2');
    });
</script>


<?
get_footer();

?>