<?php

get_header();



function obtener_ajustes_estacionamiento() {
    global $wpdb;
    $tabla_ajustes = $wpdb->prefix . 'ajustes_estacionamiento';
    return $wpdb->get_row("SELECT * FROM $tabla_ajustes WHERE id = 1", ARRAY_A);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $estacionamiento = intval($_POST['estacionamiento']);

    $user_id = wp_create_user($email, $password, $email);

    if (is_wp_error($user_id)) {
        echo 'Error al crear el usuario: ' . $user_id->get_error_message();
    } else {
        wp_update_user(array('ID' => $user_id, 'role' => 'author'));

        // Guardar el estacionamiento en una tabla personalizada
        global $wpdb;
        $table_name = $wpdb->prefix . 'estacionamientos';

        $result = $wpdb->insert($table_name, array(
            'user_id' => $user_id,
            'estacionamiento' => $estacionamiento
        ));

        if ($result === false) {
            echo 'Error al insertar en la base de datos: ' . $wpdb->last_error;
        } else {
            echo 'Usuario creado exitosamente.';
        }
    }
}


if (current_user_can('editor') || current_user_can('administrator')) {
?>

<div class="flex justify-center">
    <div class="max-w-screen-xl w-full">

    <h4 class="text-2xl font-bold dark:text-white pt-4 pb-2 px-2">Usuarios</h4>
    <form method="POST" action="" class="pb-10 px-4">
    <div class="mb-5">
            <label for="user" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Usuario</label>
            <input type="text" id="user" name="user" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="name@flowbite.com" required />
        </div>
        <div class="mb-5">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" placeholder="name@flowbite.com" required />
        </div>
        <div class="mb-5">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña</label>
            <input type="password" id="password" name="password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required />
        </div>
        <div class="mb-5">
            <label for="estacionamiento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estacionamiento</label>
            <select id="estacionamiento" name="estacionamiento" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required>
                <option value="1">Bloque III</option>
                <option value="2">Bloque IV</option>
                <option value="3">Subsuelo y Rectorado</option>
                <option value="4">Chacabuco y Pedernera</option>
            </select>
        </div>
        <div class="mb-5">
            <label for="rol" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rol</label>
            <select id="rol" name="rol" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light" required>
                <option value="1">Guardia</option>
                <option value="2">Administrador</option>
            </select>
        </div>
        <button type="submit" name="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Agregar nuevo usuario</button>
    </form>





    <p class="text-xl font-bold dark:text-white pt-4 pb-2 px-2">Todos los usuarios</p>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-3 py-3">Usuario</th>
                <th scope="col" class="px-3 py-3">Rol</th>
                <th scope="col" class="px-3 py-3">Estacionamiento</th>
                <th scope="col" class="px-3 py-3">Último inicio de sesión</th>
                <th scope="col" class="px-3 py-3">Estado</th>
                <th scope="col" class="px-3 py-3">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $users = get_users(array('role' => 'author'));
            global $wpdb;
            $table_name = $wpdb->prefix . 'estacionamientos';


            $estacionamientos = [
                1 => "Bloque III",
                2 => "Bloque IV",
                3 => "Subsuelo y Rectorado",
                4 => "Chacabuco y Pedernera"
            ];
          


            foreach ($users as $user) {
                $estacionamiento = $wpdb->get_var($wpdb->prepare("SELECT estacionamiento FROM $table_name WHERE user_id = %d", $user->ID));
                $current_estacionamiento = isset($estacionamientos[$estacionamiento]) ? $estacionamientos[$estacionamiento] : "No seleccionado";
         
           ?>
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"><?php echo esc_html($user->user_login); ?></th>
                    <td class="px-3 py-4">Author</td>
                    <td class="px-3 py-4"><?php echo esc_html($current_estacionamiento); ?></td>
                    <td class="px-3 py-4"><?php echo esc_html($user->last_login); ?></td>
                    <td class="px-3 py-4"><?php echo (get_user_meta($user->ID, 'session_tokens', true)) ? '🟢 Conectado' : '🔴 Desconectado'; ?></td>
                    <td class="px-3 py-4">
                        <button onclick="showEditForm('<?php echo esc_html($user->ID); ?>', '<?php echo esc_html($user->user_login); ?>', '<?php echo esc_html($estacionamiento); ?>')" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Editar</button>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>






    <!-- Modal -->
    <div id="editFormContainer" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
    <form id="editForm" class="bg-white p-4 rounded-lg shadow-md" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="edit_estacionamiento">
        <input type="hidden" id="edit_user_id" name="user_id" value="">
        <h2 class="text-xl font-bold mb-4">Editar Estacionamiento</h2>
        <div class="mb-4">
            <label for="edit_user_login" class="block text-sm font-medium text-gray-700">Usuario</label>
            <input type="text" id="edit_user_login" name="user_login" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" readonly>
        </div>
        <div class="mb-4">
            <label for="edit_estacionamiento" class="block text-sm font-medium text-gray-700">Estacionamiento</label>
            <select id="edit_estacionamiento" name="estacionamiento" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                <option value="1">Bloque III</option>
                <option value="2">Bloque IV</option>
                <option value="3">Subsuelo y Rectorado</option>
                <option value="4">Chacabuco y Pedernera</option>
            </select>
        </div>
        <div class="flex justify-end">
            <button type="button" onclick="hideEditForm()" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancelar</button>
            <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md">Guardar</button>
        </div>
    </form>
</div>

<hr>

<h4 class="text-2xl font-bold dark:text-white pt-4 pb-2 px-2 mt-4">Capacidad de estacionamientos</h4>


<form class="max-w-lg" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
    <div class="flex">
        <label for="search-dropdown" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Capacidad</label>
        
        <button id="dropdown-button" data-dropdown-toggle="dropdown" class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600" type="button">Seleccione categoría
            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>
        
        <input type="hidden" name="categoria" id="categoria-input" value="Todas las categorías"> <!-- Campo oculto para almacenar la categoría seleccionada -->
        
        <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-button">
                <li>
                    <button type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" onclick="setCategoria('Bloque III')">Bloque III</button>
                </li>
                <li>
                    <button type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" onclick="setCategoria('Bloque IV')">Bloque IV</button>
                </li>
                <li>
                    <button type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" onclick="setCategoria('Subsuelo y Rectorado')">Subsuelo y Rectorado</button>
                </li>
                <li>
                    <button type="button" class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" onclick="setCategoria('Chacabuco y Pedernera')">Chacabuco y Pedernera</button>
                </li>
            </ul>
        </div>
        
        <div class="relative w-full">
            <input type="number" min="0" id="search-dropdown" name="capacidad" class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-e-lg border-s-gray-50 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-s-gray-700  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500" placeholder="Número entero..." required />
            <button type="submit" class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 rounded-e-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
               
            <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
</svg>


                <span class="sr-only">Aceptar</span>
            </button>
        </div>
    </div>
    
    <input type="hidden" name="action" value="actualizar_capacidad_estacionamiento"> <!-- Action para que se ejecute la función -->
</form>

<script>
    function setCategoria(categoria) {
        document.getElementById('categoria-input').value = categoria;
        document.getElementById('dropdown-button').textContent = categoria;
    }
</script>



<h4 class="text-2xl font-bold dark:text-white pt-4 pb-2 px-2">Horarios</h4>




<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="max-w-lg">
    <input type="hidden" name="action" value="actualizar_horarios_estacionamiento">
    <div class="flex">
        <button id="dropdownTimepickerButton" data-dropdown-toggle="dropdownTimepicker" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">Elegir horarios 
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>
        
        <!-- Dropdown menu -->
        <div id="dropdownTimepicker" class="z-10 hidden bg-white rounded-lg shadow w-auto dark:bg-gray-700 p-3">
            <div class="max-w-[16rem] mx-auto grid grid-cols-2 gap-4 mb-2">
                <div>
                    <label for="start-time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white min-w-max">Horario de ingreso:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" id="start-time" name="horario_ingreso" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" min="06:00" max="18:00" value="00:00" required />
                    </div>
                </div>
                <div>
                    <label for="end-time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Horario de egreso:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" id="end-time" name="horario_egreso" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" min="09:00" max="23:59" value="00:00" required />
                    </div>
                </div>
            </div>
            <button id="saveTimeButton" type="submit" class="text-blue-700 dark:text-blue-500 text-sm hover:underline p-0">Guardar</button>
        </div>
    </div>
</form>





<div class="relative overflow-x-auto shadow-md sm:rounded-lg my-12">
    <table class="w-full text-sm text-left rtl:text-right text-blue-100 dark:text-blue-100">
        <thead class="text-xs text-white uppercase bg-blue-600 dark:text-white">
            <tr>
                <th scope="col" class="px-3 py-3">
                    Capacidad Bloque III
                </th>
                <th scope="col" class="px-3 py-3">
                    Capacidad Bloque IV
                </th>
                <th scope="col" class="px-3 py-3">
                    Capacidad Subsuelo y Rectorado
                </th>
                <th scope="col" class="px-3 py-3">
                    Capacidad Chacabuco y Pedernera
                </th>
                <th scope="col" class="px-3 py-3">
                    Horario de ingreso
                </th>
                <th scope="col" class="px-3 py-3">
                    Horario de egreso
                </th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Obtener los datos de la base de datos
            $ajustes = obtener_ajustes_estacionamiento();
            ?>
            <tr class="bg-blue-500 border-b border-blue-400">
                <td class="px-3 py-4 font-medium text-blue-50 whitespace-nowrap dark:text-blue-100">
                    <?php echo esc_html($ajustes['capacidad_bloque_iii']); ?>
                </td>
                <td class="px-3 py-4">
                    <?php echo esc_html($ajustes['capacidad_bloque_iv']); ?>
                </td>
                <td class="px-3 py-4">
                    <?php echo esc_html($ajustes['capacidad_subsuelo_rectorado']); ?>
                </td>
                <td class="px-3 py-4">
                    <?php echo esc_html($ajustes['capacidad_chacabuco_pedernera']); ?>
                </td>
                <td class="px-3 py-4">
                    <?php echo esc_html($ajustes['horario_ingreso']); ?>
                </td>
                <td class="px-3 py-4">
                    <?php echo esc_html($ajustes['horario_egreso']); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

</div>

</div>


<script>
   function showEditForm(userId, userLogin, estacionamiento) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_user_login').value = userLogin;
    document.getElementById('edit_estacionamiento').value = estacionamiento;
    document.getElementById('editFormContainer').classList.remove('hidden');
}

function hideEditForm() {
    document.getElementById('editFormContainer').classList.add('hidden');
}

</script>





<?php







} else {

?>

    <h1 class="text-center p-1">No posees los permisos necesarios para acceder a esta página.</h1>

<?php

}

get_footer();
?>