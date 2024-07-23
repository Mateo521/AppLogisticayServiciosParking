<?php

get_header();


?>


<p class="text-center py-4">Leer QR</p>


    <video class="w-full p-5 rounded-lg" id="cameraFeed" width="320" height="240" autoplay></video>
<div class="flex w-full justify-center">
    <input  type="text" id="text" readonly>
    </div>


    <script src="https://cdn.rawgit.com/cozmo/jsQR/master/dist/jsQR.js"></script>





  
    <script>
 function base64Decode(base64) {
    return atob(base64);
}

function decodeSAML() {
    var samlResponse = document.getElementById('samlInput').value;
    var decoded = base64Decode(samlResponse);
    document.getElementById('output').textContent = decoded;
}
    </script>


    <h1>Decodificar SAML</h1>
    <textarea id="samlInput" rows="10" cols="100" placeholder="Pega tu texto SAML codificado en Base64 aquí"></textarea>
    <br>
    <button onclick="decodeSAML()">Decodificar</button>
    <h2>Resultado:</h2>
    <pre id="output"></pre>

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
                    document.getElementById('text').value = code.data;
                
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