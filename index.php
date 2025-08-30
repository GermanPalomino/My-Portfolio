<?php
    // Incluir el archivo de configuración de la aplicación
    require_once "./config/APP.php";

    // Incluir el controlador de vistas
    require_once "./controladores/vistasControlador.php";

    // Crear una instancia del controlador de vistas
    $plantilla = new vistasControlador();

    // Llamar al método para obtener y mostrar la plantilla
    $plantilla->obtener_plantilla_controlador();