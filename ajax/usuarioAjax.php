<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado datos mediante el método POST relacionados con la gestión de usuarios
if(isset($_POST['usuario_email_reg']) || isset($_POST['usuario_id_up']) || isset($_POST['usuario_id_del'])){
    
    // Incluye el controlador de usuarios
    require_once "../controladores/usuarioControlador.php";
    // Crea una instancia del controlador de usuarios
    $ins_usuario = new usuarioControlador();

    // Si se ha enviado el email de un usuario para registrar
    if(isset($_POST['usuario_email_reg'])){
        // Llama al método del controlador para agregar un usuario y muestra el resultado
        echo $ins_usuario->agregar_usuario_controlador();
    }

    // Si se han enviado el ID y el nombre de un usuario para actualizar
    if(isset($_POST['usuario_id_up']) && isset($_POST['usuario_nombre_up'])){
        // Llama al método del controlador para actualizar un usuario y muestra el resultado
        echo $ins_usuario->actualizar_usuario_controlador();
    }

    // Si se ha enviado el ID de un usuario para eliminar
    if(isset($_POST['usuario_id_del'])){
        // Llama al método del controlador para eliminar un usuario y muestra el resultado
        echo $ins_usuario->eliminar_usuario_controlador();
    }

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