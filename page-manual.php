

<?php

 get_header();

$current_url = home_url('/manual'); // o home_url('/leerqr'), dependiendo de la página actual
?>
<form class="max-w-sm mx-auto py-24" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
    <input type="hidden" name="action" value="insert_ingreso">
    <input type="hidden" name="redirect_url" value="<?php echo esc_url($current_url); ?>">
    <div class="flex relative">
        <button id="estacionamiento-button" data-dropdown-toggle="dropdown-estacionamiento" class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-500 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600" type="button">
            Bloque III <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>
        <div id="dropdown-estacionamiento" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 absolute">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="estacionamiento-button">
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="1">Bloque III</button></li>
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="2">Bloque IV</button></li>
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="3">Subsuelo y Rectorado</button></li>
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="4">Chacabuco y Pedernera</button></li>
            </ul>
        </div>
        <input type="hidden" id="estacionamiento-index" name="estacionamiento_index" value="1">

        <label for="categoria_index" class="sr-only">Categoría</label>
        <select id="categoria_index" name="categoria_index" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-e-lg border-s-gray-100 dark:border-s-gray-700 border-s-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="1" selected>Personal no docente</option>
            <option value="0">Personal docente</option>
            <option value="3">Visitas</option>
            <option value="2">Alumnos</option>
        </select>


 

        <button type="submit" class=" px-2 py-2 bg-blue-600 text-white rounded-lg mx-2">Ingresar</button>
    </div>
  
</form>





<form id="form2" class="flex items-center max-w-sm mx-auto py-24" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" id="categoria_index2" name="categoria_index2" value="">
        <input type="hidden" id="estacionamiento_index2" name="estacionamiento_index2" value="">
        <input type="hidden" name="action" value="delete_oldest_ingreso">


            <input type="hidden" name="redirect_url2" value="<?php echo esc_url($current_url); ?>">
 
        <button id="estacionamiento-button2" data-dropdown-toggle="dropdown-estacionamiento2" class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-500 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700 dark:text-white dark:border-gray-600" type="button">
            Bloque III <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>
        <div id="dropdown-estacionamiento2" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 absolute">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="estacionamiento-button2">
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="1">Bloque III</button></li>
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="2">Bloque IV</button></li>
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="3">Subsuelo y Rectorado</button></li>
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="4">Chacabuco y Pedernera</button></li>
            </ul>
        </div>
        <input type="hidden" id="estacionamiento-index2" name="estacionamiento_index2" value="1">

        <label for="categoria_index2" class="sr-only">Categoría</label>
        <select id="categoria_index2" name="categoria_index2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-e-lg border-s-gray-100 dark:border-s-gray-700 border-s-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="1" selected>Personal no docente</option>
            <option value="0">Personal docente</option>
            <option value="3">Visitas</option>
            <option value="2">Alumnos</option>
        </select>
 
 
 
        <button id="delete-oldest-button" type="submit" class="p-2.5 mx-2 px-2 text-sm font-medium bg-white rounded-lg border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <span>Eliminar</span>
        </button>







    </form>
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