<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado datos mediante el método POST relacionados con la gestión de proveedores
if(isset($_POST['nombre_proveedor_reg']) || isset($_POST['proveedor_id_up']) || isset($_POST['proveedor_id_del']) || isset($_POST['proveedor_id'])){
    
    // Incluye el controlador de proveedores
    require_once "../controladores/proveedorControlador.php";
    // Crea una instancia del controlador de proveedores
    $ins_proveedor= new proveedorControlador();
    
    // Si se ha enviado el nombre de un proveedor para registrar
    if(isset($_POST['nombre_proveedor_reg'])){
        // Llama al método del controlador para agregar un proveedor y muestra el resultado
        echo $ins_proveedor->agregar_proveedor_controlador(); 
    } 
    // Si se han enviado el ID y el nombre de un proveedor para actualizar
    if(isset($_POST['proveedor_id_up']) && isset($_POST['nombre_proveedor_up'] )){
        // Llama al método del controlador para actualizar un proveedor y muestra el resultado
        echo $ins_proveedor->actualizar_proveedor_controlador(); 
    }
    // Si se ha enviado el ID de un proveedor para eliminar
    if(isset($_POST['proveedor_id_del'])){
        // Llama al método del controlador para eliminar un proveedor y muestra el resultado
        echo $ins_proveedor->eliminar_proveedor_controlador(); 
    }
    // Si se ha enviado el ID de un proveedor para seleccionar
    if(isset($_POST['proveedor_id'])){
        // Llama al método del controlador para agregar un proveedor como variable de sesión y muestra el resultado
        echo $ins_proveedor->agregar_proveedor_variable($_POST['proveedor_id']); 
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