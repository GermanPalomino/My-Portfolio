<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-table fa-fw"></i> &nbsp; LISTA DE CRITERIOS
    </h3>
</div>

<!-- Navegación de pestañas -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo criterio -->
        <li>
            <a href="<?php echo SERVERURL; ?>criterio-new/">
                <i class="fas fa-table fa-fw"></i> &nbsp; CREAR CRITERIO
            </a>
        </li>
        <!-- Enlace para ver la lista de criterios -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>criterio-list/">
                <i class="fas fa-table fa-fw"></i> &nbsp; LISTA DE CRITERIOS
            </a>
        </li>
        <!-- Enlace para buscar criterios (comentado) -->
        <li>
            <!--<a href="<?php echo SERVERURL; ?>criterio-search/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR CRITERIO
            </a>-->
        </li>
    </ul>
</div>

<!-- Contenedor principal -->
<div class="container-fluid">
    <?php
        // Incluir el controlador de criterios
        require_once "./controladores/criterioControlador.php";
        $ins_criterio = new criterioControlador();

        // Mostrar la paginación de criterios
        echo $ins_criterio->paginador_criterio_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
    ?>
</div>
