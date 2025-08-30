<?php

if($peticionAjax){
    require_once "../modelos/proyectoModelo.php";
}else{
    require_once "./modelos/proyectoModelo.php";
}

class proyectoControlador extends proyectoModelo{

    /*----------  Controlador agregar proyecto  ----------*/
    public function agregar_proyecto_controlador(){
        session_start(['name'=>'xcoring']);
        
        $nombre = mainModel::limpiar_cadena($_POST['nombre_proyecto_reg']);
        $email = mainModel::limpiar_cadena($_POST['emailContacto_proyecto_reg']);
        $roles = array_map('mainModel::limpiar_cadena', $_POST['roles_proyecto']);
        $cliente = isset($_SESSION['datos_cliente'][0]['IDCliente']) ? $_SESSION['datos_cliente'][0]['IDCliente'] : null;

        if(empty($cliente)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No se ha seleccionado un cliente válido.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        if($nombre == "" || $email == ""){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No has llenado todos los campos que son requeridos.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        if(mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}", $nombre)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El email no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_nombre = mainModel::ejecutar_consulta_simple("SELECT nombre_proyecto FROM proyecto WHERE nombre_proyecto='$nombre'");
        if($check_nombre->rowCount() > 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre ingresado ya se encuentra registrado en el sistema.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start(['name'=>'xcoring']);
        }

        if (!isset($_SESSION['datos_proyecto'])) {
            $_SESSION['datos_proyecto'] = array();
        }

        $datos_proyecto_reg = [
            "Nombre" => $nombre,
            "Email" => $email,
            "IdCliente" => $cliente
        ];

        $agregar_proyecto = proyectoModelo::agregar_proyecto_modelo($datos_proyecto_reg);
        
        if($agregar_proyecto->rowCount() == 1){
            $ultimo_id = proyectoModelo::obtener_ultimo_id();
            $this->guardar_datos_variable($ultimo_id);
            
            foreach($roles as $rol){
                $datos_rol = [
                    "NombreRol" => $rol,
                    "IdProyecto" => $ultimo_id
                ];
                proyectoModelo::agregar_rol_modelo($datos_rol);
            }

            $alerta = [
                "Alerta" => "recargar",
            ];
            return json_encode($alerta);
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos podido registrar el proyecto, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }
    }

    public function guardar_datos_variable($id_proyecto) {
        $info_proyecto = proyectoModelo::obtener_info_proyecto($id_proyecto);
        
        $proyecto_existente = false;
        foreach ($_SESSION['datos_proyecto'] as $proyecto) {
            if ($proyecto['IDProyecto'] == $info_proyecto['id_proyecto']) {
                $proyecto_existente = true;
                break;
            }
        }
        
        if (!$proyecto_existente) {
            $_SESSION['datos_proyecto'][] = [
                "IDProyecto" => $info_proyecto['id_proyecto'],
                "NombreProyecto" => $info_proyecto['nombre_proyecto'],
            ];
        }
    }

    /*----------  Controlador paginador proyecto  ----------*/
    public function paginador_proyecto_controlador($pagina, $registros, $privilegio, $url, $busqueda){
        require_once "./vistas/inc/BtnSeleccionar.php";
        $id_cliente = $_SESSION['datos_cliente'][0]['IDCliente'];
        $pagina = mainModel::limpiar_cadena($pagina);
        $registros = mainModel::limpiar_cadena($registros);
        $privilegio = mainModel::limpiar_cadena($privilegio);

        $url = mainModel::limpiar_cadena($url);
        $url = SERVERURL . $url . "/";

        $busqueda = mainModel::limpiar_cadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS proyecto.*, cliente.nombre_cliente 
                        FROM proyecto 
                        LEFT JOIN cliente ON proyecto.id_cliente = cliente.id_cliente 
                        WHERE cliente.id_cliente = $id_cliente
                        AND proyecto.nombre_proyecto LIKE '%$busqueda%'
                        ORDER BY proyecto.id_proyecto ASC";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS proyecto.*, cliente.nombre_cliente 
                        FROM proyecto 
                        LEFT JOIN cliente ON proyecto.id_cliente = cliente.id_cliente 
                        WHERE cliente.id_cliente = $id_cliente
                        ORDER BY proyecto.id_proyecto ASC";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta);
        $datos = $datos->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int) $total->fetchColumn();

        $Npaginas = ceil($total / $registros);

        ### Cuerpo de la tabla ###
        $tabla .= '
            <div class="table-responsive">
            <table class="table table-dark table-sm">
                <thead>
                    <tr class="text-center roboto-medium">
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>EMAIL</th>
                        <th>CLIENTE</th>';
                        if($privilegio == 1 || $privilegio == 2){
                            $tabla .= '<th>EDITAR</th>';
                        }
                        if($privilegio == 1){
                            $tabla .= '<th>SELECCIONAR</th>';
                        }
                        if($privilegio == 1){
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
                        <td>' . $rows['id_proyecto'] . '</td>
                        <td>' . $rows['nombre_proyecto'] . '</td>
                        <td>' . $rows['emailContacto_proyecto'] . '</td>
                        <td>' . $rows['nombre_cliente'] . '</td>';
                        if ($privilegio == 1 || $privilegio == 2){
                            $tabla .= '
                                <td>
                                    <a href="' . SERVERURL . 'proyecto-update/' . mainModel::encryption($rows['id_proyecto']) . '/" class="btn btn-dark">
                                        <i class="fas fa-sync-alt"></i>    
                                    </a>
                                </td>
                            ';
                        }
                        $tabla .= '
                        <td>
                            <button class="btn btn-dark btn-agregar-proyecto" data-proyecto-id="' . mainModel::encryption($rows['id_proyecto']) . '" onclick="agregarProyecto(' . $rows['id_proyecto'] . ')">
                                Seleccionar 
                            </button>
                        </td>
                    ';
                        if ($privilegio == 1){
                            $tabla .= '
                                <td>
                                    <form class="FormularioAjax" action="' . SERVERURL . 'ajax/proyectoAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
                                        <input type="hidden" name="proyecto_id_del" value="' . mainModel::encryption($rows['id_proyecto']) . '">
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
            if ($total >= 1){
                $tabla .= '
                    <tr class="text-center" >
                        <td colspan="8">
                            <a href="' . $url . '" class="btn btn-raised btn-primary btn-sm">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            } else {
                $tabla .= '
                    <tr class="text-center" >
                        <td colspan="8">
                            No hay registros en el sistema
                        </td>
                    </tr>
                ';
            }
        }

        $tabla .= '</tbody></table></div>';

        ### Paginacion ###
        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">Mostrando proyectos ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';

            // Verificar si $_SESSION['datos_proyecto'] está definida y no está vacía
            if (isset($_SESSION['datos_proyecto'][0]['IDProyecto']) && !empty($_SESSION['datos_proyecto'][0]['IDProyecto'])) {
                $idProyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
                $consultaEvaluacion = "SELECT COUNT(*) as count FROM evaluacion WHERE id_proyecto = :idProyecto";
                $stmt = $conexion->prepare($consultaEvaluacion);
                $stmt->bindParam(':idProyecto', $idProyecto, PDO::PARAM_INT);
                $stmt->execute();
                $resultEvaluacion = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($resultEvaluacion['count'] > 0) {
                    $urlEvaluacion = SERVERURL . 'evaluacion-list/';
                } else {
                    $urlEvaluacion = SERVERURL . 'evaluacion-new/';
                }

                $tabla .= '
                <div class="btn-right">
                    <p>Después de crear o seleccionar tu proyecto, ya puedes gestionar tus categorías, criterios, proveedores y evaluadores desde este apartado:</p>
                    <a href="' . $urlEvaluacion . '" class="btn btn-primary btn-custom"> <b>Ir a gestion</b></a>
                </div>
                ';
            }

            $tabla .= mainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
        }

        return $tabla;
    } /*-- Fin controlador --*/

    /*----------  Controlador seleccionar cliente evaluación ----------*/
    public function agregar_proyecto_variable($id_proyecto) {
        // Limpiamos el ID del cliente
        $id_proyecto = mainModel::limpiar_cadena($id_proyecto);

        // Verificamos si el cliente existe en la base de datos
        $check_proyecto = mainModel::ejecutar_consulta_simple("SELECT * FROM proyecto WHERE id_proyecto = :id_proyecto", array(':id_proyecto' => $id_proyecto));

        // Si no se encuentra el cliente, devolvemos un mensaje de error
        if($check_proyecto->rowCount() <= 0) {
            $alerta = array(
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No hemos podido agregar el cliente debido a un error, por favor inténtelo nuevamente.",
                "Tipo" => "error"
            );
            return json_encode($alerta);
        } else {
            // El cliente existe en la base de datos, obtenemos sus datos
            $campos = $check_proyecto->fetch();

            // Iniciamos la sesión si aún no se ha iniciado
            if (session_status() == PHP_SESSION_NONE) {
                session_start(['name' => 'xcoring']);
            }

            // Verificamos si $_SESSION['datos_proyecto'] está vacío o no está definido
            if(empty($_SESSION['datos_proyecto'])) {
                // Creamos la variable $_SESSION['datos_proyecto'] como un array
                $_SESSION['datos_proyecto'] = array();
            }

            // Agregamos el proyecto al array $_SESSION['datos_proyecto']
            $_SESSION['datos_proyecto'][] = array(
                "IDProyecto" => $campos['id_proyecto'],
                "NombreProyecto" => $campos['nombre_proyecto']
            );

            // Devolvemos un mensaje de éxito
            $alerta = array(
                "Alerta" => "recargar",
                "Titulo" => "¡Proyecto Seleccionado!",
                "Texto" => "El proyecto se seleccionó para realizar una evaluación.",
                "Tipo" => "success"
            );
            return json_encode($alerta);
        }
    }

    /*----------  Controlador datos proyecto  ----------*/
    public function datos_proyecto_controlador($tipo, $id){
        $tipo = mainModel::limpiar_cadena($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_cadena($id);
        return proyectoModelo::datos_proyecto_modelo($tipo, $id);
    }/*-- Fin controlador --*/

    /*----------  Controlador obtener roles del proyecto  ----------*/
    public function obtener_roles_proyecto_controlador($id_proyecto){
        return proyectoModelo::obtener_roles_proyecto_modelo($id_proyecto);
    }/*-- Fin controlador --*/

    /*----------  Controlador actualizar proyecto  ----------*/
    public function actualizar_proyecto_controlador(){
        // Recuperando id
        $id = mainModel::decryption($_POST['proyecto_id_up']);
        $id = mainModel::limpiar_cadena($id);

        // Comprobando proyecto en la DB
        $check_proyecto = mainModel::ejecutar_consulta_simple("SELECT * FROM proyecto WHERE id_proyecto='$id'");
        if($check_proyecto->rowCount() <= 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos encontrado el proyecto en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $nombre = mainModel::limpiar_cadena($_POST['proyecto_nombre_up']);
        $email = mainModel::limpiar_cadena($_POST['emailContacto_proyecto_up']);
        $roles = array_map('mainModel::limpiar_cadena', $_POST['roles_proyecto']);

        // Comprobando que los campos no estén vacíos
        if($nombre == "" || $email == ""){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No has llenado todos los campos que son requeridos.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,40}", $nombre)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El nombre que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "El email que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Comprobando privilegios
        session_start(['name'=>'xcoring']);
        if($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Datos del proyecto a actualizar
        $datos_proyecto_up = [
            "Nombre" => $nombre,
            "Email" => $email,
            "ID" => $id
        ];

        // Llamar al modelo para actualizar el proyecto
        if(proyectoModelo::actualizar_proyecto_modelo($datos_proyecto_up)){
            // Actualizar roles del proyecto
            proyectoModelo::eliminar_roles_proyecto_modelo($id);
            foreach($roles as $rol){
                $datos_rol = [
                    "NombreRol" => $rol,
                    "IdProyecto" => $id
                ];
                proyectoModelo::agregar_rol_modelo($datos_rol);
            }

            $alerta = [
                "Alerta" => "recargar"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos podido actualizar los datos del proyecto, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }/*-- Fin controlador --*/

    /*----------  Controlador eliminar proyecto  ----------*/
    public function eliminar_proyecto_controlador(){
        // Recuperando id
        $id = mainModel::decryption($_POST['proyecto_id_del']);
        $id = mainModel::limpiar_cadena($id);

        // Comprobando proyecto en la DB
        $check_proyecto = mainModel::ejecutar_consulta_simple("SELECT id_proyecto FROM proyecto WHERE id_proyecto='$id'");
        if($check_proyecto->rowCount() <= 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos encontrado el proyecto en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Comprobando privilegios
        session_start(['name'=>'xcoring']);
        if($_SESSION['privilegio_xcoring'] != 1){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Llamar al modelo para eliminar los roles asociados
        $eliminar_roles = proyectoModelo::eliminar_roles_proyecto_modelo($id);

        // Llamar al modelo para eliminar el proyecto
        $eliminar_proyecto = proyectoModelo::eliminar_proyecto_modelo($id);

        if($eliminar_proyecto->rowCount() == 1 && $eliminar_roles->rowCount() > 0){
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "¡Proyecto Eliminado!",
                "Texto" => "El proyecto y sus roles asociados han sido eliminados del sistema exitosamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error.",
                "Texto" => "No hemos podido eliminar el proyecto del sistema, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }/*-- Fin controlador --*/
}