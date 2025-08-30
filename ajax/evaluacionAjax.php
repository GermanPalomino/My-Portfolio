<?php

// Establece la variable $peticionAjax en true para indicar que se está utilizando una petición AJAX
$peticionAjax=true;

// Incluye el archivo de configuración de la aplicación
require_once "../config/APP.php";

// Verifica si se han enviado datos mediante el método GET o POST relacionados con la gestión de evaluaciones
if(
    isset($_GET['listar_clientes']) || isset($_POST['id_agregar_cliente']) || isset($_POST['id_eliminar_cliente']) || 
    isset($_GET['listar_proyectos']) || isset($_POST['id_agregar_proyecto']) || isset($_POST['id_eliminar_proyecto']) || 
    isset($_POST['id_eliminar_categoria']) || isset($_POST['id_eliminar_proveedor']) ||  isset($_POST['id_eliminar_criterio']) || 
    isset($_POST['id_eliminar_evaluador']) || isset($_POST['evaluacion_observacion_reg']) || isset($_POST['categoria_id_del']) || 
    isset($_POST['criterio_id_del']) || isset($_POST['proveedor_id_del']) || isset($_POST['user_id_del']) || isset($_POST['evaluador_id'])
){

    // Incluye el controlador de evaluaciones
    require_once "../controladores/evaluacionControlador.php";
    
    // Crea una instancia del controlador de evaluaciones
    $ins_evaluacion = new evaluacionControlador();

    // Si se solicita listar clientes
    if(isset($_GET['listar_clientes'])){
        // Llama al método para listar clientes de evaluación
        echo $ins_evaluacion->listar_clientes_evaluacion_controlador();
    }
    // Si se solicita agregar un cliente
    if(isset($_POST['id_agregar_cliente'])){
        // Llama al método para agregar cliente de evaluación
        echo $ins_evaluacion->agregar_cliente_evaluacion_controlador($_POST['id_agregar_cliente']);
    }
    // Si se solicita eliminar un cliente
    if(isset($_POST['id_eliminar_cliente'])){
        // Llama al método para eliminar cliente de evaluación
        echo $ins_evaluacion->eliminar_cliente_evaluacion_controlador();
    }
    // Si se solicita listar proyectos
    if(isset($_GET['listar_proyectos'])){
        // Llama al método para listar proyectos de evaluación
        echo $ins_evaluacion->listar_proyecto_evaluacion_controlador();
    }
    // Si se solicita agregar un proyecto
    if(isset($_POST['id_agregar_proyecto'])){
        // Llama al método para agregar proyecto de evaluación
        echo $ins_evaluacion->agregar_proyecto_evaluacion_controlador($_POST['id_agregar_proyecto']);
    }
    // Si se solicita eliminar un proyecto
    if(isset($_POST['id_eliminar_proyecto'])){
        // Llama al método para eliminar proyecto de evaluación
        echo $ins_evaluacion->eliminar_proyecto_evaluacion_controlador();
    }
    // Si se solicita eliminar un proveedor
    if(isset($_POST['id_eliminar_proveedor'])){
        // Llama al método para eliminar proveedor de evaluación
        echo $ins_evaluacion->eliminar_proveedor_evaluacion_controlador();
    }
    // Si se solicita eliminar un criterio
    if(isset($_POST['id_eliminar_criterio'])){
        // Llama al método para eliminar criterio de evaluación
        echo $ins_evaluacion->eliminar_criterio_evaluacion_controlador();
    }
    // Si se solicita eliminar un evaluador
    if(isset($_POST['id_eliminar_evaluador'])){
        // Llama al método para eliminar evaluador de evaluación
        echo $ins_evaluacion->eliminar_evaluador_evaluacion_controlador();
    }
    // Si se solicita eliminar una categoría
    if(isset($_POST['id_eliminar_categoria'])){
        // Llama al método para eliminar categoría de evaluación
        echo $ins_evaluacion->eliminar_categoria_evaluacion_controlador();
    }
    // Si se solicita agregar una evaluación
    if(isset($_POST['evaluacion_observacion_reg'])){
        // Llama al método para agregar evaluación
        echo $ins_evaluacion->agregar_evaluacion_controlador();
    }
    // Si se solicita eliminar una categoría
    if(isset($_POST['categoria_id_del'])){
        // Llama al método para eliminar categoría (versión 2) de evaluación
        echo $ins_evaluacion->eliminar_categoria2_evaluacion_controlador();
    }
    // Si se solicita eliminar un criterio
    if(isset($_POST['criterio_id_del'])){
        // Llama al método para eliminar criterio de evaluación
        echo $ins_evaluacion->eliminarCriterioEvaluacion();
    }
    // Si se solicita eliminar un proveedor
    if(isset($_POST['proveedor_id_del'])){
        // Llama al método para eliminar proveedor de evaluación
        echo $ins_evaluacion->eliminarProveedorEvaluacion();
    }
    // Si se solicita eliminar un evaluador
    if(isset($_POST['user_id_del'])){
        // Llama al método para eliminar evaluador de evaluación
        echo $ins_evaluacion->eliminarEvaluadorEvaluacion();
    }
    // Si se solicita enviar un recordatorio por correo
    if(isset($_POST['evaluador_id'])){
        // Llama al método para enviar correo
        echo $ins_evaluacion->enviarCorreo();
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