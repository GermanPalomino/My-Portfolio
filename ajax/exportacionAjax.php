<?php

// Establece que la petición es AJAX
$peticionAjax = true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si la solicitud POST contiene la clave 'export'
if (isset($_POST['export'])) {
    // Incluye el controlador de exportación
    require_once "../controladores/exportacionControlador.php";
    
    // Crea una instancia del controlador de exportación
    $ins_exportacion = new exportacionControlador();
    
    // Llama al método para exportar a Excel y muestra el resultado
    echo $ins_exportacion->exportar_excel_controlador();
} else {
    // Si no se encuentra la clave 'export' en la solicitud POST, destruye la sesión y redirige al login
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}