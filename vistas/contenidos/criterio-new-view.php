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
            
            // Consulta para obtener las categorías del proyecto específico
            $stmt = $conexion->prepare("SELECT id_categoria, nombre_categoria FROM categoria WHERE id_proyecto = :id_proyecto");
            $stmt->bindParam(':id_proyecto', $id_proyecto, PDO::PARAM_INT);
            $stmt->execute();
            
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }   
?>

<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-table fa-fw"></i> &nbsp; CREAR CRITERIO
    </h3>
</div>

<!-- Navegación de pestañas -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo criterio -->
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>criterio-new/">
                <i class="fas fa-table fa-fw"></i> &nbsp; CREAR CRITERIO
            </a>
        </li>
        <!-- Enlace para ver la lista de criterios -->
        <li>
            <a href="<?php echo SERVERURL; ?>criterio-list/">
                <i class="fas fa-table fa-fw"></i> &nbsp; LISTA DE CRITERIOS
            </a>
        </li>
        <!-- Enlace para buscar criterios (comentado) -->
        <li>
            <!--<a href="<?php echo SERVERURL; ?>criterio-search/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR CRITERIO
            </a>-->
        </li>
    </ul>
</div>

<!-- Formulario para crear un nuevo criterio -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/criterioAjax.php" method="POST" data-form="save" enctype="multipart/form-data" autocomplete="off">
        <fieldset>
            <legend><i class="fas fa-table fa-fw"></i> &nbsp; Información del criterio</legend>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo para el nombre del criterio -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="criterio_nombre" class="bmd-label-floating">Nombre del criterio</label>
                            <input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}" class="form-control" name="criterio_nombre_reg" id="criterio_nombre" maxlength="140" required="">
                        </div>
                        <!-- Campo para el peso del criterio -->
                        <div class="form-group">
                            <label for="criterio_peso" class="bmd-label-floating">Peso (%)</label>
                            <input type="num" pattern="[0-9]{1,9}" class="form-control" name="criterio_peso_reg" id="criterio_peso" maxlength="3" required="">
                        </div>
                    </div>
                    <!-- Campo para la descripción del criterio -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="criterio_descripcion" class="bmd-label-floating">Descripción</label>
                            <input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,10000}" class="form-control" name="criterio_descripcion_reg" id="criterio_descripcion" maxlength="140" required="">
                        </div>
                        <!-- Campo para seleccionar la categoría del criterio -->
                        <div class="form-group">
                            <label for="criterio_proyecto" class="bmd-label-floating">Categoría</label>
                            <select class="form-control" name="idcategoria_reg" id="">
                                <option value="">Seleccione categoría</option>
                                <?php
                                if (!empty($categorias)) {
                                    foreach ($categorias as $categoria) {
                                        echo '<option value="' . $categoria['id_categoria'] . '">' . $categoria['nombre_categoria'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Campo para seleccionar el tipo de pregunta -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="tipo_pregunta" class="bmd-label-floating">Tipo de Pregunta</label>
                            <select class="form-control" name="tipo_pregunta" id="tipo_pregunta" onchange="mostrarCampos()">
                                <option value="abierta">Abierta</option>
                                <option value="cerrada">Cerrada</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Campos adicionales para preguntas cerradas -->
                <div class="row" id="campos_cerrada" style="display: none;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="descripcion_calificacion_<?php echo $i; ?>" class="bmd-label-floating">Descripción Calificación <?php echo $i; ?></label>
                            <input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}" class="form-control" name="descripcion_calificacion_<?php echo $i; ?>" id="descripcion_calificacion_<?php echo $i; ?>" maxlength="140">
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </fieldset>
        <br><br><br>
        <!-- Botones de acción -->
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

<!-- Script para mostrar/ocultar campos -->
<script>
function mostrarCampos() {
    var tipoPregunta = document.getElementById("tipo_pregunta").value;
    var camposCerrada = document.getElementById("campos_cerrada");
    
    if (tipoPregunta === "cerrada") {
        camposCerrada.style.display = "flex";
    } else {
        camposCerrada.style.display = "none";
    }
}
</script>