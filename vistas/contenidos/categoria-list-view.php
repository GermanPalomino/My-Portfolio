<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-list fa-fw"></i> &nbsp; LISTA DE CATEGORIAS
    </h3>
</div>

<!-- Navegación de pestañas -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear una nueva categoría -->
        <li>
            <a href="<?php echo SERVERURL; ?>categoria-new/">
                <i class="fas fa-list fa-fw"></i> &nbsp; CREAR CATEGORIA
            </a>
        </li>
        <!-- Enlace para ver la lista de categorías (activo) -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>categoria-list/">
                <i class="fas fa-list fa-fw"></i> &nbsp; LISTA DE CATEGORIAS
            </a>
        </li>
        <!-- Comentado: Enlace para buscar una categoría -->
        <!--
        <li>
            <a href="<?php echo SERVERURL; ?>categoria-search/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR CATEGORIA
            </a>
        </li>
        -->
    </ul>
</div>

<!-- Contenido principal: lista de categorías -->
<div class="container-fluid">
    <?php
        // Incluir el controlador de categorías
        require_once "./controladores/categoriaControlador.php";
        $ins_item = new categoriaControlador();
        // Mostrar el paginador de categorías
        echo $ins_item->paginador_categoria_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
    ?>
</div>
