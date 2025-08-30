<?php
    // Verificar privilegios del usuario
    if($_SESSION['privilegio_xcoring']<1 || $_SESSION['privilegio_xcoring']>2){
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }

    // Conexión a la base de datos
    $link = mysqli_connect("localhost","root","");
    if($link){
        mysqli_select_db($link,"xcoring");
    }
?>

<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-building fa-fw"></i> &nbsp; ACTUALIZAR CLIENTE
    </h3>
</div>

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
            <a href="<?php echo SERVERURL; ?>client-list/">
                <i class="fas fa-building fa-fw"></i> &nbsp; LISTA DE CLIENTES
            </a>
        </li>
    </ul>
</div>

<!-- Formulario para actualizar un cliente -->
<div class="container-fluid">
    <?php
        // Incluir el controlador de clientes
        require_once "./controladores/clienteControlador.php";
        $ins_cliente = new clienteControlador();

        // Obtener los datos del cliente
        $datos_cliente = $ins_cliente->datos_cliente_controlador("Unico", $pagina[1]);

        // Verificar si se encontró el cliente
        if($datos_cliente->rowCount() == 1){
            $campos = $datos_cliente->fetch();
    ?>
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/clienteAjax.php" method="POST" data-form="update" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="cliente_id_up" value="<?php echo $pagina[1]; ?>">
        <fieldset>
            <legend><i class="far fa-plus-square"></i> &nbsp; Información del cliente</legend><br>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo para el nombre del cliente -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_cliente" class="bmd-label-floating">Nombre Cliente</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="nombre_cliente_up" value="<?php echo $campos['nombre_cliente']; ?>" id="nombre_cliente" maxlength="40">
                        </div>
                    </div>
                    <!-- Campo para el nombre del contacto -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombreContacto_cliente" class="bmd-label-floating">Nombre Contacto</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="nombreContacto_cliente_up" value="<?php echo $campos['nombreContacto_cliente']; ?>" id="nombreContacto_cliente" maxlength="40">
                        </div>
                    </div>
                    <!-- Información de contacto -->
                    <div class="col-md-6">
                        <fieldset>
                            <legend>Información de contacto</legend>
                            <!-- Campo para el teléfono del contacto -->
                            <div class="form-group">
                                <label for="telefonoContacto_cliente" class="bmd-label-floating">Teléfono</label>
                                <input type="text" pattern="[0-9\+()]{8,20}" class="form-control" name="telefonoContacto_cliente_up" value="<?php echo $campos['telefonoContacto_cliente']; ?>" id="telefonoContacto_cliente" maxlength="20">
                            </div>
                        </fieldset>
                    </div>
                    <!-- Campo para el email del contacto -->
                    <div class="col-md-6">
                        <fieldset>
                            <legend><br></legend>
                            <div class="form-group">
                                <label for="emailContacto_cliente" class="bmd-label-floating">Email</label>
                                <input type="email" pattern="[a-zA-Z0-9$@.-]{7,100}" class="form-control" name="emailContacto_cliente_up" value="<?php echo $campos['emailContacto_cliente']; ?>" id="emailContacto_cliente" maxlength="150">
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </fieldset>
        <br><br><br>
        <!-- Botón para actualizar -->
        <p class="text-center" style="margin-top: 40px;">
            <button type="submit" class="btn btn-raised btn-success btn-sm">
                <i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR
            </button>
        </p>
    </form>
    <?php } else { ?>
    <!-- Mensaje de error si no se encontró el cliente -->
    <div class="alert alert-danger text-center" role="alert">
        <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
        <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
        <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
    </div>
    <?php } ?>
</div>