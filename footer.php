<?php wp_footer() ?>
<!-- Script Flowbite -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>


<?php
if ( current_user_can( 'editor' ) ) {
?>
      


<div class="p-5  grid md:grid-cols-2 grid-cols-1 md:gap-10 gap-2 justify-between">
    <div class="flex flex-col">

        <p> Estacionamiento actual (admin)</p>
        <canvas id="myChart2" style="width:100%;"></canvas>
    </div>

    <div class="flex flex-col">

        <p> Vehiculos egresados ("agregar intervalo de fechas") (admin)</p>
        <canvas id="myChart" style="width:100%;"></canvas>
    </div>




</div>



<script>
    var xValues2 = ["Italy", "France", "Spain", "USA", "Argentina"];
    var yValues2 = [55, 49, 44, 24, 15];
    var barColors2 = ["red", "green", "blue", "orange", "brown"];

    new Chart("myChart2", {
        type: "bar",
        data: {
            labels: xValues2,
            datasets: [{
                backgroundColor: barColors2,
                data: yValues2
            }]
        },
        options: {
            legend: {
                display: false
            },
            title: {
                display: true,
                text: "World Wine Production 2018"
            }
        }
    });



    const xValues = [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000];

    new Chart("myChart", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                    data: [860, 1140, 1060, 1060, 1070, 1110, 1330, 2210, 7830, 2478],
                    borderColor: "red",
                    fill: false
                },
                {
                    data: [1600, 1700, 1700, 1900, 2000, 2700, 4000, 5000, 6000, 7000],
                    borderColor: "green",
                    fill: false
                },
                {
                    data: [1600, 1100, 400, 100, 2000, 2500, 3000, 1000, 3100, 600],
                    borderColor: "orange",
                    fill: false
                },
                {
                    data: [300, 700, 2000, 5000, 6000, 4000, 2000, 1000, 200, 100],
                    borderColor: "blue",
                    fill: false
                }
            ]
        },
        options: {
            legend: {
                display: false
            }
        }
    });
</script>

<?php
}?>
</body>

</html>