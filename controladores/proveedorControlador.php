<?php

// Verificar si la petición es AJAX para incluir el archivo de modelo correspondiente
if ($peticionAjax) {
    require_once "../modelos/proveedorModelo.php";
} else {
    require_once "./modelos/proveedorModelo.php";
}

// Definición de la clase proveedorControlador que extiende de proveedorModelo
class proveedorControlador extends proveedorModelo {

    /*----------  Controlador agregar proveedor  ----------*/
    public function agregar_proveedor_controlador() {
        // Iniciar sesión
        session_start(['name' => 'xcoring']);
        // Limpiar y asignar los datos enviados por POST
        $nombre = mainModel::limpiar_cadena($_POST['nombre_proveedor_reg']);
        $nombreContacto_proveedor = mainModel::limpiar_cadena($_POST['nombreContacto_proveedor_reg']);
        $email = mainModel::limpiar_cadena($_POST['emailContacto_proveedor_reg']);
        $telefono = mainModel::limpiar_cadena($_POST['telefonoContacto_proveedor_reg']);

        // Comprobar que los campos no estén vacíos
        if ($nombre == "" || $nombreContacto_proveedor == "" || $email == "" || $telefono == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No has llenado todos los campos que son requeridos.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar la integridad de los datos
        if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,40}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombreContacto_proveedor)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre del contacto que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "La dirección que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificar_datos("[0-9\+()]{8,20}", $telefono)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El teléfono que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Comprobar si el nombre ya está registrado
        $check_nombre = mainModel::ejecutar_consulta_simple("SELECT nombre_proveedor FROM proveedor WHERE nombre_proveedor='$nombre'");
        if ($check_nombre->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre ingresado ya se encuentra registrado en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start(['name' => 'xcoring']);
        }

        // Verificar si $_SESSION['datos_proveedor'] está inicializado como un arreglo
        if (!isset($_SESSION['datos_proveedor'])) {
            $_SESSION['datos_proveedor'] = array();
        }

        // Crear un array con los datos del proveedor
        $datos_proveedor_reg = [
            "Nombre" => $nombre,
            "nombreContacto_proveedor" => $nombreContacto_proveedor,
            "Email" => $email,
            "Telefono" => $telefono,
            "Proyecto" => $_SESSION['datos_proyecto'][0]['IDProyecto']
        ];

        // Llamar al modelo para agregar el proveedor
        $agregar_proveedor = proveedorModelo::agregar_proveedor_modelo($datos_proveedor_reg);

