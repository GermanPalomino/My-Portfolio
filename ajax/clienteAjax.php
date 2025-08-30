<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado datos mediante el método POST relacionados con la gestión de clientes
if(isset($_POST['nombre_cliente_reg']) || isset($_POST['cliente_id_up']) || isset($_POST['cliente_id_del']) || isset($_POST['cliente_id'])){
    
    // Incluye el controlador de clientes
    require_once "../controladores/clienteControlador.php";

    // Crea una instancia del controlador de clientes
    $ins_cliente= new clienteControlador();
    
    // Si se ha enviado el nombre de un cliente para registrar
    if(isset($_POST['nombre_cliente_reg'])){
        // Llama al método del controlador para agregar un cliente y muestra el resultado
        echo $ins_cliente->agregar_cliente_controlador(); 
    } 
    // Si se han enviado el ID y el nombre de un cliente para actualizar
    if(isset($_POST['cliente_id_up']) && isset($_POST['nombre_cliente_up'] )){
        // Llama al método del controlador para actualizar un cliente y muestra el resultado
        echo $ins_cliente->actualizar_cliente_controlador(); 
    }

    // Si se ha enviado el ID de un cliente para eliminar
    if(isset($_POST['cliente_id_del'])){
        // Llama al método del controlador para eliminar un cliente y muestra el resultado
        echo $ins_cliente->eliminar_cliente_controlador(); 
    }
    // Si se ha enviado el ID de un cliente para seleccionar
    if (isset($_POST['cliente_id'])) {
        // Llama al método del controlador para agregar un cliente como variable de sesión y muestra el resultado
        echo $ins_cliente->agregar_cliente_variable($_POST['cliente_id']);
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