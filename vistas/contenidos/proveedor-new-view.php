<?php
    // Verificar si el usuario tiene privilegios suficientes
    if($_SESSION['privilegio_xcoring'] != 1){
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
?>
<div class="full-box page-header">
    <!-- Encabezado de la página con el título y el ícono -->
    <h3 class="text-left">
        <i class="fas fa-industry fa-fw"></i> &nbsp; CREAR PROVEEDOR
    </h3>
</div>

<div class="container-fluid">
    <!-- Barra de navegación para diferentes acciones relacionadas con proveedores -->
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo proveedor, marcado como activo -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>proveedor-new/"><i class="fas fa-industry fa-fw"></i> &nbsp; CREAR PROVEEDOR</a>
        </li>
        <!-- Enlace para listar los proveedores -->
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-list/"><i class="fas fa-industry fa-fw"></i> &nbsp; LISTA DE PROVEEDORES</a>
        </li>
    </ul>    
</div>

<div class="container-fluid">
    <!-- Formulario para crear un nuevo proveedor -->
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/proveedorAjax.php" method="POST" data-form="save" enctype="multipart/form-data" autocomplete="off">
        <fieldset>
            <!-- Leyenda y campo de entrada para la información del proveedor -->
            <legend><i class="fas fa-industry fa-fw"></i> &nbsp; Información del proveedor</legend><br>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo de entrada para el nombre del proveedor -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_proveedor" class="bmd-label-floating">Nombre Proveedor</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,40}" class="form-control" name="nombre_proveedor_reg" id="nombre_proveedor" maxlength="40" required="">
                        </div>
                    </div>
                    <!-- Campo de entrada para el nombre del contacto del proveedor -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombreContacto_proveedor" class="bmd-label-floating">Nombre Contacto</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="nombreContacto_proveedor_reg" id="nombreContacto_proveedor" maxlength="40" required="">
                        </div>
                    </div>
                    <!-- Campos de entrada para la información de contacto del proveedor -->
                    <div class="col-md-6">
                        <fieldset>
                            <legend>Información de contacto</legend>
                            <div class="form-group">
                                <label for="emailContacto_proveedor" class="bmd-label-floating">Email</label>
                                <input type="email" class="form-control" name="emailContacto_proveedor_reg" id="emailContacto_proveedor" maxlength="70" pattern="[a-zA-Z0-9$@.-]{7,100}" required="">
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset>
                            <legend><br></legend>
                            <div class="form-group">
                                <label for="telefonoContacto_proveedor" class="bmd-label-floating">Teléfono</label>
                                <input type="text" pattern="[0-9\+()]{8,20}" class="form-control" name="telefonoContacto_proveedor_reg" id="telefonoContacto_proveedor" maxlength="20" required="">
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </fieldset>
        <br><br><br>
        <!-- Botones para limpiar el formulario y para enviar los datos -->
        <p class="text-center" style="margin-top: 40px;">
            <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
            &nbsp; &nbsp;
            <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
        </p>
    </form>
</div>