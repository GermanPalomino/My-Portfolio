<?php 

class vistasModelo{

    /* 
    Método: obtener_vistas_modelo
    Descripción: Obtiene la vista solicitada si está en la lista blanca y el archivo correspondiente existe.
    Parámetros:
        - $vistas (string): Nombre de la vista solicitada.
    Retorno:
        - $contenido (string): Ruta del archivo de la vista o "404" si la vista no está permitida o no existe.
    */
    protected static function obtener_vistas_modelo($vistas){
        // Lista blanca de vistas permitidas
        $listaBlanca = [
            "proyecto-list", "proyecto-new", "proyecto-search", "proyecto-update",
            "reporte-new", "formulario-new", "client-list", "client-new", "client-search",
            "client-update", "company", "home", "categoria-list", "categoria-new",
            "categoria-search", "categoria-update", "criterio-list", "criterio-new",
            "criterio-search", "criterio-update", "evaluacion-list", "evaluacion-new",
            "evaluacion-asignado", "reservation-search", "evaluacion-update", "user-list",
            "evaluacion-sinasignar", "user-new", "user-search", "user-update",
            "proveedor-new", "proveedor-list", "proveedor-update"
        ];

        // Verificar si la vista está en la lista blanca
        if (in_array($vistas, $listaBlanca)) {
            // Verificar si el archivo de la vista existe
            if (is_file("./vistas/contenidos/".$vistas."-view.php")) {
                $contenido = "./vistas/contenidos/".$vistas."-view.php";
            } else {
                $contenido = "404";
            }
        } elseif ($vistas == "login" || $vistas == "index") {
            // Si la vista es "login" o "index", devolver la vista de login
            $contenido = "login";
        } else {
            // Si la vista no está permitida o no existe, devolver "404"
            $contenido = "404";
        }

        // Retornar la ruta del archivo de la vista o "404"
        return $contenido;
    }
}