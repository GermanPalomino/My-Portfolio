<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado datos mediante el método POST relacionados con la gestión de criterios
if(isset($_POST['criterio_nombre_reg']) || isset($_POST['criterio_id_up']) || isset($_POST['criterio_id_del']) || isset($_POST['criterio_id'])){

    // Incluye el controlador de criterios
    require_once "../controladores/criterioControlador.php";
    
    // Crea una instancia del controlador de criterios
    $ins_criterio = new criterioControlador();

    // Si se ha enviado el nombre de un criterio para registrar
    if(isset($_POST['criterio_nombre_reg'])){
        // Llama al método del controlador para agregar un criterio y muestra el resultado
        echo $ins_criterio->agregar_criterio_controlador(); 
    }

    // Si se han enviado el ID y el nombre de un criterio para actualizar
    if(isset($_POST['criterio_id_up']) && isset($_POST['criterio_nombre_up'])){
        // Llama al método del controlador para actualizar un criterio y muestra el resultado
        echo $ins_criterio->actualizar_criterio_controlador();
    }

    // Si se ha enviado el ID de un criterio para seleccionar
    if (isset($_POST['criterio_id'])) {
        // Llama al método del controlador para agregar un criterio como variable de sesión y muestra el resultado
        echo $ins_criterio->agregar_criterio_variable($_POST['criterio_id']);
    }

    // Si se ha enviado el ID de un criterio para eliminar
    if(isset($_POST['criterio_id_del'])){
        // Llama al método del controlador para eliminar un criterio y muestra el resultado
        echo $ins_criterio->eliminar_criterio_controlador();
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