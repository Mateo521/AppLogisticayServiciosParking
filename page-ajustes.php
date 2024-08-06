<?php

get_header();


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

    <form method="POST" action="">
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
        <button type="submit" name="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Registrar nueva cuenta</button>
    </form>






    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Usuario</th>
                <th scope="col" class="px-6 py-3">Rol</th>
                <th scope="col" class="px-6 py-3">Estacionamiento</th>
                <th scope="col" class="px-6 py-3">Último inicio de sesión</th>
                <th scope="col" class="px-6 py-3">Estado</th>
                <th scope="col" class="px-6 py-3">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $users = get_users(array('role' => 'author'));
            global $wpdb;
            $table_name = $wpdb->prefix . 'estacionamientos';

            foreach ($users as $user) {
                $estacionamiento = $wpdb->get_var($wpdb->prepare("SELECT estacionamiento FROM $table_name WHERE user_id = %d", $user->ID));
            ?>
                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"><?php echo esc_html($user->user_login); ?></th>
                    <td class="px-6 py-4">Author</td>
                    <td class="px-6 py-4"><?php echo esc_html($estacionamiento); ?></td>
                    <td class="px-6 py-4"><?php echo esc_html($user->last_login); ?></td>
                    <td class="px-6 py-4"><?php echo (get_user_meta($user->ID, 'session_tokens', true)) ? 'Conectado' : 'Desconectado'; ?></td>
                    <td class="px-6 py-4">
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