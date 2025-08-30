<!-- Contenedor principal de la página -->
<div class="container-fluid form-neon">
    <div class="container-fluid">
        <div class="row">
            <!-- Sección para mostrar la tabla de categorías -->
            <?php
            require_once "./controladores/evaluacionControlador.php";
            $ins_evaluacion = new evaluacionControlador();
            // Mostrar la primera tabla en la primera columna
            echo $ins_evaluacion->tabla_categoria_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
            ?>
            <!-- Sección para mostrar la tabla de criterios -->
            <?php
            // Mostrar la segunda tabla en la segunda columna
            echo $ins_evaluacion->tabla_criterios_categoria_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
            ?>
        </div>
    </div>
    <!-- Sección para mostrar la tabla de proveedores -->
    <?php
        echo $ins_evaluacion->tabla_proveedor_controlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
    ?>
    <!-- Sección para mostrar la tabla de evaluadores -->
    <?php
        echo $ins_evaluacion->tablaEvaluadoresControlador($pagina[1], 15, $_SESSION['privilegio_xcoring'], $pagina[0], "");
    ?>
    <!-- Formulario para exportar las tablas a Excel -->
    <form class="form-neon FormularioAjax form-importar-excel" action="<?php echo SERVERURL; ?>ajax/exportacionAjax.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        <p>Exporta las tablas aquí: </p>
        <input type="hidden" class="form-control" name="export" id="export" value="<?php echo $_SESSION['datos_proyecto'][0]['IDProyecto']; ?>">
        <button type="submit">Exportar</button>
    </form>
    
<!-- Script para poder descargar el excel sin necesidad de pasar un JSON -->
<script>
document.querySelector('.form-importar-excel').addEventListener('submit', function(event) {
    // Permite que el formulario se envíe de manera tradicional
    this.submit();
});
</script>

</div>