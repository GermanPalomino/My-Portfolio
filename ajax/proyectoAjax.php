<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado datos mediante el método POST relacionados con la gestión de proyectos
if(isset($_POST['nombre_proyecto_reg']) || isset($_POST['proyecto_id_up']) || isset($_POST['proyecto_id_del']) || isset($_POST['proyecto_id'])){
    
    // Incluye el controlador de proyectos
    require_once "../controladores/proyectoControlador.php";
    // Crea una instancia del controlador de proyectos
    $ins_proyecto=new proyectoControlador();
    
    // Si se ha enviado el nombre de un proyecto para registrar
    if(isset($_POST['nombre_proyecto_reg'])){
        // Llama al método del controlador para agregar un proyecto y muestra el resultado
        echo $ins_proyecto->agregar_proyecto_controlador();
    }

    // Si se ha enviado el ID de un proyecto para actualizar
    if(isset($_POST['proyecto_id_up'])){
        // Llama al método del controlador para actualizar un proyecto y muestra el resultado
        echo $ins_proyecto->actualizar_proyecto_controlador(); 
    }

    // Si se ha enviado el ID de un proyecto para eliminar
    if(isset($_POST['proyecto_id_del'])){
        // Llama al método del controlador para eliminar un proyecto y muestra el resultado
        echo $ins_proyecto->eliminar_proyecto_controlador(); 
    }
    
    // Si se ha enviado el ID de un proyecto para seleccionar
    if (isset($_POST['proyecto_id'])) {
        // Llama al método del controlador para agregar un proyecto como variable de sesión y muestra el resultado
        echo $ins_proyecto->agregar_proyecto_variable($_POST['proyecto_id']);
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