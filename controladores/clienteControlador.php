<?php

// Verifica si la petición es AJAX para incluir el archivo de modelo correspondiente
if($peticionAjax){
    require_once "../modelos/clienteModelo.php";
}else{
    require_once "./modelos/clienteModelo.php";
}

// Definición de la clase clienteControlador que extiende de clienteModelo
class clienteControlador extends clienteModelo{
    
    /*----------  Controlador agregar cliente  ----------*/
    public function agregar_cliente_controlador(){
        // Limpiar y asignar los datos enviados por POST
        $nombre = mainModel::limpiar_cadena($_POST['nombre_cliente_reg']);
        $nombreContacto_cliente = mainModel::limpiar_cadena($_POST['nombreContacto_cliente_reg']);
        $telefono = mainModel::limpiar_cadena($_POST['telefonoContacto_cliente_reg']);
        $email = mainModel::limpiar_cadena($_POST['emailContacto_cliente_reg']);

        // Comprobar que los campos no estén vacíos
        if($nombre == "" || $nombreContacto_cliente == "" || $telefono == "" || $email == ""){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "No has llenado todos los campos que son requeridos.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        // Verificar la integridad de los datos
        if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombre)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El nombre que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombreContacto_cliente)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El nombre del contacto del cliente que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        if(mainModel::verificar_datos("[0-9\+()]{8,20}", $telefono)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El teléfono que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El email que ha ingresado no coincide con el formato solicitado. Solo se permiten los siguientes símbolos () . , # -",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        // Comprobar si el nombre del cliente ya existe en la base de datos
        $check_nombre = mainModel::ejecutar_consulta_simple("SELECT nombre_cliente FROM cliente WHERE nombre_cliente='$nombre'");
        if($check_nombre->rowCount() > 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El nombre ingresado ya se encuentra registrado en el sistema.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }

        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start(['name' => 'xcoring']);
        }
    
        // Verificar si $_SESSION['datos_cliente'] está inicializado como un arreglo
        if (!isset($_SESSION['datos_cliente'])) {
            $_SESSION['datos_cliente'] = array();
        }

        // Datos del cliente para registrar
        $datos_cliente_reg = [
            "Nombre" => $nombre,
            "nombreContacto_cliente" => $nombreContacto_cliente,
            "Telefono" => $telefono,
            "Email" => $email
        ];

        // Llamar al modelo para agregar el cliente
        $agregar_cliente = clienteModelo::agregar_cliente_modelo($datos_cliente_reg);
        
        if($agregar_cliente->rowCount() == 1){
            $ultimo_id = clienteModelo::obtener_ultimo_id();
            $this->guardar_datos_variable($ultimo_id);
            $alerta = [
                "Alerta" => "recargar",
            ];
            return json_encode($alerta);
        }else{
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "No hemos podido registrar el cliente, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }
    } /*-- Fin controlador --*/

    /*----------  Controlador guardar datos cliente en variable  ----------*/
    public function guardar_datos_variable($id_cliente) {
        // Obtener la información del cliente
        $info_cliente = clienteModelo::obtener_info_cliente($id_cliente);
    
        // Verificar si el cliente ya existe en $_SESSION['datos_cliente']
        $cliente_existente = false;
        foreach ($_SESSION['datos_cliente'] as $cliente) {
            if ($cliente['ID'] == $info_cliente['id_cliente']) {
                $cliente_existente = true;
                break;
            }
        }
    
        // Si el cliente no existe, agregarlo a $_SESSION['datos_cliente']
        if (!$cliente_existente) {
            // Agregar el cliente a $_SESSION['datos_cliente']
            $_SESSION['datos_cliente'][] = [
                "IDCliente" => $info_cliente['id_cliente'],
                "NombreCliente" => $info_cliente['nombre_cliente']
            ];
        }
    } /*-- Fin controlador --*/

