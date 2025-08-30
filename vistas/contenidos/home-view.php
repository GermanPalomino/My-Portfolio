<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración de los metadatos del documento -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Enlace a la hoja de estilo CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo SERVERURL; ?>vistas/css/style.css">
</head>
<body>
    <!-- Contenedor principal para el encabezado de la página -->
    <div class="full-box page-header">
        <!-- Título de la página con un ícono -->
        <h2 class="text-left">
             
        </h2>
        <!-- Descripción del software -->
        <p class="text-justify">
            <b>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer sed gravida lorem. Maecenas vitae nunc eu arcu bibendum accumsan.</b>
        </p>
        <p class="text-justify">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur mattis, orci sed facilisis iaculis, arcu mi fringilla lacus, ut interdum urna lacus non tellus. Phasellus at nibh non neque fermentum lacinia.
        </p>
        <p class="text-justify">
            Praesent luctus, nisl vitae porttitor pretium, mauris libero porttitor justo, a tincidunt urna lorem non odio. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Integer sed eros non risus interdum sagittis.
        </p>
        <p class="text-justify">
            Cras euismod, sem at posuere finibus, nibh lorem dapibus neque, ac condimentum magna elit sed enim. Vivamus dictum, ipsum at vulputate finibus, arcu purus finibus mi, a iaculis arcu dolor at purus.
        </p>
        <p class="text-justify">
            Sed ullamcorper, mi nec iaculis viverra, nisl quam viverra velit, ac laoreet neque justo eget nisl. Aliquam at sagittis lorem. Duis vehicula, ipsum id laoreet mattis, lectus sapien luctus dui, vitae egestas nisl ex vitae risus.
        </p>

        <!-- Lógica PHP para mostrar el botón adecuado -->
        <?php
        // Incluye el modelo principal
        require_once "./modelos/mainModel.php";

        // Verificar si $_SESSION['datos_cliente'][0]['IDCliente'] está definida y no está vacía
        if (!isset($_SESSION['datos_cliente'][0]['IDCliente']) || empty($_SESSION['datos_cliente'][0]['IDCliente'])) {
            // Mostrar párrafo y botón para redirigir a la lista de clientes si no está definido
            echo '<p class="text-justify"><b> ¿Quieres ver la lista de clientes existentes o crear nuevos? ¡Haz clic en el botón de abajo para comenzar!</b></p>';
            echo '<a href="' . SERVERURL . 'client-list/" class="btn btn-primary btn-custom"> <b>Ir a lista de clientes</b></a>';
        } else {
            if (!isset($_SESSION['datos_proyecto'][0]['IDProyecto']) || empty($_SESSION['datos_proyecto'][0]['IDProyecto'])) {
                // Mostrar párrafo si no hay proyecto iniciado
                echo '<p class="text-justify"><b> ¿Quieres ver la lista de proyectos existentes o crear nuevos? ¡Haz clic en el botón de abajo para comenzar!</b></p>';
                echo '<a href="' . SERVERURL . 'proyecto-list/" class="btn btn-primary btn-custom"> <b>Ir a lista de proyectos</b></a>';
            } else {
                // Verificar si el IDProyecto está en la tabla evaluacion
                $idProyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
                $conexion = new PDO('mysql:host=localhost;dbname=xcoring', 'root', ''); // Ajusta los parámetros de conexión según tu configuración
                $consultaEvaluacion = "SELECT COUNT(*) as count FROM evaluacion WHERE id_proyecto = :idProyecto";
                $stmt = $conexion->prepare($consultaEvaluacion);
                $stmt->bindParam(':idProyecto', $idProyecto, PDO::PARAM_INT);
                $stmt->execute();
                $resultEvaluacion = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($resultEvaluacion['count'] > 0) {
                    // Si está en la tabla evaluacion, redirigir a evaluacion-list
                    $urlEvaluacion = SERVERURL . 'evaluacion-list/';
                } else {
                    // Si no está en la tabla evaluacion, redirigir a evaluacion-new
                    $urlEvaluacion = SERVERURL . 'evaluacion-new/';
                }
                echo '<p class="text-justify"><b> ¡Ya puedes gestionar tus categorías, criterios, proveedores y evaluadores desde este apartado!</b></p>';
                echo '<a href="' . $urlEvaluacion . '" class="btn btn-primary btn-custom"> <b>Ir a gestion</b></a>';
            }
        }
        ?>
    </div>
</body>
</html>