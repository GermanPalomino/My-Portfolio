<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xcoring";

// Crear conexión con la base de datos
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión a la base de datos
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verificar si hay datos de proyecto en la sesión
if(isset($_SESSION['datos_proyecto'])) {
    // Obtener el ID del proyecto de la sesión
    $IDProyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];

    // Consultar si existe una evaluación asociada a ese proyecto
    $query = "SELECT id_proyecto FROM evaluacion WHERE id_proyecto = $IDProyecto";
    $resultado = $conexion->query($query);

    // Verificar si hay resultados en la consulta
    if($resultado->num_rows > 0) {
        // Si hay resultados, dirigir al usuario a evaluacion-list
        $url = "evaluacion-list/";
    } else {
        // Si no hay resultados, dirigir al usuario a evaluacion-new
        $url = "evaluacion-new/";
    }
} else {
    // Si no hay datos de proyecto en la sesión, dirigir al usuario a home
    $url = "home/";
}
?>

<!-- Sección de navegación lateral -->
<section class="full-box nav-lateral">
    <div class="full-box nav-lateral-bg show-nav-lateral"></div>
    <div class="full-box nav-lateral-content">
        <!-- Avatar y nombre del usuario -->
        <figure class="full-box nav-lateral-avatar">
            <figcaption class="text-center">
                <p class="text-center"><b>¡Bienvenido!</b></p>
                <?php echo $_SESSION['nombre_xcoring']; ?> <br><small><?php echo "(".$_SESSION['email_xcoring'].")"; ?></small>
            </figcaption>
        </figure>
        <div class="full-box nav-lateral-bar"></div>
        <!-- Menú de navegación lateral -->
        <nav class="full-box nav-lateral-menu">
            <ul>
                <li>
                    <a href="<?php echo SERVERURL; ?>home/"><i class="fab fa-dashcube fa-fw"></i> &nbsp; Home</a>
                </li>
                <li>
                    <a href="#" class="nav-btn-submenu" id="cliente-link"><i class="fas fa-building fa-fw"></i> &nbsp; Cliente <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('cliente-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL; ?>client-list/"; 
                    });
                </script>
                <!-- Verificar si hay datos de cliente en la sesión -->
                <?php if(isset($_SESSION['datos_cliente'])){ ?>
                <li>
                    <a href="#" class="nav-btn-submenu" id="proyecto-link"><i class="fas fa-folder fa-fw"></i> &nbsp; Proyecto <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('proyecto-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL; ?>proyecto-list/"; 
                    });
                </script>
                <?php } ?>
                <!-- Verificar si hay datos de proyecto en la sesión -->
                <?php if(isset($_SESSION['datos_proyecto'])){ ?>
                <li>
                    <a href="#" class="nav-btn-submenu" id="categoria-link"><i class="fas fa-list fa-fw"></i> &nbsp; Categorias <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('categoria-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL; ?>categoria-list/"; 
                    });
                </script>
                <li>
                    <a href="#" class="nav-btn-submenu" id="criterios-link"><i class="fas fa-table fa-fw"></i> &nbsp; Criterios <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('criterios-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL; ?>criterio-list/"; 
                    });
                </script>
                <li>
                    <a href="#" class="nav-btn-submenu" id="proveedor-link"><i class="fas fa-industry fa-fw"></i> &nbsp; Proveedor <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('proveedor-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL; ?>proveedor-list/"; 
                    });
                </script>
                <!-- Verificar si el usuario tiene privilegios de administrador -->
                <?php if($_SESSION['privilegio_xcoring']==1){ ?>
                <li>
                    <a href="#" class="nav-btn-submenu" id="evaluadores-link"><i class="fas fa-user fa-fw"></i> &nbsp; Evaluadores <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('evaluadores-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL; ?>user-list/";
                    });
                </script>
                <?php 
                    }

                    // Verificar si el usuario tiene privilegios de administrador o edición
                    if($_SESSION['privilegio_xcoring']==1 || $_SESSION['privilegio_xcoring']==2){
                ?>
                <li>
                    <a href="#" class="nav-btn-submenu" id="generar-link"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Estado <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('generar-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL . $url; ?>";
                    });
                </script>
                <li>
                    <a href="#" class="nav-btn-submenu" id="reportes-link"><i class="fas fa-chart-bar fa-fw"></i> &nbsp; Reportes <i class="fas fa-chevron-down"></i></a>
                </li>
                <script>
                    document.getElementById('reportes-link').addEventListener('click', function(event) {
                        event.preventDefault();
                        window.location.href = "<?php echo SERVERURL; ?>reporte-new/";
                    });
                </script>
                <?php } ?>
                <?php } ?>
            </ul>
        </nav>
    </div>
</section>