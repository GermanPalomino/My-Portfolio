<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-building fa-fw"></i> &nbsp; LISTA DE CLIENTES
    </h3>
</div>

<?php 
// Verificar si hay un cliente seleccionado en la sesión
if (isset($_SESSION['datos_cliente']) && isset($_SESSION['datos_cliente'][0]['NombreCliente'])) {
    $nombreCliente = $_SESSION['datos_cliente'][0]['NombreCliente'];
    echo "<h4 class=\"centrar-texto\">Se encuentra seleccionado el cliente <strong>$nombreCliente</strong></h4>";
} else {
    echo "<h4>No hay un cliente seleccionado, debe seleccionar uno</h4>";
}
?>

<!-- Navegación de pestañas -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo cliente -->
        <li>
            <a href="<?php echo SERVERURL; ?>client-new/">
                <i class="fas fa-building fa-fw"></i> &nbsp; CREAR CLIENTE
            </a>
        </li>
        <!-- Enlace para ver la lista de clientes -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>client-list/">
                <i class="fas fa-building fa-fw"></i> &nbsp; LISTA DE CLIENTES
            </a>
        </li>
        <!-- Comentado: Enlace para buscar un cliente -->
        <!--
        <li>
            <a href="<?php echo SERVERURL; ?>client-search/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR CLIENTE
            </a>
        </li>
        -->
    </ul>
</div>

<!-- Contenedor principal -->
<div class="container-fluid">
    <?php
    // Incluir el controlador de clientes
    require_once "./controladores/clienteControlador.php";
    $ins_cliente = new clienteControlador();

    // Mostrar el paginador de clientes
    echo $ins_cliente->paginador_cliente_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
    ?>
</div>