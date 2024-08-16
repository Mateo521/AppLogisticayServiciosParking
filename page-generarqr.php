<?php

get_header();

?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
    #qrcode {
        width: 100%;

        margin-top: 20px;
    }
</style>
<div class="flex justify-center">
    <div class="max-w-screen-xl w-full">
        <h1 class="text-xl text-center font-extrabold dark:text-white mb-12 mt-3">Generar QR</h1>

        <div class="flex w-full justify-center">
            <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button">Seleccionar categorías
                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>
        </div>
        <div id="dropdown"
            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                <li>
                    <a href="#" onclick="handleMenuClick('[0] personal docente')"
                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Personal
                        Docente</a>
                </li>
                <li>
                    <a href="#" onclick="handleMenuClick('[1] personal no docente')"
                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Personal
                        No Docente</a>
                </li>
                <li>
                    <a href="#" onclick="handleMenuClick('[2] alumnos unsl')"
                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Alumnos</a>
                </li>
                <li>
                    <a href="#" onclick="handleMenuClick('[3] visitas unsl')"
                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Visitas</a>
                </li>
            </ul>
        </div>


        <div id="qrcode" class="flex justify-center"></div>

        <div class="flex w-full justify-center gap-4 items-center">
            <button id="printMultipleButton" style="display:none;" onclick="handlePrintButtonClick()"
                class="text-white my-12 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                Imprimir QR
            </button>
            <div class="max-w-max w-full">
                <input type="number" id="number-input" aria-describedby="helper-text-explanation"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Seleccione copias..." required />
            </div>
        </div>
        <script>

            function handlePrintButtonClick() {
                const numberInput = document.getElementById('number-input');
                const numberOfCopies = parseInt(numberInput.value, 10);

                if (isNaN(numberOfCopies) || numberOfCopies <= 0) {
                    alert('Por favor, ingrese un número válido de copias.');
                    return;
                }

                printMultipleQR(numberOfCopies);
            }


            let encodedText = '';  // Definición global de la variable

            const dropdownButton = document.getElementById('dropdownDefaultButton');
            const dropdownMenu = document.getElementById('dropdown');

            dropdownButton.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });

            function handleMenuClick(qrData) {
                generateQR(qrData); // Llama a tu función para generar el QR
                dropdownMenu.classList.add('hidden'); // Oculta el menú
            }

            document.addEventListener('click', (event) => {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });

            function generateQR(text) {
                // Codifica el texto en Base64 y asigna el valor a la variable global
                encodedText = btoa(text);

                // Limpiar el contenido anterior del contenedor QR
                document.getElementById("qrcode").innerHTML = "";

                // Generar el código QR
                const qrcode = new QRCode(document.getElementById("qrcode"), {
                    text: encodedText,
                    width: 256,
                    height: 256,
                    foreground: "#004078"
                });

                // Mostrar el botón para imprimir múltiples QR
                document.getElementById("printMultipleButton").style.display = 'block';
            }

            function printMultipleQR(quantity) {
                const qrWindow = window.open('', '_blank');
                qrWindow.document.write('<html><head><title>Imprimir QR</title>');
                qrWindow.document.write('<style>');
                qrWindow.document.write('body { display: flex; flex-wrap: wrap; }');
                qrWindow.document.write('.qrcode { margin: 10px; }');
                qrWindow.document.write('</style>');
                qrWindow.document.write('</head><body>');

                for (let i = 0; i < quantity; i++) {
                    qrWindow.document.write('<div class="qrcode"></div>');
                }

                qrWindow.document.write('</body></html>');
                qrWindow.document.close();

                // Generar el QR en cada contenedor en la nueva ventana
                qrWindow.onload = function () {
                    const qrContainers = qrWindow.document.querySelectorAll('.qrcode');
                    qrContainers.forEach(container => {
                        new QRCode(container, {
                            text: encodedText,  // Utiliza la variable global definida en generateQR
                            width: 256,
                            height: 256,
                        });
                    });
                    qrWindow.print();
                };
            }

        </script>




    </div>



    <?php
    get_footer();
    ?>