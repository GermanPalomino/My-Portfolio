<?php
    // Verificar si el usuario tiene los privilegios necesarios para acceder a esta página
    if($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2){
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
?>
<div class="full-box page-header">
    <!-- Encabezado de la página con el título y el ícono -->
    <h3 class="text-left">
        <i class="fas fa-industry fa-fw"></i> &nbsp; ACTUALIZAR PROVEEDOR
    </h3>
</div>

<div class="container-fluid">
    <!-- Barra de navegación para diferentes acciones relacionadas con proveedores -->
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo proveedor -->
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-new/"><i class="fas fa-industry fa-fw"></i> &nbsp; CREAR PROVEEDOR</a>
        </li>
        <!-- Enlace para listar los proveedores -->
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-list/"><i class="fas fa-industry fa-fw"></i> &nbsp; LISTA DE PROVEEDORES</a>
        </li>
    </ul>    
</div>

<div class="container-fluid">
    <?php
        // Incluir el controlador de proveedores
        require_once "./controladores/proveedorControlador.php";
        $ins_proveedor = new proveedorControlador();

        // Obtener los datos del proveedor a actualizar
        $datos_proveedor = $ins_proveedor->datos_proveedor_controlador("Unico", $pagina[1]);

        // Verificar si se encontraron datos del proveedor
        if($datos_proveedor->rowCount() == 1){
            $campos = $datos_proveedor->fetch();
    ?>
    <!-- Formulario para actualizar el proveedor -->
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/proveedorAjax.php" method="POST" data-form="update" enctype="multipart/form-data" autocomplete="off" >
        <input type="hidden" name="proveedor_id_up" value="<?php echo $pagina[1]; ?>" >
        <fieldset>
            <!-- Leyenda y campo de entrada para la información del proveedor -->
            <legend><i class="fas fa-industry fa-fw"></i> &nbsp; Información del proveedor</legend><br>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo de entrada para el nombre del proveedor -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_proveedor" class="bmd-label-floating">Nombre Proveedor</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,40}" class="form-control" name="nombre_proveedor_up" value="<?php echo $campos['nombre_proveedor']; ?>" id="nombre_proveedor" maxlength="40">
                        </div>
                    </div>
                    <!-- Campo de entrada para el nombre del contacto del proveedor -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombreContacto_proveedor" class="bmd-label-floating">Nombre Contacto</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="nombreContacto_proveedor_up" value="<?php echo $campos['nombreContacto_proveedor']; ?>" id="nombreContacto_proveedor" maxlength="40">
                        </div>
                    </div>
                    <!-- Campo de entrada para el correo electrónico del contacto del proveedor -->
                    <div class="col-md-6">
                        <fieldset>
                            <legend>Información de contacto</legend>
                            <div class="form-group">
                                <label for="emailContacto_proveedor" class="bmd-label-floating">Email</label>
                                <input type="email" pattern="[a-zA-Z0-9$@.-]{7,100}" class="form-control" name="emailContacto_proveedor_up" value="<?php echo $campos['emailContacto_proveedor']; ?>" id="emailContacto_cliente" maxlength="150">
                            </div>
                        </fieldset>
                    </div>
                    <!-- Campo de entrada para el teléfono del contacto del proveedor -->
                    <div class="col-md-6">
                        <fieldset>
                            <legend><br></legend>
                            <div class="form-group">
                                <label for="telefonoContacto_proveedor" class="bmd-label-floating">Teléfono</label>
                                <input type="text" pattern="[0-9\+()]{8,20}" class="form-control" name="telefonoContacto_proveedor_up" value="<?php echo $campos['telefonoContacto_proveedor']; ?>" id="telefonoContacto_proveedor" maxlength="20">
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </fieldset>
        <br><br><br>
        <!-- Botón para actualizar la información del proveedor -->
        <p class="text-center" style="margin-top: 40px;">
            <button type="submit" class="btn btn-raised btn-success btn-sm"><i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR</button>
        </p>
    </form>
    <?php } else { ?>
    <!-- Mensaje de error en caso de que no se encuentren datos del proveedor -->
    <div class="alert alert-danger text-center" role="alert">
        <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
        <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
        <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
    </div>
    <?php } ?>
</div>