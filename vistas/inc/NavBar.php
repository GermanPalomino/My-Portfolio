<?php
    // Instanciar el controlador de login
    require_once "./controladores/loginControlador.php";

    // Obtener la página actual en minúsculas
    $pagina_actual = strtolower(basename($_SERVER['REQUEST_URI']));

    // Crear una instancia del controlador de login
    $controlador = new loginControlador();

    // Redireccionar si faltan datos necesarios para la sesión
    $controlador->redireccionar_si_faltan_datos_controlador($pagina_actual);
?>

<!-- Navegación superior -->
<nav class="full-box navbar-info">
    <a href="#" class="float-left show-nav-lateral">
        <i class="fas fa-exchange-alt"></i>
    </a>
    <span>CLIENTE:</span>
    <!-- Verificar si hay datos del cliente en la sesión -->
    <?php if(!empty($_SESSION['datos_cliente']) && is_array($_SESSION['datos_cliente'])) { ?>
        <!-- Mostrar cada cliente y un botón para eliminarlo -->
        <?php foreach($_SESSION['datos_cliente'] as $cliente) { ?>
            <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/evaluacionAjax.php" method="POST" data-form="loans" enctype="multipart/form-data" autocomplete="off" style="display: inline-block !important;">
                <?php echo $cliente['NombreCliente']; ?>
                <input type="hidden" name="id_eliminar_cliente" value="<?php echo $cliente['IDCliente'];?>">
                <button type="submit" class="btn btn-dark" data-confirm="true"><i class="fas fa-user-times"></i></button>
            </form>
        <?php } ?>
    <?php } else { ?> 
        <!-- Botón para seleccionar un cliente si no hay ninguno seleccionado -->
        <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#ModalCliente"><i class="fas fa-building"></i> &nbsp; Seleccionar Cliente</button>
    <?php } ?>
    
    <!-- Verificar si hay datos del proyecto en la sesión -->
    <?php if(isset($_SESSION['datos_cliente'])){?>
        <span>PROYECTO:</span>
        <?php if(!empty($_SESSION['datos_proyecto']) && is_array($_SESSION['datos_proyecto'])) { ?>
            <!-- Mostrar cada proyecto y un botón para eliminarlo -->
            <?php foreach($_SESSION['datos_proyecto'] as $cliente) { ?>
                <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/evaluacionAjax.php" method="POST" data-form="loans" enctype="multipart/form-data" autocomplete="off" style="display: inline-block !important;">
                    <?php echo $cliente['NombreProyecto']; ?>
                    <input type="hidden" name="id_eliminar_proyecto" value="<?php echo $cliente['IDProyecto'];?>">
                    <button type="submit" class="btn btn-dark" data-confirm="true"><i class="fas fa-user-times"></i></button>
                </form>
            <?php } ?>
        <?php } else { ?> 
            <!-- Botón para seleccionar un proyecto si no hay ninguno seleccionado -->
            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#ModalProyecto"><i class="fas fa-building"></i> &nbsp; Seleccionar Proyecto</button>
        <?php } ?>
    <?php } ?>
    
    <!-- Enlace para actualizar el perfil del usuario -->
    <a href="<?php echo SERVERURL."user-update/".$lc->encryption($_SESSION['id_xcoring'])."/"; ?>">
        <i class="fas fa-user-cog"></i>
    </a>
    <!-- Botón para salir del sistema -->
    <a href="#" class="btn-exit-system">
        <i class="fas fa-power-off"></i>
    </a>
</nav>

<!-- Modal para seleccionar cliente -->
<div class="modal fade" id="ModalCliente" tabindex="-1" role="dialog" aria-labelledby="ModalClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalClienteLabel">Seleccionar Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="select_cliente" class="bmd-label-floating">Cliente</label>
                        <select class="form-control" id="select_cliente">
                            <option value="">Selecciona un cliente</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Botón para agregar el cliente seleccionado -->
                <button type="button" class="btn btn-primary" onclick="agregar_cliente()"><i class="fas fa-building"></i> Seleccionar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar proyecto -->
<div class="modal fade" id="ModalProyecto" tabindex="-1" role="dialog" aria-labelledby="ModalProyectoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalProyectoLabel">Seleccionar Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="select_proyecto" class="bmd-label-floating">Proyecto</label>
                        <select class="form-control" id="select_proyecto">
                            <option value="">Selecciona un proyecto</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Botón para agregar el proyecto seleccionado -->
                <button type="button" class="btn btn-primary" onclick="agregar_proyecto()"><i class="fas fa-folder"></i> Seleccionar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "./vistas/inc/Evaluacion.php"; ?>