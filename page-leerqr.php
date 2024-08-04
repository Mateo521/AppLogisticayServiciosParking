<?php

if (!session_id()) {
    session_start();
}


get_header();


?>


<p class="text-center py-4">Leer QR</p>
<div class="flex justify-center   p-5">
    <div class="max-w-screen-lg w-full rounded-xl overflow-hidden">
        <div class="text-center" id="error-message"></div>
        <video class="w-full" id="cameraFeed" width="320" height="240" autoplay></video>


        <?php

if(isset($_SESSION['message'])): ?>
    <div id="alert-border-3" class="flex items-center px-4 py-2 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
      <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
    </svg>
    <div class="ms-3 text-sm font-medium">
    <?php echo $_SESSION['message']; ?>
    </div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700"  data-dismiss-target="#alert-border-3" aria-label="Close">
      <span class="sr-only">Cerrar</span>
      <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
      </svg>
    </button>
    <?php unset($_SESSION['message']);?>
</div>
<?php endif; ?>


    </div>
   
   
</div>
<div class="flex w-full justify-center">



    <div class="flex flex-wrap gap-5 p-2 justify-center">
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">Seleccione la opción... <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4" />
            </svg>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdown"
            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
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

        <form id="form1" class="flex items-center max-w-sm mx-auto" method="post"
            action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <label for="simple-search" class="sr-only">Search</label>
            <div class="relative w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2" />
                    </svg>
                </div>
                <input type="text" id="text" readonly
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Categorías..." required />
                <input type="hidden" id="categoria_index" name="categoria_index" value="">
                <input type="hidden" id="estacionamiento_index" name="estacionamiento_index" value="">
                <!-- Asegúrate de que este campo esté aquí -->
                <input type="hidden" name="action" value="insert_ingreso">

            </div>
            <div>

                <button id="ingreso-button" type="submit"
                    class="p-2.5 ms-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 hidden">
                    <span>Ingreso</span>
                </button>

                <button id="esperando-button" disabled type="button"
                    class="text-white mx-3 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 inline-flex items-center">
                    <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="#E5E7EB" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentColor" />
                    </svg>
                    Esperando...
                </button>

            </div>



        </form>








    </div>



</div>

<div class="flex justify-center" style="padding:35px 0 0;">


    <form id="form2" class="flex items-center max-w-sm mx-auto" method="post"
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" id="categoria_index2" name="categoria_index2" value="">
        <input type="hidden" id="estacionamiento_index2" name="estacionamiento_index2" value="">
        <input type="hidden" name="action" value="delete_oldest_ingreso">
        <button id="delete-oldest-button" type="submit"
            class="p-2.5 text-sm font-medium bg-white rounded-lg border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
            <span>Eliminar Más Antiguo</span>
        </button>



        <button id="esperando-egreso-button" disabled type="button"
            class="py-2.5 px-5 me-2 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 inline-flex items-center">
            <svg aria-hidden="true" role="status"
                class="inline w-4 h-4 me-3 text-gray-200 animate-spin dark:text-gray-600" viewBox="0 0 100 101"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor" />
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="#1C64F2" />
            </svg>
            Esperando...
        </button>
    </form>
</div>
<script src="https://cdn.rawgit.com/cozmo/jsQR/master/dist/jsQR.js"></script>





<style>

.message {
        margin: 20px 0;
        padding: 10px;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
    }
