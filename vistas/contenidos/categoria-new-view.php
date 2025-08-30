<?php
    // Verificar el privilegio del usuario y forzar cierre de sesión si no es administrador
    if($_SESSION['privilegio_xcoring'] != 1){
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
    require_once "./modelos/mainModel.php";

    // Verifica si la sesión contiene los datos del proyecto
    if (isset($_SESSION['datos_proyecto'][0]['IDProyecto'])) {
        $id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
        
        // Conexión a la base de datos
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
    }   
?>
<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-list fa-fw"></i> &nbsp; CREAR CATEGORIA
    </h3>
</div>

<!-- Navegación de pestañas -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear una nueva categoría (activo) -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>categoria-new/">
                <i class="fas fa-list fa-fw"></i> &nbsp; CREAR CATEGORIA
            </a>
        </li>
        <!-- Enlace para ver la lista de categorías -->
        <li>
            <a href="<?php echo SERVERURL; ?>categoria-list/">
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

<!-- Formulario para crear una nueva categoría -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/categoriaAjax.php" method="POST" data-form="save" enctype="multipart/form-data" autocomplete="off">
        <fieldset>
            <legend><i class="fas fa-list fa-fw"></i> &nbsp; Información de la categoría</legend>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo para el nombre de la categoría -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categoria_nombre" class="bmd-label-floating">Nombre</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,35}" class="form-control" name="categoria_nombre_reg" id="categoria_nombre" maxlength="35" required>
                        </div>
                    </div>
                    <!-- Campo para la descripción de la categoría -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categoria_descripcion" class="bmd-label-floating">Descripción</label>
                            <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,10000}" class="form-control" name="categoria_descripcion_reg" id="categoria_descripcion" maxlength="140" required>
                        </div>
                    </div>
                    <!-- Campo para el peso de la categoría -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categoria_peso" class="bmd-label-floating">Peso (%)</label>
                            <input type="num" pattern="[0-9]{1,9}" class="form-control" name="categoria_peso_reg" id="categoria_peso" maxlength="3" required>
                        </div>
                    </div>
                    <!-- Campo para seleccionar el rol asociado -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="criterio_proyecto" class="bmd-label-floating">Hacia qué rol va dirigido</label>
                            <select class="form-control" name="id_rol_reg" id="id_rol">
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
        <br><br><br>
        <!-- Botones para limpiar el formulario y guardar la categoría -->
        <p class="text-center" style="margin-top: 40px;">
            <button type="reset" class="btn btn-raised btn-secondary btn-sm">
                <i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR
            </button>
            &nbsp; &nbsp;
            <button type="submit" class="btn btn-raised btn-info btn-sm">
                <i class="far fa-save"></i> &nbsp; GUARDAR
            </button>
        </p>
    </form>
</div>