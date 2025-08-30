<?php
    // Verificar el privilegio del usuario y forzar cierre de sesión si no es administrador
    if ($_SESSION['privilegio_xcoring'] != 1) {
        echo $lc->forzar_cierre_sesion_controlador();
        exit();
    }
    require_once "./modelos/mainModel.php";

    // Conexión a la base de datos
    $link = mysqli_connect("localhost", "root", "");
    if ($link) {
        mysqli_select_db($link, "xcoring");
    }
?>

<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-table fa-fw"></i> &nbsp; ACTUALIZAR CRITERIO
    </h3>
</div>

<!-- Navegación de pestañas -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <!-- Enlace para crear un nuevo criterio -->
        <li>
            <a href="<?php echo SERVERURL; ?>criterio-new/">
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

<!-- Formulario para actualizar un criterio existente -->
<div class="container-fluid">
    <?php
        // Obtener datos del criterio a actualizar
        require_once "./controladores/criterioControlador.php";
        $ins_criterio = new criterioControlador();
        $datos_criterio = $ins_criterio->datos_criterio_controlador("Unico", $pagina[1]);

        if ($datos_criterio->rowCount() == 1) {
            $campos = $datos_criterio->fetch();
    ?>
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/criterioAjax.php" method="POST" data-form="update" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="criterio_id_up" value="<?php echo $pagina[1]; ?>">
        <fieldset>
            <legend><i class="fas fa-table fa-fw"></i> &nbsp; Información del criterio</legend>
            <div class="container-fluid">
                <div class="row">
                    <!-- Campo para el nombre del criterio -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="criterio_nombre" class="bmd-label-floating">Nombre del criterio</label>
                            <input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}" class="form-control" name="criterio_nombre_up" value="<?php echo $campos['nombre_criterio']; ?>" id="criterio_nombre" maxlength="140" required="">
                        </div>
                    </div>
                    <!-- Campo para la descripción del criterio -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="criterio_descripcion" class="bmd-label-floating">Descripción</label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,1000}" class="form-control" name="criterio_descripcion_up" value="<?php echo $campos['descripcion_criterio']; ?>" id="criterio_descripcion" maxlength="1000">
                        </div>
                    </div>
                    <!-- Campo para el peso del criterio -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="criterio_peso" class="bmd-label-floating">Peso (%)</label>
                            <input type="num" pattern="[0-9]{1,9}" class="form-control" name="criterio_peso_up" value="<?php echo $campos['peso_criterio']; ?>" id="criterio_peso" maxlength="9">
                        </div>
                    </div>
                    <!-- Campo para seleccionar la categoría del criterio -->
                    <div class="col-md-6">
                        <label for="usuario_categoria" class="bmd-label-floating">Categoría (SELECCIONADO)</label>
                        <select class="form-control" name="id_categoria_criterio_up" id="id_categoria_criterio">
                            <option value="">Seleccione categoría</option>
                            <?php
                            session_start(['name' => 'xcoring']);
                            $id_proyecto = $_SESSION['datos_proyecto']['ID'];
                            $v = mysqli_query($link, "SELECT * FROM categoria WHERE id_proyecto LIKE '%$id_proyecto%'");
                            while ($sistemas = mysqli_fetch_row($v)) {
                            ?>
                                <option value="<?php echo $sistemas[0] ?>" <?php if ($sistemas[0] == $campos['id_categoria']) echo 'selected'; ?>>
                                    <?php echo $sistemas[1] ?>
                                </option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Campo para seleccionar el tipo de pregunta -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_pregunta" class="bmd-label-floating">Tipo de Pregunta</label>
                            <select class="form-control" name="tipo_pregunta_up" id="tipo_pregunta" onchange="mostrarCampos()">
                                <option value="abierta" <?php if ($campos['tipopregunta_criterio'] == 'abierta') echo 'selected'; ?>>Abierta</option>
                                <option value="cerrada" <?php if ($campos['tipopregunta_criterio'] == 'cerrada') echo 'selected'; ?>>Cerrada</option>
                            </select>
                        </div>
                    </div>
                    <!-- Campos adicionales para preguntas cerradas -->
                    <div class="col-md-12" id="campos_cerrada" style="display: none;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <div class="form-group">
                            <label for="descripcion_calificacion_<?php echo $i; ?>" class="bmd-label-floating">Descripción Calificación <?php echo $i; ?></label>
                            <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,1000}" class="form-control" name="descripcion_calificacion_<?php echo $i; ?>_up" value="<?php echo $campos['descripcion_calificacion' . $i . '_criterio']; ?>" id="descripcion_calificacion_<?php echo $i; ?>" maxlength="1000">
                        </div>
                        <?php endfor; ?>
                    </div>
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
                <i class="far fa-save"></i> &nbsp; ACTUALIZAR
            </button>
        </p>
    </form>
    <?php } else { ?>
    <!-- Mensaje de error si no se encuentra el criterio -->
    <div class="alert alert-danger text-center" role="alert">
        <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
        <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
        <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
    </div>
    <?php } ?>
</div>

<!-- Script para mostrar/ocultar campos -->
<script>
function mostrarCampos() {
    var tipoPregunta = document.getElementById("tipo_pregunta").value;
    var camposCerrada = document.getElementById("campos_cerrada");
    
    if (tipoPregunta === "cerrada") {
        camposCerrada.style.display = "block";
    } else {
        camposCerrada.style.display = "none";
    }
}

// Llamar a la función mostrarCampos al cargar la página para ajustar la visibilidad inicial
document.addEventListener("DOMContentLoaded", function() {
    mostrarCampos();
});
</script>
