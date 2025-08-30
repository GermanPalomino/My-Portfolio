<?php
    // Verificar si el usuario tiene los privilegios necesarios para acceder a esta página
    if($_SESSION['privilegio_xcoring'] != 1){
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
?>
<div class="full-box page-header">
    <!-- Encabezado de la página con el título y el ícono -->
    <h3 class="text-left">
        <i class="fas fa-folder fa-fw"></i> &nbsp; LISTA DE PROYECTOS
    </h3>
</div>

<?php 
    // Verificar si hay un proyecto seleccionado y mostrar su nombre
    if (isset($_SESSION['datos_proyecto']) && isset($_SESSION['datos_proyecto'][0]['NombreProyecto'])) {
        $nombreProyecto = $_SESSION['datos_proyecto'][0]['NombreProyecto'];
        echo "<h4 class=\"centrar-texto\">Se encuentra seleccionado el proyecto <strong>$nombreProyecto</strong></h4>";
    } else {
        echo "<h4>No hay un proyecto seleccionado, debe seleccionar uno</h4>";
    }
?>

<div class="container-fluid">
    <!-- Barra de navegación para diferentes acciones relacionadas con proyectos -->
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo proyecto -->
        <li>
            <a href="<?php echo SERVERURL; ?>proyecto-new/"><i class="fas fa-folder fa-fw"></i> &nbsp; CREAR PROYECTO</a>
        </li>
        <!-- Enlace para listar los proyectos -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>proyecto-list/"><i class="fas fa-folder fa-fw"></i> &nbsp; LISTA DE PROYECTOS</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
        // Incluir el controlador de proyectos
        require_once "./controladores/proyectoControlador.php";
        $ins_proyecto = new proyectoControlador();

        // Mostrar la lista de proyectos con paginación
        echo $ins_proyecto->paginador_proyecto_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
    ?>
</div>