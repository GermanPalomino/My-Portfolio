<?php
    // Verificar si el usuario tiene privilegios de administrador (privilegio 1)
    if ($_SESSION['privilegio_xcoring'] != 1) {
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-user fa-fw"></i> &nbsp; CREAR EVALUADOR
    </h3>
</div>

<!-- Barra de navegación con enlaces para diferentes acciones relacionadas con evaluadores -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <!-- Enlace activo para crear un nuevo evaluador -->
            <a class="active" href="<?php echo SERVERURL; ?>user-new/"><i class="fas fa-user fa-fw"></i> &nbsp; CREAR EVALUADOR</a>
        </li>
        <li>
            <!-- Enlace para listar los evaluadores -->
            <a href="<?php echo SERVERURL; ?>user-list/"><i class="fas fa-user fa-fw"></i> &nbsp; LISTA DE EVALUADORES</a>
        </li>
    </ul>    
</div>

<?php
    // Obtener roles desde la base de datos
    $id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
    try {
        $conexion = new PDO("mysql:host=localhost;dbname=xcoring", "root", "");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta para obtener los roles del proyecto específico
        $stmt = $conexion->prepare("SELECT id_rol, nombre_rol FROM roles WHERE id_proyecto = :id_proyecto");
        $stmt->bindParam(':id_proyecto', $id_proyecto, PDO::PARAM_INT);
        $stmt->execute();

        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>

<!-- Formulario para crear un nuevo evaluador -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/usuarioAjax.php" method="POST" data-form="save" enctype="multipart/form-data" autocomplete="off">
        <fieldset>
            <legend><i class="fas fa-user fa-fw"></i> &nbsp; Información de la cuenta</legend>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo para el nombre del evaluador -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario_nombre" class="bmd-label-floating">Nombres</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}" class="form-control" name="usuario_nombre_reg" id="usuario_nombre" maxlength="35" required="">
                        </div>
                    </div>
                    <!-- Campo para el apellido del evaluador -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario_apellido" class="bmd-label-floating">Apellidos</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}" class="form-control" name="usuario_apellido_reg" id="usuario_apellido" maxlength="35" required="">
                        </div>
                    </div>
                    <!-- Campo para el teléfono del evaluador -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario_telefono" class="bmd-label-floating">Teléfono</label>
                            <input type="text" pattern="[0-9\+()]{8,20}" class="form-control" name="usuario_telefono_reg" id="usuario_telefono" maxlength="20">
                        </div>
                    </div>
                    <!-- Campo para el correo electrónico del evaluador -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario_email" class="bmd-label-floating">Email</label>
                            <input type="email" class="form-control" name="usuario_email_reg" id="usuario_email" maxlength="70" pattern="[a-zA-Z0-9$@.-]{7,100}" required="">
                        </div>
                    </div>
                    <!-- Campo para la contraseña del evaluador -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario_clave_1" class="bmd-label-floating">Contraseña</label>
                            <input type="password" class="form-control" name="usuario_clave_1_reg" id="usuario_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required="">
                        </div>
                    </div>
                    <!-- Campo para repetir la contraseña del evaluador -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario_clave_2" class="bmd-label-floating">Repetir contraseña</label>
                            <input type="password" class="form-control" name="usuario_clave_2_reg" id="usuario_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required="">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <br>
        <fieldset>
            <legend><i class="fas fa-user fa-fw"></i> &nbsp; Rol dentro de la aplicación</legend>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo para seleccionar el rol del evaluador -->
                    <div class="col-12">
                        <div class="form-group">
                            <select class="form-control" name="usuario_privilegio_reg">
                                <option value="0" selected="">Seleccione una opción</option>
                                <option value="1">Nivel 1: Administrador</option>
                                <option value="2">Nivel 2: Consultor</option>
                                <option value="3">Nivel 3: Evaluador</option>
                            </select>
                        </div>
                    </div>
                    <!-- Campo para seleccionar el rol asociado al proyecto -->
                    <legend><i class="fas fa-user fa-fw"></i> &nbsp; Rol dentro del proyecto </legend>
                    <div class="col-12">
                        <div class="form-group">
                            <select class="form-control" name="rol_asociado_proyecto" id="rol_asociado_proyecto">
                                <option value="">Seleccione el rol</option>
                                <?php
                                if (!empty($roles)) {
                                    foreach ($roles as $rol) {
                                        echo '<option value="' . $rol['id_rol'] . '">' . $rol['nombre_rol'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <!-- Botones para limpiar el formulario o guardar los datos -->
        <p class="text-center" style="margin-top: 40px;">
            <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
            &nbsp; &nbsp;
            <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
        </p>
    </form>
</div>