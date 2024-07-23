<?php

get_header();


?>


<p class="text-center py-4">Leer QR</p>
<p class="text-center" id="mensaje"></p>

    <video id="preview"></video>
    <script type="text/javascript">
      let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
      scanner.addListener('scan', function (content) {
        console.log(content);
      });
      Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          scanner.start(cameras[0]);
        } else {
        var l = document.getElementById("mensaje");
        l.innerHTML = "No se encontró camara";
         
        }
      }).catch(function (e) {
        console.error(e);
      });
    </script>




<?

get_footer();

?>