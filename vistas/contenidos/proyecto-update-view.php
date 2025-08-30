<?php
    // Verificar si el usuario tiene los privilegios necesarios para acceder a esta página
    if ($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2) {
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
?>

<div class="full-box page-header">
    <!-- Encabezado de la página con el título y el ícono -->
    <h3 class="text-left">
        <i class="fas fa-folder fa-fw"></i> &nbsp; ACTUALIZAR PROYECTO
    </h3>
</div>

<div class="container-fluid">
    <!-- Barra de navegación para diferentes acciones relacionadas con proyectos -->
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo proyecto -->
        <li>
            <a href="<?php echo SERVERURL; ?>proyecto-new/"><i class="fas fa-folder fa-fw"></i> &nbsp; CREAR PROYECTO</a>
        </li>
        <!-- Enlace para listar los proyectos -->
        <li>
            <a href="<?php echo SERVERURL; ?>proyecto-list/"><i class="fas fa-folder fa-fw"></i> &nbsp; LISTA DE PROYECTOS</a>
        </li>
    </ul>    
</div>    

<div class="container-fluid">
    <?php
        // Incluir el controlador de proyecto
        require_once "./controladores/proyectoControlador.php";
        $ins_proyecto = new proyectoControlador();

        // Obtener los datos del proyecto a actualizar
        $datos_proyecto = $ins_proyecto->datos_proyecto_controlador("Unico", $pagina[1]);

        if ($datos_proyecto->rowCount() == 1) {
            $campos = $datos_proyecto->fetch();
            $roles_proyecto = $ins_proyecto->obtener_roles_proyecto_controlador($campos['id_proyecto']);
    ?>
    <!-- Formulario para actualizar un proyecto -->
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/proyectoAjax.php" method="POST" data-form="update" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="proyecto_id_up" value="<?php echo $pagina[1]; ?>">
        <fieldset>
            <!-- Leyenda del formulario -->
            <legend><i class="fas fa-folder"></i> &nbsp; Información del proyecto</legend>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo de entrada para el nombre del proyecto -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="proyecto_nombre" class="bmd-label-floating">Nombre</label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="proyecto_nombre_up" value="<?php echo $campos['nombre_proyecto']; ?>" id="proyecto_nombre" maxlength="40">
                        </div>
                    </div>
                    <!-- Campo de entrada para el email de contacto del proyecto -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="emailContacto_proyecto" class="bmd-label-floating">Email</label>
                            <input type="email" pattern="[a-zA-Z0-9$@.-]{7,100}" class="form-control" name="emailContacto_proyecto_up" value="<?php echo $campos['emailContacto_proyecto']; ?>" id="emailContacto_proyecto" maxlength="150">
                        </div>
                    </div>
                    <!-- Campo de entrada para definir roles del proyecto -->
                    <legend><i class="fas fa-folder fa-fw"></i> &nbsp; Roles del proyecto</legend>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="roles_proyecto" class="bmd-label-floating">Roles del Proyecto</label>
                            <div id="roles-container">
                                <?php
                                if (!empty($roles_proyecto)) {
                                    foreach ($roles_proyecto as $rol) {
                                        echo '<input type="text" class="form-control" name="roles_proyecto[]" value="' . $rol['nombre_rol'] . '" maxlength="50" required>';
                                    }
                                } else {
                                    echo '<input type="text" class="form-control" name="roles_proyecto[]" maxlength="50" required>';
                                }
                                ?>
                            </div>
                            <button type="button" class="btn-add-role" onclick="agregarRol()">Agregar otro rol</button>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <br><br><br>
        <!-- Botón para actualizar el proyecto -->
        <p class="text-center" style="margin-top: 40px;">
            <button type="submit" class="btn btn-raised btn-success btn-sm"><i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR</button>
        </p>
    </form>
    <?php } else { ?>
    <!-- Mensaje de error si no se puede mostrar la información del proyecto -->
    <div class="alert alert-danger text-center" role="alert">
        <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
        <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
        <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
    </div>
    <?php } ?>
</div>

<!-- Estilos CSS -->
<style>
    .btn-add-role {
        background-color: #364867;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        margin-top: 10px; /* Añade un margen superior para separar del input anterior */
    }

    .btn-add-role:hover {
        background-color: #218838;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-add-role:active {
        background-color: #1e7e34;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .btn-add-role:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
    }
</style>

<script>
function agregarRol() {
    var container = document.getElementById('roles-container');
    var input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.name = 'roles_proyecto[]';
    input.placeholder = 'Ingrese un rol';
    input.maxLength = 50;
    input.required = true;
    container.appendChild(input);
}
</script>
