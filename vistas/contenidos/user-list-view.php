<?php
    // Verificar si el usuario tiene privilegios para acceder a esta página
    if ($_SESSION['privilegio_xcoring'] != 1) {
        // Forzar el cierre de sesión si no tiene los privilegios adecuados
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
?>

<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-user fa-fw"></i> &nbsp; LISTA DE USUARIOS
    </h3>
</div>

<!-- Barra de navegación con enlaces para diferentes acciones relacionadas con usuarios -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <!-- Enlace para crear un nuevo usuario -->
            <a href="<?php echo SERVERURL; ?>user-new/"><i class="fas fa-user fa-fw"></i> &nbsp; NUEVO USUARIO</a>
        </li>
        <li>
            <!-- Enlace activo para listar los usuarios -->
            <a class="active" href="<?php echo SERVERURL; ?>user-list/"><i class="fas fa-user fa-fw"></i> &nbsp; LISTA DE USUARIOS</a>
        </li>
        <li>
            <!-- Enlace comentado para buscar usuarios (actualmente no disponible) -->
            <!--<a href="<?php echo SERVERURL; ?>user-search/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR USUARIO</a>-->
        </li>
    </ul>
</div>

<!-- Contenedor para el contenido principal de la página -->
<div class="container-fluid">
    <?php
        // Incluir el controlador de usuario
        require_once "./controladores/usuarioControlador.php";
        // Crear una instancia del controlador de usuario
        $ins_usuario = new usuarioControlador();

        // Llamar al método para paginar y mostrar la lista de usuarios
        // $pagina[1] -> Página actual
        // 15 -> Número de usuarios por página
        // $_SESSION['privilegio_xcoring'] -> Privilegios del usuario actual
        // $_SESSION['id_xcoring'] -> ID del usuario actual
        // $pagina[0] -> Parámetro adicional para el paginador (puede ser una búsqueda, etc.)
        echo $ins_usuario->paginador_usuario_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $_SESSION['id_xcoring'], $pagina[0], "");
    ?>
</div>