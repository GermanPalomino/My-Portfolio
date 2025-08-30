<?php
    // Verificar si el usuario tiene privilegios válidos (privilegio entre 1 y 2)
    if ($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2) {
        // Si el usuario no tiene los privilegios necesarios, forzar el cierre de sesión
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }

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
        <i class="fas fa-list fa-fw"></i> &nbsp; ACTUALIZAR CATEGORIA
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
        <!-- Enlace para ver la lista de categorías -->
        <li>
            <a href="<?php echo SERVERURL; ?>categoria-list/">
                <i class="fas fa-list fa-fw"></i> &nbsp; LISTA DE CATEGORIA
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

<!-- Contenedor principal -->
<div class="container-fluid">
    <?php
        // Incluir el controlador de categorías
        require_once "./controladores/categoriaControlador.php";
        $ins_categoria = new categoriaControlador();

        // Obtener los datos de la categoría según el ID proporcionado en la URL
        $datos_categoria = $ins_categoria->datos_categoria_controlador("Unico", $pagina[1]);

        // Verificar si se encontraron datos para la categoría
        if ($datos_categoria->rowCount() == 1) {
            $campos = $datos_categoria->fetch();
    ?>
    <!-- Formulario para actualizar la categoría -->
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/categoriaAjax.php" method="POST" data-form="update" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="categoria_id_up" value="<?php echo $pagina[1]; ?>" >
        <fieldset>
            <legend><i class="fas fa-list fa-fw"></i> &nbsp; Información de la categoría</legend>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo para el nombre de la categoría -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categoria_nombre" class="bmd-label-floating">Nombre</label>
                            <input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}" class="form-control" name="categoria_nombre_up" value="<?php echo $campos['nombre_categoria']; ?>" id="categoria_nombre" maxlength="140" required="">
                        </div>
                    </div>
                    <!-- Campo para la descripción de la categoría -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categoria_descripcion" class="bmd-label-floating">Descripción</label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,1000}" class="form-control" name="categoria_descripcion_up" value="<?php echo $campos['descripcion_categoria']; ?>" id="categoria_descripcion" maxlength="190">
                        </div>
                    </div>
                    <!-- Campo para el peso de la categoría -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categoria_peso" class="bmd-label-floating">Peso (%)</label>
                            <input type="num" pattern="[0-9]{1,9}" class="form-control" name="categoria_peso_up" value="<?php echo $campos['peso_categoria']; ?>" id="categoria_peso" maxlength="9">
                        </div>
                    </div>
                    <!-- Campo para seleccionar el rol asociado -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="criterio_proyecto" class="bmd-label-floating">Hacia qué rol va dirigido</label>
                            <select class="form-control" name="id_rol_up" id="id_rol">
                                <option value="">Seleccione el rol</option>
                                <?php
                                if (!empty($roles)) {
                                    foreach ($roles as $rol) {
                                        $selected = ($rol['id_rol'] == $campos['id_rol']) ? 'selected' : '';
                                        echo '<option value="' . $rol['id_rol'] . '" ' . $selected . '>' . $rol['nombre_rol'] . '</option>';
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
        <!-- Botón para actualizar la categoría -->
        <p class="text-center" style="margin-top: 40px;">
            <button type="submit" class="btn btn-raised btn-success btn-sm">
                <i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR
            </button>
        </p>
    </form>
    <?php } else { ?>
    <!-- Mostrar un mensaje de error si no se encontraron datos para la categoría -->
    <div class="alert alert-danger text-center" role="alert">
        <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
        <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
        <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
    </div>
    <?php } ?>
</div>