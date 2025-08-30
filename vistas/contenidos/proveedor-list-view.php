<div class="full-box page-header">
    <!-- Encabezado de la página con el título y el ícono -->
    <h3 class="text-left">
        <i class="fas fa-industry fa-fw"></i> &nbsp; LISTA DE PROVEEDORES
    </h3>
</div>

<div class="container-fluid">
    <!-- Barra de navegación para diferentes acciones relacionadas con proveedores -->
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo proveedor -->
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-new/"><i class="fas fa-industry fa-fw"></i> &nbsp; CREAR PROVEEDOR</a>
        </li>
        <!-- Enlace para listar los proveedores, marcado como activo -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>proveedor-list/"><i class="fas fa-industry fa-fw"></i> &nbsp; LISTA DE PROVEEDORES</a>
        </li>
        <!-- Enlace comentado para buscar clientes (no está activo) -->
        <li>
            <!--<a href="<?php echo SERVERURL; ?>client-search/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR CLIENTE</a>-->
        </li>
    </ul>	
</div>

<div class="container-fluid">
    <?php
        // Incluir el controlador de proveedores
        require_once "./controladores/proveedorControlador.php";
        // Crear una instancia del controlador de proveedores
        $ins_proveedor = new proveedorControlador();
        // Mostrar la paginación de la lista de proveedores
        echo $ins_proveedor->paginador_proveedor_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
    ?>
</div>