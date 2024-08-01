

<?php

 get_header();

$current_url = home_url('/manual'); // o home_url('/leerqr'), dependiendo de la página actual
?>
<form class="max-w-sm mx-auto" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
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
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="3">Chacabuco y Pedernera</button></li>
                <li><button type="button" class="dropdown-item inline-flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" data-value="4">Rectorado</button></li>
            </ul>
        </div>
        <input type="hidden" id="estacionamiento-index" name="estacionamiento_index" value="1">

        <label for="categoria_index" class="sr-only">Categoría</label>
        <select id="categoria_index" name="categoria_index" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-e-lg border-s-gray-100 dark:border-s-gray-700 border-s-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="0" selected>Personal no docente</option>
            <option value="1">Personal docente</option>
            <option value="2">Visitas</option>
            <option value="3">Alumnos</option>
        </select>
    </div>
    <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Submit</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
  const estacionamientoButton = document.getElementById('estacionamiento-button');
  const dropdownEstacionamiento = document.getElementById('dropdown-estacionamiento');
  const dropdownItems = document.querySelectorAll('.dropdown-item');
  const estacionamientoIndex = document.getElementById('estacionamiento-index');

  estacionamientoButton.addEventListener('click', () => {
      dropdownEstacionamiento.classList.toggle('hidden');
  });

  dropdownItems.forEach(item => {
      item.addEventListener('click', (event) => {
          estacionamientoButton.textContent = event.target.textContent;
          estacionamientoIndex.value = event.target.getAttribute('data-value');
          dropdownEstacionamiento.classList.add('hidden');
      });
  });

  document.addEventListener('click', (event) => {
      if (!estacionamientoButton.contains(event.target) && !dropdownEstacionamiento.contains(event.target)) {
          dropdownEstacionamiento.classList.add('hidden');
      }
  });
});
</script>

<?
get_footer();

?>