    /*----------  Controlador paginador cliente  ----------*/
    public function paginador_cliente_controlador($pagina, $registros, $privilegio, $url, $busqueda){
        require_once "./vistas/inc/BtnSeleccionar.php";
        $pagina = mainModel::limpiar_cadena($pagina);
        $registros = mainModel::limpiar_cadena($registros);
        $privilegio = mainModel::limpiar_cadena($privilegio);

        $url = mainModel::limpiar_cadena($url);
        $url = SERVERURL . $url . "/";

        $busqueda = mainModel::limpiar_cadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        // Realizar la consulta SQL dependiendo de si hay una búsqueda o no
        if (isset($busqueda) && $busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM cliente WHERE nombre_cliente LIKE '%$busqueda%' OR nombreContacto_cliente LIKE '%$busqueda%' OR telefonoContacto_cliente LIKE '%$busqueda%' ORDER BY id_cliente ASC LIMIT $inicio, $registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM cliente ORDER BY id_cliente ASC LIMIT $inicio, $registros";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta);
        $datos = $datos->fetchAll();
        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int) $total->fetchColumn();
        $Npaginas = ceil($total / $registros);

        // Crear la tabla HTML con los datos de los clientes
        $tabla .= '
            <div class="table-responsive">
            <table class="table table-dark table-sm">
                <thead>
                    <tr class="text-center roboto-medium">
                        <th>ID</th>
                        <th>NOMBRE CLIENTE</th>
                        <th>NOMBRE CONTACTO</th>
                        <th>TELEFONO</th>
                        <th>EMAIL</th>';
                        if ($privilegio == 1 || $privilegio == 2) {
                            $tabla .= '<th>EDITAR</th>';
                        }
                        if ($privilegio == 1 || $privilegio == 2) {
                            $tabla .= '<th>SELECCIONAR</th>';
                        }
                        if ($privilegio == 1) {
                            $tabla .= '<th>ELIMINAR</th>';
                        }
                    $tabla .= '</tr>
                </thead>
                <tbody>
        ';

        // Verificar si hay datos para mostrar
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                $tabla .= '
                    <tr class="text-center">
                        <td>' . $rows['id_cliente'] . '</td>
                        <td>' . $rows['nombre_cliente'] . '</td>
                        <td>' . $rows['nombreContacto_cliente'] . '</td>
                        <td>' . $rows['telefonoContacto_cliente'] . '</td>
                        <td>' . $rows['emailContacto_cliente'] . '</td>';
                        if ($privilegio == 1 || $privilegio == 2) {
                            $tabla .= '
                            <td>
                                <a href="' . SERVERURL . 'client-update/' . mainModel::encryption($rows['id_cliente']) . '/" class="btn btn-dark">
                                    <i class="fas fa-sync-alt"></i>    
                                </a>
                            </td>';
                            $tabla .= '
                            <td>
                                <button class="btn btn-dark btn-agregar-cliente" data-cliente-id="' . mainModel::encryption($rows['id_cliente']) . '" onclick="agregarCliente(' . $rows['id_cliente'] . ')">
                                    Seleccionar 
                                </button>
                            </td>';
                        }
                        if ($privilegio == 1) {
                            $tabla .= '
                            <td>
                                <form class="FormularioAjax" action="' . SERVERURL . 'ajax/clienteAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off">
                                    <input type="hidden" name="cliente_id_del" value="' . mainModel::encryption($rows['id_cliente']) . '">
                                    <button type="submit" class="btn btn-dark">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>';
                        }
                    $tabla .= '</tr>';
                $contador++;
            }
            $reg_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla .= '
                    <tr class="text-center">
                        <td colspan="9">
                            <a href="' . $url . '" class="btn btn-raised btn-dark btn-sm">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>';
            } else {
                $tabla .= '
                    <tr class="text-center">
                        <td colspan="9">
                            No hay registros en el sistema
                        </td>
                    </tr>';
            }
        }

        $tabla .= '</tbody></table></div>';

        // Paginación
        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">Mostrando clientes ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            
            // Mostrar el botón solo si $_SESSION['datos_cliente'][0]['IDCliente'] está definido
            if (isset($_SESSION['datos_cliente'][0]['IDCliente']) && !empty($_SESSION['datos_cliente'][0]['IDCliente'])) {
                $tabla .= '
                <div class="btn-right">
                    <p>Después de crear o seleccionar tu cliente, puedes ir a la lista de proyectos donde puedes crear o seleccionar un proyecto:</p>
                    <a href="' . SERVERURL . 'proyecto-list/" class="btn btn-primary btn-custom"> <b>Ir a lista de proyectos</b></a>
                </div>';
            }
            
            $tabla .= mainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
        }

        return $tabla;
    } /*-- Fin controlador --*/   

    /*----------  Controlador seleccionar cliente para evaluación  ----------*/
    public function agregar_cliente_variable($cliente_id) {
        // Limpiar el ID del cliente
        $id_cliente = mainModel::limpiar_cadena($cliente_id);

        // Verificar si el cliente existe en la base de datos
        $check_cliente = mainModel::ejecutar_consulta_simple("SELECT * FROM cliente WHERE id_cliente = :id_cliente", array(':id_cliente' => $id_cliente));

        // Si no se encuentra el cliente, devolver un mensaje de error
        if($check_cliente->rowCount() <= 0) {
            $alerta = array(
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No hemos podido agregar el cliente debido a un error, por favor inténtelo nuevamente.",
                "Tipo" => "error"
            );
            return json_encode($alerta);
        } else {
            // El cliente existe en la base de datos, obtener sus datos
            $campos = $check_cliente->fetch();

            // Iniciar la sesión si aún no se ha iniciado
            if (session_status() == PHP_SESSION_NONE) {
                session_start(['name' => 'xcoring']);
            }

            // Verificar si $_SESSION['datos_cliente'] está vacío o no está definido
            if(empty($_SESSION['datos_cliente'])) {
                // Crear la variable $_SESSION['datos_cliente'] como un array
                $_SESSION['datos_cliente'] = array();
            }

            // Agregar el cliente al array $_SESSION['datos_cliente']
            $_SESSION['datos_cliente'][] = array(
                "IDCliente" => $campos['id_cliente'],
                "NombreCliente" => $campos['nombre_cliente']
            );

            // Devolver un mensaje de éxito
            $alerta = array(
                "Alerta" => "recargar",
                "Titulo" => "¡Cliente Seleccionado!",
                "Texto" => "El cliente se seleccionó para realizar una evaluación.",
                "Tipo" => "success"
            );
            return json_encode($alerta);
        }
    }

    /*----------  Controlador datos cliente  ----------*/
    public function datos_cliente_controlador($tipo, $id){
        $tipo = mainModel::limpiar_cadena($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_cadena($id);

        return clienteModelo::datos_cliente_modelo($tipo, $id);
    } /*-- Fin controlador --*/

    /*----------  Controlador actualizar cliente  ----------*/
    public function actualizar_cliente_controlador(){
        // Recuperar el ID del cliente a actualizar
        $id = mainModel::decryption($_POST['cliente_id_up']);
        $id = mainModel::limpiar_cadena($id);

        // Comprobar que el cliente existe en la base de datos
        $check_cliente = mainModel::ejecutar_consulta_simple("SELECT * FROM cliente WHERE id_cliente='$id'");
        if($check_cliente->rowCount() <= 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No hemos encontrado el cliente en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos = $check_cliente->fetch();
        }

        // Limpiar y asignar los datos enviados por POST
        $nombre = mainModel::limpiar_cadena($_POST['nombre_cliente_up']);
        $nombreContacto_cliente = mainModel::limpiar_cadena($_POST['nombreContacto_cliente_up']);
        $telefono = mainModel::limpiar_cadena($_POST['telefonoContacto_cliente_up']);
        $email = mainModel::limpiar_cadena($_POST['emailContacto_cliente_up']);

        // Comprobar que los campos no estén vacíos
        if($nombre == "" || $nombreContacto_cliente == "" || $telefono == "" || $email == ""){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "No has llenado todos los campos que son requeridos.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar la integridad de los datos
        if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombre)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El nombre que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombreContacto_cliente)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El nombre del contacto del cliente que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if(mainModel::verificar_datos("[0-9\+()]{8,20}", $telefono)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El teléfono que ha ingresado no coincide con el formato solicitado.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "El email que ha ingresado no coincide con el formato solicitado. Solo se permiten los siguientes símbolos () . , # -",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Comprobar si el nombre del cliente ya existe en la base de datos
        if($nombre != $campos['nombre_cliente']){
            $check_nombre = mainModel::ejecutar_consulta_simple("SELECT nombre_cliente FROM cliente WHERE nombre_cliente='$nombre'");
            if($check_nombre->rowCount() > 0){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error",
                    "Texto" => "El nombre ingresado ya se encuentra registrado en el sistema.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }

        // Verificar privilegios
        session_start(['name' => 'xcoring']);
        if($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Datos del cliente para actualizar
        $datos_cliente_up = [
            "Nombre" => $nombre,
            "nombreContacto_cliente" => $nombreContacto_cliente,
            "Telefono" => $telefono,
            "Email" => $email,
            "ID" => $id
        ];

        // Llamar al modelo para actualizar el cliente
        if(clienteModelo::actualizar_cliente_modelo($datos_cliente_up)){
            $alerta = [
                "Alerta" => "recargar"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "No hemos podido actualizar los datos del cliente, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    } /*-- Fin controlador --*/

    /*----------  Controlador eliminar cliente  ----------*/
    public function eliminar_cliente_controlador(){
        // Recuperar el ID del cliente a eliminar
        $id = mainModel::decryption($_POST['cliente_id_del']);
        $id = mainModel::limpiar_cadena($id);

        // Comprobar que el cliente existe en la base de datos
        $check_cliente = mainModel::ejecutar_consulta_simple("SELECT id_cliente FROM cliente WHERE id_cliente='$id'");
        if($check_cliente->rowCount() <= 0){
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "No hemos encontrado el cliente en el sistema.",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Comprobar privilegios
        session_start(['name' => 'xcoring']);
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

        // Llamar al modelo para eliminar el cliente
        $eliminar_cliente = clienteModelo::eliminar_cliente_modelo($id);

        if($eliminar_cliente->rowCount() == 1){
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "¡Cliente Eliminado!",
                "Texto" => "El cliente ha sido eliminado del sistema exitosamente.",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error",
                "Texto" => "No hemos podido eliminar el cliente del sistema, por favor intente nuevamente.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    } /*-- Fin controlador --*/
}