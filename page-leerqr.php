<?php

get_header();


?>


<p class="text-center py-4">Leer QR</p>


    <video class="w-full p-5 rounded-lg" id="cameraFeed" width="320" height="240" autoplay></video>
<div class="flex w-full justify-center">
   




    <form class="flex items-center max-w-sm mx-auto">   
    <label for="simple-search" class="sr-only">Search</label>
    <div class="relative w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2"/>
            </svg>
        </div>
        <input type="text" id="text" readonly id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search branch name..." required />
    </div>
    <button type="submit" class="p-2.5 ms-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
     
        <span>Ingreso</span>
    </button>
</form>


    </div>


    <script src="https://cdn.rawgit.com/cozmo/jsQR/master/dist/jsQR.js"></script>





  
    <script>
 function base64Decode(base64) {
    return atob(base64);
}
    </script>


    <!--h1>Decodificar SAML</h1>
    <textarea id="samlInput" rows="10" cols="100" placeholder="Pega tu texto SAML codificado en Base64 aquí"></textarea>
    <br>
    <button onclick="decodeSAML()">Decodificar</button>
    <h2>Resultado:</h2>
    <pre id="output"></pre-->
<!-- 
    
[0] personal docente    ====== WzBdIHBlcnNvbmFsIGRvY2VudGU=
[1] personal no docente ====== WzFdIHBlcnNvbmFsIG5vIGRvY2VudGU=
[2] alumnos unsl        ====== WzJdIGFsdW1ub3MgdW5zbA==
[3] visitas unsl        ====== WzNdIHZpc2l0YXMgdW5zbA==

-->


    <script>
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
                    console.log('QR Code detected:', code.data);

                    var decoded = base64Decode(code.data);
                    document.getElementById('text').textContent = decoded;

              /*      document.getElementById('text').value = code.data;*/
                
                }
                requestAnimationFrame(scan);
            }

            scan();
        }

        initCamera();
    </script>

  
<?

get_footer();

?>