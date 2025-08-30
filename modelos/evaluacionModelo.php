<?php

require_once "mainModel.php";

class evaluacionModelo extends mainModel{
    /* 
        Este modelo maneja todas las operaciones relacionadas con las evaluaciones en la base de datos.
        Incluye métodos para agregar, eliminar y realizar operaciones relacionadas con las evaluaciones.
    */

    /*----------  Modelo agregar evaluacion  ----------*/
    protected static function agregar_evaluacion_modelo($datos){
        // Preparar la consulta SQL para agregar una nueva evaluación
        $sql=mainModel::conectar()->prepare("INSERT INTO evaluacion(id_cliente,id_proyecto,id_usuario,id_proveedor,id_categoria,id_criterio,evaluacion_observacion) VALUES (:Cliente,:Proyecto,:Usuario,:Proveedor,:Categoria,:Criterio,:Observacion)");

        // Vincular los parámetros de la consulta con los datos proporcionados
        $sql->bindParam(":Cliente",$datos['Cliente']);
        $sql->bindParam(":Proyecto",$datos['Proyecto']);
        $sql->bindParam(":Usuario",$datos['Usuario']);
        $sql->bindParam(":Proveedor",$datos['Proveedor']);
        $sql->bindParam(":Categoria",$datos['Categoria']);
        $sql->bindParam(":Criterio",$datos['Criterio']);
        $sql->bindParam(":Observacion",$datos['Observacion']);
        
        // Ejecutar la consulta SQL
        $sql->execute();

        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }

    /*-- Modelo para eliminar una categoría y sus criterios asociados --*/
    public static function eliminar_categoria_y_criterios_modelo($id_categoria) {
        // Iniciar una transacción para garantizar la consistencia de los datos
        $conexion = mainModel::conectar();
        $conexion->beginTransaction(); 

        try {
            // Eliminar las evaluaciones asociadas a los criterios de la categoría
            $query_evaluacion = "DELETE FROM evaluacion WHERE id_criterio IN (
                                    SELECT id_criterio FROM criterio WHERE id_categoria = :id_categoria
                                )";
            $eliminar_evaluacion = $conexion->prepare($query_evaluacion);
            $eliminar_evaluacion->bindParam(":id_categoria", $id_categoria, PDO::PARAM_INT);
            $eliminar_evaluacion->execute();

            // Confirmar la transacción
            $conexion->commit();

            // Devolver el resultado de la eliminación de la categoría
            return $eliminar_evaluacion;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conexion->rollback();
            // También puedes registrar o manejar el error de otra manera si es necesario
            return false;
        }
    }

    /*-- Modelo para eliminar criterios en evaluaciones --*/
    public static function eliminarCriterioEnEvaluacionModelo($criterio_id){
        try {
            // Realizar la eliminación de los criterios de la tabla de evaluación
            $consulta = "DELETE FROM evaluacion WHERE id_criterio = :criterio_id";

            // Preparar la sentencia
            $stmt = mainModel::conectar()->prepare($consulta);

            // Vincular parámetros
            $stmt->bindParam(":criterio_id", $criterio_id, PDO::PARAM_INT);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Si la eliminación fue exitosa, devolver verdadero
                return true;
            } else {
                // Si ocurrió un error durante la eliminación, devolver falso
                return false;
            }
        } catch (PDOException $e) {
            // Si ocurrió una excepción, imprimir el mensaje de error y devolver falso
            error_log("Error en EvaluacionModelo::eliminarCriterioEnEvaluacionModelo(): " . $e->getMessage());
            return false;
        }
    }

    /*-- Modelo para eliminar proveedores en evaluaciones --*/
    public static function eliminarProveedorEnEvaluacionModelo($proveedor_id){
        try {
            // Realizar la eliminación de las entradas asociadas al proveedor en la tabla de evaluación
            $consulta = "DELETE FROM evaluacion WHERE id_proveedor = :proveedor_id";

            // Preparar la sentencia
            $stmt = mainModel::conectar()->prepare($consulta);

            // Vincular parámetros
            $stmt->bindParam(":proveedor_id", $proveedor_id, PDO::PARAM_INT);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Si la eliminación fue exitosa, devolver verdadero
                return true;
            } else {
                // Si ocurrió un error durante la eliminación, devolver falso
                return false;
            }
        } catch (PDOException $e) {
            // Si ocurrió una excepción, imprimir el mensaje de error y devolver falso
            error_log("Error en EvaluacionModelo::eliminarProveedorEnEvaluacionModelo(): " . $e->getMessage());
            return false;
        }
    }

    /*-- Modelo para eliminar usuarios en evaluaciones --*/
    public static function eliminarUsuarioEnEvaluacionModelo($usuario_id){
        try {
            // Realizar la eliminación de las entradas asociadas al usuario en la tabla de evaluación
            $consulta = "DELETE FROM evaluacion WHERE id_usuario = :usuario_id";
    
            // Preparar la sentencia
            $stmt = mainModel::conectar()->prepare($consulta);
    
            // Vincular parámetros
            $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
    
            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Si la eliminación fue exitosa, devolver verdadero
                return true;
            } else {
                // Si ocurrió un error durante la eliminación, devolver falso
                return false;
            }
        } catch (PDOException $e) {
            // Si ocurrió una excepción, imprimir el mensaje de error y devolver falso
            error_log("Error en EvaluacionModelo::eliminarUsuarioEnEvaluacionModelo(): " . $e->getMessage());
            return false;
        }
    }
}