        if ($agregar_proveedor->rowCount() == 1) {
            // Generar la alerta de éxito
            $alerta = [
                "Alerta" => "redireccionar",
                "URL" => SERVERURL . "evaluacion-new/"
            ];
            // Obtener el último ID insertado
            $ultimo_id = proveedorModelo::obtener_ultimo_id();
            $this->guardar_datos_variable($ultimo_id);
            return json_encode($alerta); // Devolver la respuesta como JSON
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos podido registrar el proveedor, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
        }
    } /*-- Fin controlador --*/

    /*----------  Controlador guardar datos proveedor variable  ----------*/
    public function guardar_datos_variable($id_proveedor) {
        // Obtener la información del proveedor con la información del proyecto asociado
        $info_proveedor = proveedorModelo::obtener_info_proveedor_con_proyecto($id_proveedor);

        // Verificar si el proveedor ya existe en $_SESSION['datos_proveedor']
        $proveedor_existente = false;
        foreach ($_SESSION['datos_proveedor'] as $proveedor) {
            if ($proveedor['ID'] == $info_proveedor['id_proveedor']) {
                $proveedor_existente = true;
                break;
            }
        }

        // Si el proveedor no existe, agregarlo a $_SESSION['datos_proveedor']
        if (!$proveedor_existente) {
            // Agregar el proveedor a $_SESSION['datos_proveedor']
            $_SESSION['datos_proveedor'][] = [
                "ID" => $info_proveedor['id_proveedor'],
                "Nombre" => $info_proveedor['nombre_proveedor'],
                "nombreContacto_proveedor" => $info_proveedor['nombreContacto_proveedor'],
                "Email" => $info_proveedor['emailContacto_proveedor'],
                "Telefono" => $info_proveedor['telefonoContacto_proveedor']
            ];
        }
    } /*-- Fin controlador --*/

    /*----------  Controlador paginador proveedor  ----------*/
    public function paginador_proveedor_controlador($pagina, $registros, $privilegio, $url, $busqueda) {
        $id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
        $pagina = mainModel::limpiar_cadena($pagina);
        $registros = mainModel::limpiar_cadena($registros);
        $privilegio = mainModel::limpiar_cadena($privilegio);

        $url = mainModel::limpiar_cadena($url);
        $url = SERVERURL . $url . "/";

        $busqueda = mainModel::limpiar_cadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS c.*, p.nombre_proyecto 
                        FROM proveedor c
                        INNER JOIN proyecto p ON c.id_proyecto = p.id_proyecto
                        WHERE c.nombre_proveedor LIKE '%$busqueda%'
                        ORDER BY c.nombre_proveedor ASC LIMIT $inicio, $registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS c.*, p.nombre_proyecto 
                        FROM proveedor c
                        INNER JOIN proyecto p ON c.id_proyecto = p.id_proyecto
                        WHERE c.id_proyecto = $id_proyecto
                        ORDER BY c.nombre_proveedor ASC
                        LIMIT $inicio, $registros";
        }

        $conexion = mainModel::conectar();

        $datos = $conexion->query($consulta);

        $datos = $datos->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int)$total->fetchColumn();

        $Npaginas = ceil($total / $registros);

        ### Cuerpo de la tabla ###
        $tabla .= '
            <div class="table-responsive">
            <table class="table table-dark table-sm">
                <thead>
                    <tr class="text-center roboto-medium">
                        <th>#</th>
                        <th>NOMBRE PROVEEDOR</th>
                        <th>NOMBRE CONTACTO</th>
                        <th>EMAIL</th>
                        <th>TELEFONO</th>';
        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .= '<th>EDITAR</th>';
        }
        if ($privilegio == 1) {
            $tabla .= '<th>ELIMINAR</th>';
        }
        $tabla .= '</tr>
                </thead>
                <tbody>
        ';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                $tabla .= '
                    <tr class="text-center" >
                        <td>' . $contador . '</td>
                        <td>' . $rows['nombre_proveedor'] . '</td>
                        <td>' . $rows['nombreContacto_proveedor'] . '</td>
                        <td>' . $rows['emailContacto_proveedor'] . '</td>
                        <td>' . $rows['telefonoContacto_proveedor'] . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '
                        <td>
                            <a href="' . SERVERURL . 'proveedor-update/' . mainModel::encryption($rows['id_proveedor']) . '/" class="btn btn-dark">
                                <i class="fas fa-sync-alt"></i>    
                            </a>
                        </td>
                    ';
                }
                if ($privilegio == 1) {
                    $tabla .= '
                        <td>
                            <form class="FormularioAjax" action="' . SERVERURL . 'ajax/proveedorAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
                                <input type="hidden" name="proveedor_id_del" value="' . mainModel::encryption($rows['id_proveedor']) . '">
                                <button type="submit" class="btn btn-dark">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    ';
                }
                $tabla .= '</tr>
                ';
                $contador++;
            }
            $reg_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla .= '
                    <tr class="text-center" >
                        <td colspan="9">
                            <a href="' . $url . '" class="btn btn-raised btn-primary btn-sm">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            } else {
                $tabla .= '
                    <tr class="text-center" >
                        <td colspan="9">
                            No hay registros en el sistema
                        </td>
                    </tr>
                ';
            }
        }

        $tabla .= '</tbody></table></div>';

        ### Paginacion ###
        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">Mostrando proveedores ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';

            $tabla .= mainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
        }

        return $tabla;
    } /*-- Fin controlador --*/

    /*----------  Controlador seleccionar proveedor evaluación ----------*/
    public function agregar_proveedor_variable($proveedor_id) {
        // Limpiar el ID del proveedor
        $id_proveedor = mainModel::limpiar_cadena($proveedor_id);

        // Verificar si el proveedor existe en la base de datos
        $check_proveedor = mainModel::ejecutar_consulta_simple("SELECT * FROM proveedor WHERE id_proveedor = :id_proveedor", array(':id_proveedor' => $id_proveedor));

        // Si no se encuentra el proveedor, devolver un mensaje de error
        if ($check_proveedor->rowCount() <= 0) {
            $alerta = array(
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No hemos podido agregar el proveedor debido a un error, por favor inténtelo nuevamente.",
                "Tipo" => "error"
            );
            return json_encode($alerta);
        } else {
            // El proveedor existe en la base de datos, obtener sus datos
            $campos = $check_proveedor->fetch();

            // Iniciar la sesión si aún no se ha iniciado
            if (session_status() == PHP_SESSION_NONE) {
                session_start(['name' => 'xcoring']);
            }

            // Verificar si $_SESSION['datos_proveedor'] está vacío o no está definido
            if (empty($_SESSION['datos_proveedor'])) {
                // Crear la variable $_SESSION['datos_proveedor'] como un array
                $_SESSION['datos_proveedor'] = array();
            }

            // Agregar el proveedor al array $_SESSION['datos_proveedor']
            $_SESSION['datos_proveedor'][] = array(
                "ID" => $campos['id_proveedor'],
                "Nombre" => $campos['nombre_proveedor']
            );

            // Devolver un mensaje de éxito
            $alerta = array(
                "Alerta" => "recargar",
                "Titulo" => "¡Proveedor Seleccionado!",
                "Texto" => "El proveedor se seleccionó para realizar una evaluación.",
                "Tipo" => "success"
            );
            return json_encode($alerta);
        }
    }

    /*----------  Controlador datos proveedor  ----------*/
    public function datos_proveedor_controlador($tipo, $id) {
        $tipo = mainModel::limpiar_cadena($tipo);

        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_cadena($id);

        return proveedorModelo::datos_proveedor_modelo($tipo, $id);
    } /*-- Fin controlador --*/

    /*----------  Controlador actualizar proveedor  ----------*/
    public function actualizar_proveedor_controlador() {
        session_start(['name' => 'xcoring']);
        /*== Recuperando id ==*/
        $id = mainModel::decryption($_POST['proveedor_id_up']);
        $id = mainModel::limpiar_cadena($id);

        /*== Comprobando proveedor en la DB ==*/
        $check_proveedor = mainModel::ejecutar_consulta_simple("SELECT * FROM proveedor WHERE id_proveedor='$id'");
        if ($check_proveedor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos encontrado el proveedor en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos = $check_proveedor->fetch();
        }

        // Limpiar y asignar los datos enviados por POST
        $nombre = mainModel::limpiar_cadena($_POST['nombre_proveedor_up']);
        $nombreContacto_proveedor = mainModel::limpiar_cadena($_POST['nombreContacto_proveedor_up']);
        $email = mainModel::limpiar_cadena($_POST['emailContacto_proveedor_up']);
        $telefono = mainModel::limpiar_cadena($_POST['telefonoContacto_proveedor_up']);

        // Comprobar que los campos no estén vacíos
        if ($nombre == "" || $nombreContacto_proveedor == "" || $telefono == "" || $email == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No has llenado todos los campos que son requeridos.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar la integridad de los datos
        if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,40}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombreContacto_proveedor)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre del contacto que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El email que ha ingresado no coincide con el formato solicitado. Solo se permiten los siguientes símbolos () . , # -",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificar_datos("[0-9\+()]{8,20}", $telefono)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El teléfono que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Comprobar si el nombre ya está registrado
        if ($nombre != $campos['nombre_proveedor']) {
            $check_nombre = mainModel::ejecutar_consulta_simple("SELECT nombre_proveedor FROM proveedor WHERE nombre_proveedor='$nombre'");
            if ($check_nombre->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error.",
                    "Texto" => "El nombre ingresado ya se encuentra registrado en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }

        // Comprobar privilegios
        if ($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Crear un array con los datos del proveedor actualizado
        $datos_proveedor_up = [
            "Nombre" => $nombre,
            "nombreContacto_proveedor" => $nombreContacto_proveedor,
            "Email" => $email,
            "Telefono" => $telefono,
            "ID" => $id
        ];

        // Llamar al modelo para actualizar el proveedor
        if (proveedorModelo::actualizar_proveedor_modelo($datos_proveedor_up)) {
            // Actualizar los datos de sesión si el proveedor está presente
            if (isset($_SESSION['datos_proveedor']) && is_array($_SESSION['datos_proveedor'])) {
                foreach ($_SESSION['datos_proveedor'] as $key => $value) {
                    if ($value['ID'] == $id) {
                        $_SESSION['datos_proveedor'][$key]['Nombre'] = $nombre;
                        break;
                    }
                }
            }
            $alerta = [
                "Alerta" => "redireccionar",
                "URL" => SERVERURL . "evaluacion-new/"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos podido actualizar los datos del proveedor, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    } /*-- Fin controlador --*/

    /*----------  Controlador eliminar proveedor  ----------*/
    public function eliminar_proveedor_controlador() {
        // Recuperar el ID del proveedor
        $id = mainModel::decryption($_POST['proveedor_id_del']);
        $id = mainModel::limpiar_cadena($id);

        // Comprobar si el proveedor existe en la base de datos
        $check_proveedor = mainModel::ejecutar_consulta_simple("SELECT id_proveedor FROM proveedor WHERE id_proveedor='$id'");
        if ($check_proveedor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos encontrado el proveedor en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Comprobar privilegios
        session_start(['name' => 'xcoring']);
        if ($_SESSION['privilegio_xcoring'] != 1) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Llamar al modelo para eliminar el proveedor
        $eliminar_proveedor = proveedorModelo::eliminar_proveedor_modelo($id);

        if ($eliminar_proveedor->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "¡Proveedor Eliminado!",
                "Texto" => "El proveedor ha sido eliminado del sistema exitosamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos podido eliminar el proveedor del sistema, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    } /*-- Fin controlador --*/
}