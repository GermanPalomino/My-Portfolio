<?php
// Verifica si el usuario tiene privilegios suficientes, de lo contrario cierra la sesión.
if($_SESSION['privilegio_xcoring'] != 1){
    echo $lc->forzar_cierre_sesion_controlador();
    exit();
}
?>

<!-- Encabezado de la página -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list  fa-fw"></i> &nbsp; Estado de la Evaluación
    </h3>
</div>

<div class="container-fluid">
    <div class="container-fluid form-neon">
        <div class="container-fluid">
            <p class="text-center">VISUALIZAR CLIENTE, PROYECTO, PROVEEDOR, CRITERIOS Y EVALUADORES</p>

            <!-- Formulario para importar categorías y criterios desde un archivo Excel -->
            <form class="FormularioAjax form-importar-excel" action="<?php echo SERVERURL; ?>ajax/importacionAjax.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                <p>¿Ya tienes categorías y criterios? ¡Impórtalos aquí!</p>
                <div class="form-group">
                    <label for="fileInput">Seleccionar archivo Excel:</label>
                    <input type="file" id="fileInput" name="excelFile" accept=".xls,.xlsx" required>
                    <button type="submit" name="import">Importar</button>
                </div>
            </form>

            <?php
            // Organiza los criterios por categoría si hay datos disponibles
            $criteriosPorCategoria = [];
            if (!empty($_SESSION['datos_criterio'])) {
                foreach ($_SESSION['datos_criterio'] as $criterio) {
                    $categoriaId = $criterio['IDC'];
                    $criteriosPorCategoria[$categoriaId][] = $criterio;
                }
            }
            ?>

            <div class="container-fluid">
                <div class="row">
                    <!-- Tabla de categorías existentes -->
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-dark table-sm">
                                <thead>
                                    <tr class="text-center roboto-medium">
                                        <th>Categoría</th>
                                        <th>Peso(%)</th>
                                        <th>Editar</th>
                                        <th>Deseleccionar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($_SESSION['datos_categoria'])): ?>
                                        <tr class="text-center">
                                            <td colspan="12">No hay categorías</td>
                                        </tr>
                                        <tr class="text-center">
                                            <td colspan="12">
                                                <a href="<?php echo SERVERURL . 'categoria-new/'; ?>" class="btn btn-success">
                                                    Agregar Categoría <i class="fas fa-plus"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $totalPesoCategorias = 0;
                                        foreach ($_SESSION['datos_categoria'] as $categoria):
                                            $totalPesoCategorias += $categoria['Peso'];
                                        ?>
                                            <tr class="text-center">
                                                <td><?php echo $categoria['Nombre']; ?></td>
                                                <td><?php echo $categoria['Peso']; ?></td>
                                                <?php 
                                                $model = new mainModel();
                                                $encryptedID = $model->encryption($categoria['ID']);
                                                ?>
                                                <td>
                                                    <a href="<?php echo SERVERURL . 'categoria-update/' . $encryptedID . '/'; ?>" class="btn btn-success">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/evaluacionAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off">
                                                        <input type="hidden" name="id_eliminar_categoria" value="<?php echo $categoria['ID']; ?>">
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="text-center" style="font-weight: bold; color: <?php echo ($totalPesoCategorias == 100) ? 'black' : (($totalPesoCategorias > 100) ? 'red' : 'orange'); ?>">
                                            <td colspan="2">Total Peso: <?php echo $totalPesoCategorias; ?></td>
                                            <td>
                                                <?php
                                                if ($totalPesoCategorias < 100) {
                                                    echo 'Falta ' . (100 - $totalPesoCategorias) . ' para 100';
                                                } elseif ($totalPesoCategorias > 100) {
                                                    echo 'Sobrepasa por ' . ($totalPesoCategorias - 100);
                                                }
                                                ?>
                                            </td>
                                            <td colspan="12">
                                                <a href="<?php echo SERVERURL . 'categoria-new/'; ?>" class="btn btn-success">
                                                    Agregar Categoría <i class="fas fa-plus"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php if (empty($_SESSION['datos_categoria'])): ?>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-dark table-sm">
                                    <thead>
                                        <tr class="text-center roboto-medium">
                                            <th>Criterio</th>
                                            <th>Peso(%)</th>
                                            <th>Editar</th>
                                            <th>Deseleccionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td colspan="12">No hay criterios</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tablas dinámicas por cada categoría -->
                    <?php if (!empty($_SESSION['datos_categoria'])): ?>
                        <?php foreach ($_SESSION['datos_categoria'] as $categoria): ?>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-dark table-sm">
                                        <thead>
                                            <tr class="text-center">
                                                <th colspan="6"><?php echo $categoria['Nombre']; ?></th>
                                            </tr>
                                            <tr class="text-center">
                                                <th>Criterio</th>
                                                <th>Peso</th>
                                                <th>Editar</th>
                                                <th>Deseleccionar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $categoriaId = $categoria['ID'];
                                            if (empty($criteriosPorCategoria[$categoriaId])): ?>
                                                <tr class="text-center">
                                                    <td colspan="6">No hay criterios para esta categoría</td>
                                                </tr>
                                                <tr class="text-center">
                                                    <td colspan="12">
                                                        <a href="<?php echo SERVERURL . 'criterio-new/'; ?>" class="btn btn-success">
                                                            Agregar Criterio <i class="fas fa-plus"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php else:
                                                $totalPeso = 0;
                                                foreach ($criteriosPorCategoria[$categoriaId] as $criterio) {
                                                    $totalPeso += $criterio['Peso'];
                                            ?>
                                                <tr class="text-center">
                                                    <td><?php echo $criterio['Nombre']; ?></td>
                                                    <td><?php echo $criterio['Peso']; ?></td>
                                                    <?php 
                                                    $encryptedID = $model->encryption($criterio['ID']);
                                                    ?>
                                                    <td>
                                                        <a href="<?php echo SERVERURL . 'criterio-update/' . $encryptedID . '/'; ?>" class="btn btn-success">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/evaluacionAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off">
                                                            <input type="hidden" name="id_eliminar_criterio" value="<?php echo $criterio['ID']; ?>">
                                                            <button type="submit" class="btn btn-warning">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr class="text-center" style="font-weight: bold; color: <?php echo ($totalPeso == 100) ? 'black' : (($totalPeso > 100) ? 'red' : 'orange'); ?>">
                                                <td colspan="2">Total Peso: <?php echo $totalPeso; ?></td>
                                                <td>
                                                    <?php
                                                    if ($totalPeso < 100) {
                                                        echo 'Falta ' . (100 - $totalPeso) . ' para 100';
                                                    } elseif ($totalPeso > 100) {
                                                        echo 'Sobrepasa por ' . ($totalPeso - 100);
                                                    }
                                                    ?>
                                                </td>
                                                <td colspan="2">
                                                    <a href="<?php echo SERVERURL . 'criterio-new/'; ?>" class="btn btn-success">
                                                        Agregar Criterio <i class="fas fa-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tabla de proveedores -->
            <div class="table-responsive">
                <table class="table table-dark table-sm">
                    <thead>
                        <tr class="text-center roboto-medium">
                            <th>Nombre Proveedor</th>
                            <th>Editar</th>                          
                            <th>Deseleccionar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($_SESSION['datos_proveedor'])) { 
                            foreach($_SESSION['datos_proveedor'] as $proveedor) { ?>
                                <tr class="text-center">
                                    <td><?php echo $proveedor['Nombre']; ?></td>
                                    <?php
                                     
                                    $encryptedID = $model->encryption($proveedor['ID']);
                                    ?>
                                    <td>
                                        <a href="<?php echo SERVERURL . 'proveedor-update/' . $encryptedID . '/'; ?>" class="btn btn-success">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/evaluacionAjax.php" method="POST" data-form="loans" enctype="multipart/form-data" autocomplete="off">
                                            <input type="hidden" name="id_eliminar_proveedor" value="<?php echo $proveedor['ID']; ?>">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                        <?php } ?>
                            <tr class="text-center">
                                <td colspan="3">
                                    <a href="<?php echo SERVERURL . 'proveedor-new/'; ?>" class="btn btn-success">
                                        Agregar Proveedor <i class="fas fa-plus"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr class="text-center">
                                <td colspan="5">No hay proveedor</td>
                            </tr>
                            <tr class="text-center">
                                <td colspan="3">
                                    <a href="<?php echo SERVERURL . 'proveedor-new/'; ?>" class="btn btn-success">
                                        Agregar Proveedor <i class="fas fa-plus"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Tabla de evaluadores -->
            <div class="table-responsive">
                <table class="table table-dark table-sm">
                    <thead>
                        <tr class="text-center roboto-medium">
                            <th>Nombre Evaluador</th>
                            <th>Apellido Evaluador</th>
                            <th>Editar</th>
                            <th>Deseleccionar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($_SESSION['datos_usuario'])) {
                            foreach ($_SESSION['datos_usuario'] as $usuario) { 
                        ?>
                        <tr class="text-center">
                            <td><?php echo $usuario['Nombre']; ?></td>
                            <td><?php echo $usuario['Apellido']; ?></td>
                            <?php
                             $model = new mainModel();
                            $encryptedID = $model->encryption($usuario['ID']);
                            ?>
                            <td>
                                <a href="<?php echo SERVERURL . 'user-update/' . $encryptedID . '/'; ?>" class="btn btn-success">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/evaluacionAjax.php" method="POST" data-form="loans" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="id_eliminar_evaluador" value="<?php echo $usuario['ID']; ?>">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                            } // Fin del foreach
                        ?>
                        <tr class="text-center">
                                <td colspan="12">
                                    <a href="<?php echo SERVERURL . 'user-new/'; ?>" class="btn btn-success">
                                        Agregar Evaluador <i class="fas fa-plus"></i>
                                    </a>
                                </td>
                        </tr>
                        <?php } else { ?>
                        <tr class="text-center">
                            <td colspan="12">No hay evaluadores</td>
                        </tr>
                        <tr class="text-center">
                            <td colspan="12">
                                <a href="<?php echo SERVERURL . 'user-new/'; ?>" class="btn btn-success">
                                    Agregar Evaluador <i class="fas fa-plus"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Formulario para generar la evaluación -->
        <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/evaluacionAjax.php" method="POST" data-form="save" enctype="multipart/form-data" autocomplete="off" >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12" style="display: none;">
                        <div class="form-group">
                            <label for="evaluacion_observacion" class="bmd-label-floating">Observación</label>
                            <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ#() ]{1,400}" class="form-control" name="evaluacion_observacion_reg" id="evaluacion_observacion" maxlength="400">
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center" style="margin-top: 40px;">
                <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
                &nbsp; &nbsp;
                <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GENERAR</button>
            </p>
        </form>
    </div>
</div>