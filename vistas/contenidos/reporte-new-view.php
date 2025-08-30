<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['privilegio_xcoring'] != 1) {
    echo $lc->forzar_cierre_sesion_controlador();
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xcoring";

try {
    $conexion = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];

    $titulos_graficas = array(
        'graficoGeneral' => 'Evaluación promedio general',
        'grafico' => 'Puntuación Ponderada por Categoría',
        'graficoResultados' => 'Evaluación Resultados Bajos (1) y altos (5)',
        'graficoRanking' => 'Evaluación Ranking promedio - 1ro a 5to',
        'graficorRadar' => 'Radar de Desempeño por Categoría de los diferentes proveedores'
    );
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<?php if (isset($_SESSION['datos_proyecto'])) { ?>

<!DOCTYPE html>
<html>

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>

    <style>
        .centrar-texto {
            text-align: center;
        }

        .grafico-container {
            width: 100%;
            float: left;
            padding: 10px;
            box-sizing: border-box;
            height: auto;
            max-width: 800px;
            margin: 0 auto;
        }

        canvas {
            width: 100%;
            height: auto;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        #mostrarInfo {
            background-color: #021B79;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #mostrarInfo:hover {
            background-color: #00164A;
        }

        #descargar {
            background-color: #021B79;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        #descargar:hover {
            background-color: #00164A;
        }

        @media print {
            .grafico-container {
                width: 50%;
                float: left;
                padding: 10px;
                box-sizing: border-box;
                page-break-inside: avoid;
            }

            canvas {
                width: 100% !important;
                height: auto !important;
            }
        }

        .centrar-texto {
            text-align: center;
            margin: 0 auto;
            width: 100%;
            max-width: 500px;
        }

        @media (max-width: 768px) {
            .centrar-texto {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="full-box page-header">
        <h3 class="text-left">
            <i class="fas fa-chart-bar fa-fw"></i> &nbsp; Reportes
        </h3>
        <br>
        <?php
        $nombreCliente = $_SESSION['datos_cliente'][0]['NombreCliente'];
        $nombreProyecto = $_SESSION['datos_proyecto'][0]['NombreProyecto'];
        if (is_string($nombreCliente)) {
            echo "<h4 class=\"centrar-texto\">Evaluación del cliente <strong>$nombreCliente</strong> en el proyecto <strong>$nombreProyecto</strong></h4>";
        } else {
            echo "<h3>Error: Nombre del cliente no válido</h3>";
        }
        ?>
    </div>

    <div class="centrar-texto">
        <button id="mostrarInfo" onclick="mostrarInformacion()">Generar evaluación</button>
        <button id="descargar" onclick="descargarInformacion()">Descargar Información</button>
    </div>

    <div id="informacion" style="display: none;">
        <?php
        function calcularPromedioPonderadoRanking($calificaciones, $categorias, $criterios)
        {
            $puntuaciones_por_proveedor = [];
            foreach ($calificaciones as $idProveedor => $proveedorCalificaciones) {
                foreach ($proveedorCalificaciones as $idCriterio => $evaluacion) {
                    $idCategoria = $criterios[$idCriterio]['id_categoria'];
                    $pesoCategoria = $categorias[$idCategoria]['peso'];
                    $pesoCriterio = $criterios[$idCriterio]['peso'];
                    $puntuacion = $evaluacion * ($pesoCategoria * $pesoCriterio) / 10000;

                    if (!isset($puntuaciones_por_proveedor[$idProveedor])) {
                        $puntuaciones_por_proveedor[$idProveedor] = 0;
                    }
                    $puntuaciones_por_proveedor[$idProveedor] += $puntuacion;
                }
            }
            return $puntuaciones_por_proveedor;
        }

        function calcularPromedioPonderado($calificaciones, $categorias, $criterios)
        {
            $puntuaciones_por_proveedor = [];
            foreach ($calificaciones as $idProveedor => $proveedorCalificaciones) {
                foreach ($proveedorCalificaciones as $idCriterio => $calificacion_evaluacion) {
                    $idCategoria = $criterios[$idCriterio]['id_categoria'];
                    $pesoCategoria = $categorias[$idCategoria]['peso'];
                    $pesoCriterio = $criterios[$idCriterio]['peso'];
                    $puntuacion = $calificacion_evaluacion * ($pesoCategoria * $pesoCriterio) / 10000;

                    if (!isset($puntuaciones_por_proveedor[$idProveedor])) {
                        $puntuaciones_por_proveedor[$idProveedor] = 0;
                    }
                    $puntuaciones_por_proveedor[$idProveedor] += $puntuacion;
                }
            }
            return $puntuaciones_por_proveedor;
        }

        $sqlCategorias = "SELECT id_categoria, nombre_categoria, peso_categoria FROM categoria WHERE id_proyecto = '$id_proyecto'";
        $sqlCriterios = "SELECT c.id_criterio, c.id_categoria, c.peso_criterio 
        FROM criterio c
        INNER JOIN categoria cat ON c.id_categoria = cat.id_categoria
        WHERE cat.id_proyecto = '$id_proyecto'";
        $sqlCalificaciones = "SELECT id_proveedor, id_criterio, calificacion_evaluacion FROM evaluacion WHERE id_proyecto = '$id_proyecto'";
        $sqlProveedores = "SELECT DISTINCT p.id_proveedor, p.nombre_proveedor 
        FROM proveedor p
        LEFT JOIN evaluacion e ON p.id_proveedor = e.id_proveedor
        WHERE e.id_proyecto = '$id_proyecto' OR e.id_proyecto IS NULL";

        $resultCategorias = $conexion->query($sqlCategorias);
        $resultCriterios = $conexion->query($sqlCriterios);
        $resultCalificaciones = $conexion->query($sqlCalificaciones);
        $resultProveedores = $conexion->query($sqlProveedores);

        $categorias = [];
        while ($row = $resultCategorias->fetch(PDO::FETCH_ASSOC)) {
            $categorias[$row['id_categoria']] = [
                'nombre' => $row['nombre_categoria'],
                'peso' => $row['peso_categoria']
            ];
        }

        $criterios = [];
        while ($row = $resultCriterios->fetch(PDO::FETCH_ASSOC)) {
            $criterios[$row['id_criterio']] = [
                'id_categoria' => $row['id_categoria'],
                'peso' => $row['peso_criterio']
            ];
        }

        $calificaciones = [];
        while ($row = $resultCalificaciones->fetch(PDO::FETCH_ASSOC)) {
            $calificaciones[$row['id_proveedor']][$row['id_criterio']] = $row['calificacion_evaluacion'];
        }

        $puntuaciones_por_proveedor = calcularPromedioPonderado($calificaciones, $categorias, $criterios);

        $proveedores = [];
        while ($row = $resultProveedores->fetch(PDO::FETCH_ASSOC)) {
            $proveedores[$row['id_proveedor']] = $row['nombre_proveedor'];
        }
        ?>
        
        <div class="grafico-container">
            <canvas id="graficoGeneral"></canvas>
        </div>

        <div class="grafico-container">
            <canvas id="grafico"></canvas>
        </div>

        <div class="grafico-container">
            <canvas id="graficoResultados"></canvas>
        </div>

        <div class="grafico-container">
            <canvas id="graficoRanking"></canvas>
        </div>

        <div class="grafico-container">
            <canvas id="graficoRadar"></canvas>
        </div>

        <?php
        try {
            $resultCategorias = $conexion->query($sqlCategorias);
            $resultCriterios = $conexion->query($sqlCriterios);
            $resultCalificaciones = $conexion->query($sqlCalificaciones);
            $resultProveedores = $conexion->query($sqlProveedores);

            $categorias = [];
            while ($row = $resultCategorias->fetch(PDO::FETCH_ASSOC)) {
                $categorias[$row['id_categoria']] = [
                    'nombre' => $row['nombre_categoria'],
                    'peso' => $row['peso_categoria']
                ];
            }

            $criterios = [];
            while ($row = $resultCriterios->fetch(PDO::FETCH_ASSOC)) {
                $criterios[$row['id_criterio']] = [
                    'id_categoria' => $row['id_categoria'],
                    'peso' => $row['peso_criterio']
                ];
            }

            $calificaciones = [];
            while ($row = $resultCalificaciones->fetch(PDO::FETCH_ASSOC)) {
                $calificaciones[$row['id_proveedor']][$row['id_criterio']] = $row['calificacion_evaluacion'];
            }

            $proveedores = [];
            while ($row = $resultProveedores->fetch(PDO::FETCH_ASSOC)) {
                $proveedores[$row['id_proveedor']] = $row['nombre_proveedor'];
            }

            $puntuaciones_por_categoria = [];
            foreach ($calificaciones as $idProveedor => $proveedorCalificaciones) {
                foreach ($proveedorCalificaciones as $idCriterio => $calificacion_evaluacion) {
                    $idCategoria = $criterios[$idCriterio]['id_categoria'];
                    $pesoCategoria = $categorias[$idCategoria]['peso'];
                    $pesoCriterio = $criterios[$idCriterio]['peso'];
                    $puntuacion = $calificacion_evaluacion * ($pesoCategoria * $pesoCriterio) / 10000;

                    if (!isset($puntuaciones_por_categoria[$idCategoria])) {
                        $puntuaciones_por_categoria[$idCategoria] = [];
                    }
                    if (!isset($puntuaciones_por_categoria[$idCategoria][$idProveedor])) {
                        $puntuaciones_por_categoria[$idCategoria][$idProveedor] = 0;
                    }
                    $puntuaciones_por_categoria[$idCategoria][$idProveedor] += $puntuacion;
                }
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
        
        <?php
        $consultaResultados = "SELECT e.id_proveedor, 
                    SUM(e.calificacion_evaluacion = 1) AS cantidad_1,
                    SUM(e.calificacion_evaluacion = 5) AS cantidad_5
                    FROM evaluacion e
                    INNER JOIN proveedor p ON e.id_proveedor = p.id_proveedor
                    WHERE e.id_proyecto = '$id_proyecto'
                    GROUP BY e.id_proveedor";
        $resultadoResultados = $conexion->query($consultaResultados);
        $datosResultados = array();
        while ($row = $resultadoResultados->fetch(PDO::FETCH_ASSOC)) {
            $datosResultados[$row['id_proveedor']] = array('cantidad_1' => $row['cantidad_1'], 'cantidad_5' => $row['cantidad_5']);
        }

        $consultaNombres = "SELECT id_proveedor, nombre_proveedor FROM proveedor";
        $resultadoNombres = $conexion->query($consultaNombres);
        $nombresProveedores = array();
        while ($row = $resultadoNombres->fetch(PDO::FETCH_ASSOC)) {
            $nombresProveedores[$row['id_proveedor']] = $row['nombre_proveedor'];
        }

        $puntuacionesPromedio = calcularPromedioPonderadoRanking($calificaciones, $categorias, $criterios);

        $puntuacionesPromedioOrdenadas = array();
        $puntuacionesPromedioOrdenadasId = array();
        arsort($puntuacionesPromedio);
        foreach ($puntuacionesPromedio as $idProveedor => $puntuacion) {
            $puntuacionesPromedioOrdenadas[$idProveedor] = $puntuacion;
            $puntuacionesPromedioOrdenadasId[] = $idProveedor;
        }

        $puntuaciones_por_proveedor_categorias = [];
        foreach ($calificaciones as $idProveedor => $proveedorCalificaciones) {
            foreach ($proveedorCalificaciones as $idCriterio => $calificacion_evaluacion) {
                $idCategoria = $criterios[$idCriterio]['id_categoria'];
                $pesoCategoria = $categorias[$idCategoria]['peso'];
                $pesoCriterio = $criterios[$idCriterio]['peso'];
                $puntuacion = $calificacion_evaluacion * ($pesoCategoria * $pesoCriterio) / 10000;

                if (!isset($puntuaciones_por_proveedor_categorias[$idProveedor])) {
                    $puntuaciones_por_proveedor_categorias[$idProveedor] = [];
                }
                if (!isset($puntuaciones_por_proveedor_categorias[$idProveedor][$idCategoria])) {
                    $puntuaciones_por_proveedor_categorias[$idProveedor][$idCategoria] = 0;
                }
                $puntuaciones_por_proveedor_categorias[$idProveedor][$idCategoria] += $puntuacion;
            }
        }
        ?>
    </div>
    <script>
        function mostrarInformacion() {
            var coloresColumnas = ['#0c1f5b', '#d3e5fd', '#0575E6', '#37B1D4', '#4C7EA1', '#4178BE', '#BBC9F5', '#237DF3', '#021B79'];

            function obtenerColorProveedor(proveedorIndex) {
                return coloresColumnas[proveedorIndex % coloresColumnas.length];
            }

            var boton = document.getElementById("mostrarInfo");
            var informacion = document.getElementById("informacion");

            if (informacion.style.display === "none") {
                informacion.style.display = "block";

                var ctxGeneral = document.getElementById('graficoGeneral').getContext('2d');
                var graficoGeneral = new Chart(ctxGeneral, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_values($proveedores)); ?>,
                        datasets: [{
                            label: 'Promedio Ponderado',
                            data: <?php echo json_encode(array_values($puntuaciones_por_proveedor)); ?>,
                            backgroundColor: function(context) {
                                const proveedorIndex = obtenerProveedorId(context.chart, context.dataIndex);
                                return obtenerColorProveedor(proveedorIndex);
                            },
                            borderColor: '#0c1f5b',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 5
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '<?php echo $titulos_graficas["graficoGeneral"]; ?>',
                                font: {
                                    size: 20
                                }
                            },
                            legend: {
                                labels: {
                                    generateLabels: function(chart) {
                                        var data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map(function(label, i) {
                                                var meta = chart.getDatasetMeta(0);
                                                var ds = data.datasets[0];
                                                var style = meta.controller.getStyle(i);
                                                return {
                                                    text: label,
                                                    fillStyle: style.backgroundColor,
                                                    hidden: meta.hidden === null ? false : !meta.hidden[i],
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            }
                        }
                    }
                });

                var ctx = document.getElementById('grafico').getContext('2d');
                var grafico = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_values($proveedores)); ?>,
                        datasets: [
                            <?php
                            $coloresColumnas = ['#0c1f5b', '#d3e5fd', '#364867', '#4178BE', '#BBC9F5', '#237DF3', '#4C7EA1'];
                            $i = 0;
                            foreach ($puntuaciones_por_categoria as $id_categoria => $puntuaciones_proveedores) {
                                echo "{";
                                echo "label: '" . $categorias[$id_categoria]['nombre'] . "',";
                                echo "data: [";
                                foreach ($puntuaciones_proveedores as $id_proveedor => $puntuacion) {
                                    echo $puntuacion . ",";
                                }
                                echo "],";
                                echo "backgroundColor: '" . $coloresColumnas[$i % count($coloresColumnas)] . "',";
                                echo "stack: 'Stack 1',";
                                echo "},";
                                $i++;
                            }
                            ?>
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 5
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '<?php echo $titulos_graficas["grafico"]; ?>',
                                font: {
                                    size: 20
                                }
                            }
                        }
                    }
                });

                var ctxResultados = document.getElementById('graficoResultados').getContext('2d');
                var graficoResultados = new Chart(ctxResultados, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_values($proveedores)); ?>,
                        datasets: [{
                                label: 'Cantidad de calificaciones 1',
                                data: <?php echo json_encode(array_column($datosResultados, 'cantidad_1')); ?>,
                                backgroundColor: coloresColumnas[1],
                                borderColor: '#0575E6',
                                borderWidth: 1
                            },
                            {
                                label: 'Cantidad de calificaciones 5',
                                data: <?php echo json_encode(array_column($datosResultados, 'cantidad_5')); ?>,
                                backgroundColor: coloresColumnas[0],
                                borderColor: '#021B79',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Conteo de Bajos (1) y Altos (5) Para todos los evaluadores y criterios',
                                    suggestedMax: 5
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '<?php echo $titulos_graficas["graficoResultados"]; ?>',
                                font: {
                                    size: 20
                                }
                            }
                        }
                    }
                });

                const puntuacionesPromedio = <?php echo json_encode(array_values($puntuacionesPromedio)); ?>;
                const nombresProveedores = <?php echo json_encode(array_map(function ($id) use ($nombresProveedores) {
                    return $nombresProveedores[$id];
                }, array_keys($puntuacionesPromedio))); ?>;

                const ctxRanking = document.getElementById('graficoRanking').getContext('2d');
                const graficoRanking = new Chart(ctxRanking, {
                    type: 'bar',
                    data: {
                        labels: nombresProveedores,
                        datasets: [{
                            label: 'Ranking',
                            data: puntuacionesPromedio,
                            backgroundColor: function(context) {
                                const proveedorIndex = obtenerProveedorId(context.chart, context.dataIndex);
                                return obtenerColorProveedor(proveedorIndex);
                            },
                            borderColor: '#0c1f5b',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: 5
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '<?php echo $titulos_graficas["graficoRanking"]; ?>',
                                font: {
                                    size: 20
                                }
                            }
                        }
                    }
                });

                var ctxRadar = document.getElementById('graficoRadar').getContext('2d');
                var datosRadar = {
                    labels: <?php echo json_encode(array_column($categorias, 'nombre')); ?>,
                    datasets: [
                        <?php
                        $coloresColumnas = ['#0c1f5b', '#d3e5fd', '#364867', '#4178BE', '#BBC9F5', '#237DF3', '#4C7EA1'];
                        $i = 0;
                        foreach ($puntuaciones_por_proveedor_categorias as $id_proveedor => $puntuaciones_categorias) {
                            echo "{";
                            echo "label: '" . $proveedores[$id_proveedor] . "',";
                            echo "data: [";
                            foreach ($puntuaciones_categorias as $id_categoria => $puntuacion) {
                                echo $puntuacion . ",";
                            }
                            echo "],";
                            echo "borderColor: '" . $coloresColumnas[$i % count($coloresColumnas)] . "',";
                            echo "pointBackgroundColor: '" . $coloresColumnas[$i % count($coloresColumnas)] . "',";
                            echo "backgroundColor: 'rgba(0, 0, 0, 0)'";
                            echo "},";
                            $i++;
                        }
                        ?>
                    ]
                };
                var opcionesRadar = {
                    scales: {
                        r: {
                            type: 'radialLinear',
                            ticks: {
                                beginAtZero: true,
                                auto: true
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: '<?php echo $titulos_graficas["graficorRadar"]; ?>',
                            font: {
                                size: 20
                            }
                        }
                    }
                };
                var graficoRadar = new Chart(ctxRadar, {
                    type: 'radar',
                    data: datosRadar,
                    options: opcionesRadar
                });
            } else {
                informacion.style.display = "none";
            }
        }

        function obtenerProveedorId(chart, dataIndex) {
            var proveedores = <?php echo json_encode(array_values($proveedores)); ?>;
            var proveedorLabel = chart.data.labels[dataIndex];
            var proveedorIndex = proveedores.indexOf(proveedorLabel);
            return proveedorIndex !== -1 ? proveedorIndex + 1 : null;
        }

        function descargarInformacion() {
            var informacion = document.getElementById("informacion");
            informacion.style.display = "block";
            window.print();
            informacion.style.display = "none";
        }
    </script>
</body>

</html>

<?php } ?>