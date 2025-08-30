<?php

// Verificar si la petición es AJAX para incluir el archivo de modelo correspondiente
if($peticionAjax){
    require_once "../modelos/loginModelo.php";
}else{
    require_once "./modelos/loginModelo.php";
}

// Definición de la clase loginControlador que extiende de loginModelo
class loginControlador extends loginModelo{

    /*----------  Controlador iniciar sesión  ----------*/
    public function iniciar_sesion_controlador(){
        // Limpiar y asignar los datos enviados por POST
        $email = mainModel::limpiar_cadena($_POST['email_log']);
        $pss = mainModel::limpiar_cadena($_POST['pss_log']);

        // Comprobar que los campos no estén vacíos
        if($email == "" || $pss == ""){
            echo '<script>
                Swal.fire({
                    title: "Ocurrió un error inesperado",
                    text: "No has llenado todos los campos que son requeridos.",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>';
            exit();
        }

        // Verificar la integridad de los datos
        if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)){
            echo '<script>
                Swal.fire({
                    title: "Ocurrió un error inesperado",
                    text: "El nombre de usuario no coincide con el formato solicitado.",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>';
            exit();
        }
        if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $pss)){
            echo '<script>
                Swal.fire({
                    title: "Ocurrió un error inesperado",
                    text: "La contraseña no coincide con el formato solicitado.",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>';
            exit();
        }

        // Encriptar la contraseña
        $clave = mainModel::encryption($pss);

        // Crear un array con los datos de inicio de sesión
        $datos_login = [
            "Email" => $email,
            "Pss" => $clave
        ];

        // Llamar al modelo para verificar los datos de inicio de sesión
        $datos_cuenta = loginModelo::iniciar_sesion_modelo($datos_login);

        // Verificar si se encontró una cuenta con los datos proporcionados
        if($datos_cuenta->rowCount() == 1){
            $row = $datos_cuenta->fetch();

            // Iniciar sesión
            session_start(['name' => 'xcoring']);

            // Asignar los datos del usuario a la sesión
            $_SESSION['id_xcoring'] = $row['id_usuario'];
            $_SESSION['nombre_xcoring'] = $row['nombre_usuario'];
            $_SESSION['apellido_xcoring'] = $row['apellido_usuario'];
            $_SESSION['email_xcoring'] = $row['email_usuario'];
            $_SESSION['privilegio_xcoring'] = $row['rol_usuario'];
            $_SESSION['token_xcoring'] = md5(uniqid(mt_rand(), true));

            // Redirigir al home
            return header("Location: ".SERVERURL."home/");
        }else{
            // Mostrar un mensaje de error si los datos son incorrectos
            echo '<script>
                Swal.fire({
                    title: "Ocurrió un error inesperado",
                    text: "El nombre de usuario o contraseña no son correctos.",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>';
        }
    } /*-- Fin controlador --*/


    /*----------  Controlador forzar cierre de sesión  ----------*/
    public function forzar_cierre_sesion_controlador(){
        // Destruir la sesión
        session_unset();
        session_destroy();
        // Redirigir a la página de login
        if(headers_sent()){
            return "<script> window.location.href='".SERVERURL."login/'; </script>";
        }else{
            return header("Location: ".SERVERURL."login/");
        }
    } /*-- Fin controlador --*/


    /*----------  Controlador cierre de sesión  ----------*/
    public function cerrar_sesion_controlador(){
        // Iniciar sesión
        session_start(['name' => 'xcoring']);
        // Desencriptar el token y el usuario enviados por POST
        $token = mainModel::decryption($_POST['token']);
        $usuario = mainModel::decryption($_POST['usuario']);

        // Verificar si el token y el usuario coinciden con los datos de la sesión
        if($token == $_SESSION['token_xcoring'] && $usuario == $_SESSION['email_xcoring']){
            // Destruir la sesión
            session_unset();
            session_destroy();
            // Generar una alerta para redirigir a la página de login
            $alerta = [
                "Alerta" => "redireccionar",
                "URL" => SERVERURL."login/"
            ];
        }else{
            // Generar una alerta de error
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado",
                "Texto" => "No se pudo cerrar la sesión.",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    } /*-- Fin controlador --*/

    /*----------  Controlador redireccionar si faltan datos  ----------*/
    public function redireccionar_si_faltan_datos_controlador($pagina_actual) {
        // Iniciar la sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'xcoring']);
        }
    
        // Verificar si no hay datos de cliente en la sesión
        if (empty($_SESSION['datos_cliente'])) {
            // Lista de páginas restringidas que requieren datos de cliente para su acceso
            $paginas_restringidas = array("proyecto-new", "proyecto-list");
    
            // Verificar si la página actual es una de las páginas restringidas permitidas
            if (in_array($pagina_actual, $paginas_restringidas)) {
                // Si es una página restringida permitida, redirigir al home
                header("Location: " . SERVERURL . "home/");
                exit(); // Finalizar el script después de redirigir
            }
        }

        // Verificar si no hay datos de proyecto en la sesión
        if (empty($_SESSION['datos_proyecto'])) {
            // Lista de páginas restringidas que requieren datos de proyecto para su acceso
            $paginas_restringidas = array("categoria-new", "categoria-list", "criterio-new", "criterio-list", "proveedor-new", "proveedor-list", "evaluacion-new", "evaluacion-list", "reporte-new", "user-new", "user-list");
    
            // Verificar si la página actual es una de las páginas restringidas permitidas
            if (in_array($pagina_actual, $paginas_restringidas)) {
                // Si es una página restringida permitida, redirigir al home
                header("Location: " . SERVERURL . "home/");
                exit(); // Finalizar el script después de redirigir
            }
        }
    }
}