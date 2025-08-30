<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado datos mediante el método POST relacionados con la gestión de categorías
if(isset($_POST['categoria_nombre_reg']) || isset($_POST['categoria_id_up']) || isset($_POST['categoria_id_del']) || isset($_POST['categoria_id'])){

    // Incluye el controlador de categorías
    require_once "../controladores/categoriaControlador.php";

    // Crea una instancia del controlador de categorías
    $ins_categoria = new categoriaControlador();

    // Si se ha enviado el nombre de una categoría para registrar
    if(isset($_POST['categoria_nombre_reg'])){
        // Llama al método del controlador para agregar una categoría y muestra el resultado
        echo $ins_categoria->agregar_categoria_controlador(); 
    }

    // Si se han enviado el ID y el nombre de una categoría para actualizar
    if(isset($_POST['categoria_id_up']) && isset($_POST['categoria_nombre_up'])){
        // Llama al método del controlador para actualizar una categoría y muestra el resultado
        echo $ins_categoria->actualizar_categoria_controlador();
    }
    
    // Si se ha enviado el ID de una categoría para eliminar
    if(isset($_POST['categoria_id_del'])){
        // Llama al método del controlador para eliminar una categoría y muestra el resultado
        echo $ins_categoria->eliminar_categoria_controlador();
    }

    // Si se ha enviado el ID de una categoría para seleccionar
    if (isset($_POST['categoria_id'])) {
        // Llama al método del controlador para agregar una categoría como variable de sesión y muestra el resultado
        echo $ins_categoria->agregar_categoria_variable($_POST['categoria_id']);
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