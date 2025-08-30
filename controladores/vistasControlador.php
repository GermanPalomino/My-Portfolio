<?php

// Incluye el archivo del modelo de vistas
require_once "./modelos/vistasModelo.php";

// Define la clase controlador para las vistas
class vistasControlador extends vistasModelo{

	/*---------- Controlador obtener plantilla ----------*/
	// Método para obtener la plantilla principal del sistema
	public function obtener_plantilla_controlador(){
		// Incluye el archivo de la plantilla
		return require_once "./vistas/plantilla.php";
	}

	/*---------- Controlador obtener vistas ----------*/
	// Método para obtener la vista solicitada por el usuario
	public function obtener_vistas_controlador(){
		// Verifica si hay una vista solicitada a través del parámetro 'views' en la URL
		if(isset($_GET['views'])){
			// Divide la ruta en segmentos utilizando '/' como delimitador
			$ruta = explode("/", $_GET['views']);
			// Llama al modelo para obtener la vista correspondiente al primer segmento de la ruta
			$respuesta = vistasModelo::obtener_vistas_modelo($ruta[0]);
		}else{
			// Si no hay una vista solicitada, establece la vista por defecto como 'login'
			$respuesta = "login";
		}
		// Retorna la vista obtenida
		return $respuesta;
	}
}