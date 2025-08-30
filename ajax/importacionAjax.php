<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax = true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se ha enviado un archivo Excel
if (isset($_FILES['excelFile'])) {
    // Incluye el controlador de importación
    require_once "../controladores/importacionControlador.php";
    // Crea una instancia del controlador de importación
    $ins_importacion = new importacionControlador();

    // Llama al método del controlador para importar el archivo Excel y muestra el resultado
    echo $ins_importacion->importar_excel_controlador();
} else {
    // Inicia una nueva sesión con el nombre 'xcoring'
    session_start(['name' => 'xcoring']);
    // Elimina todas las variables de sesión
    session_unset();
    // Destruye la sesión actual
    session_destroy();
    // Redirige al usuario a la página de inicio de sesión y finaliza el script
    header("Location: " . SERVERURL . "login/");
    exit();
}