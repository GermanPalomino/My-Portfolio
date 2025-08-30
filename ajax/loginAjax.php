<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado el token y el usuario mediante el método POST
if(isset($_POST['token']) && isset($_POST['usuario'])){

    // Incluye el controlador de inicio de sesión
    require_once "../controladores/loginControlador.php";
    // Crea una instancia del controlador de inicio de sesión
    $ins_login= new loginControlador();

    // Llama al método del controlador para cerrar sesión y muestra el resultado
    echo $ins_login->cerrar_sesion_controlador();

}else{
    // Inicia una nueva sesión con el nombre 'xcoring'
    session_start(['name'=>'xcoring']);
    // Elimina todas las variables de sesión
    session_unset();
    // Destruye la sesión actual
    session_destroy();
    // Redirige al usuario a la página de inicio de sesión y finaliza el script
    header("Location: ".SERVERURL."login/");
    exit();
}