</style>
<script>


    document.addEventListener('DOMContentLoaded', function () {
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        const dropdownButton = document.getElementById('dropdownDefaultButton');
        const estacionamientoInput = document.getElementById('estacionamiento_index');
        const savedEstacionamiento = localStorage.getItem('selectedEstacionamiento'); // Obtiene la última selección guardada

        // Función para actualizar el texto del botón y el valor del campo oculto
        function updateDropdown(selectionText, selectionValue) {
            dropdownButton.innerHTML = `${selectionText} <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
    </svg>`;
            estacionamientoInput.value = selectionValue;
            localStorage.setItem('selectedEstacionamiento', selectionValue); // Guarda la selección en localStorage
        }

        // Inicializa el botón con la opción seleccionada al cargar la página
        if (savedEstacionamiento) {
            dropdownItems.forEach(item => {
                if (item.getAttribute('data-value') === savedEstacionamiento) {
                    updateDropdown(item.textContent.trim(), savedEstacionamiento);
                }
            });
        } else {
            // Si no hay selección guardada, establece la primera opción como predeterminada
            const firstItem = dropdownItems[0];
            if (firstItem) {
                updateDropdown(firstItem.textContent.trim(), firstItem.getAttribute('data-value'));
            }
        }

        // Maneja la selección de la opción
        dropdownItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const estacionamiento = this.getAttribute('data-value');
                updateDropdown(this.textContent.trim(), estacionamiento);
                document.getElementById('dropdown').classList.add('hidden');
            });
        });

        // Toggle dropdown visibility
        dropdownButton.addEventListener('click', function () {
            document.getElementById('dropdown').classList.toggle('hidden');
        });

        // Maneja la visibilidad de los botones según el valor del campo de texto
        const inputField = document.getElementById('text');
        const ingresoButton = document.getElementById('ingreso-button');
        const esperandoButton = document.getElementById('esperando-button');
        const egresoButton = document.getElementById('delete-oldest-button');
        const esperandoEgresoButton = document.getElementById('esperando-egreso-button');

        function updateButtonVisibility() {
            if (inputField.value.trim() === '') {
                ingresoButton.classList.add('hidden');
                esperandoButton.classList.remove('hidden');
                egresoButton.classList.add('hidden');
                esperandoEgresoButton.classList.remove('hidden');
            } else {
                esperandoButton.classList.add('hidden');
                ingresoButton.classList.remove('hidden');
                esperandoEgresoButton.classList.add('hidden');
                egresoButton.classList.remove('hidden');
            }
        }

        inputField.addEventListener('input', updateButtonVisibility);

        // Inicializa el estado de los botones al cargar la página
        updateButtonVisibility();

    });


    function base64Decode(base64) {
        try {
            return atob(base64);
        } catch (e) {
            document.getElementById('text').value = 'QR desconocido...';
            return base64;
        }
    }

    async function initCamera() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');
                const rearCamera = videoDevices.find(device => device.label.toLowerCase().includes('back')) || videoDevices[0];

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        deviceId: rearCamera.deviceId
                    }
                });

                const video = document.getElementById('cameraFeed');
                video.srcObject = stream;
                video.addEventListener('loadedmetadata', () => {
                    scanQRCode(stream);
                });

                // Close the camera stream when leaving the page.
                window.addEventListener('beforeunload', () => {
                    stream.getTracks().forEach(track => track.stop());
                });
            } catch (error) {
                console.error('Error accessing camera:', error);
            }
        }

        
    function scanQRCode(stream) {
        const video = document.getElementById('cameraFeed');
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        function scan() {
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code) {
                var decoded = base64Decode(code.data);

                const categorias = ["Personal docente", "Personal no docente", "Alumno", "Visita"];

                let k = decoded.substring(1, 2);
                let index = parseInt(k, 10);
                console.log('Código:', k);
                let m = "";
                switch (index) {
                    case 0:
                        m = categorias[0];
                        break;
                    case 1:
                        m = categorias[1];
                        break;
                    case 2:
                        m = categorias[2];
                        break;
                    case 3:
                        m = categorias[3];
                        break;
                    default:
                        m = "Categoría desconocida";
                        break;
                }

                document.getElementById('categoria_index').value = index;
                document.getElementById('categoria_index2').value = index;
                document.getElementById('text').value = m;



                estacionamientoIndex = document.getElementById('estacionamiento_index').value
                var categoriaIndex = document.getElementById('categoria_index').value;
                document.getElementById('estacionamiento_index2').value = estacionamientoIndex;

                var estacionamientoIndex2 = estacionamientoIndex;

                console.log('Valor de categoria_index:', document.getElementById('categoria_index').value);
                console.log('Valor de estacionamiento_index:', document.getElementById('categoria_index2').value);

                console.log('Valor de categoria_index2:', document.getElementById('estacionamiento_index').value);
                console.log('Valor de estacionamiento_index2:', document.getElementById('estacionamiento_index2').value);

                // Actualiza la visibilidad de los botones después de escanear el QR
                const ingresoButton = document.getElementById('ingreso-button');
                const esperandoButton = document.getElementById('esperando-button');
                const egresoButton = document.getElementById('delete-oldest-button');
                const esperandoEgresoButton = document.getElementById('esperando-egreso-button');

                if (document.getElementById('text').value.trim() === '') {
                    ingresoButton.classList.add('hidden');
                    esperandoButton.classList.remove('hidden');
                    egresoButton.classList.add('hidden');
                    esperandoEgresoButton.classList.remove('hidden');
                } else {
                    esperandoButton.classList.add('hidden');
                    ingresoButton.classList.remove('hidden');
                    esperandoEgresoButton.classList.add('hidden');
                    egresoButton.classList.remove('hidden');
                }
            }
            requestAnimationFrame(scan);
        }

        scan();
    }

    // Inicializa la cámara al cargar la página
    initCamera();
</script>


<?

get_footer();

?>