<?php

require_once "mainModel.php";

class criterioModelo extends mainModel {

    /* 
        Este modelo maneja todas las operaciones relacionadas con los criterios en la base de datos.
        Incluye métodos para agregar, obtener, actualizar y eliminar criterios.
    */

    /*----------  Modelo agregar criterio  ----------*/
    protected static function agregar_criterio_modelo($datos) {
        // Preparar la consulta SQL para insertar un nuevo criterio
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO criterio(
                nombre_criterio, 
                descripcion_criterio, 
                peso_criterio, 
                id_categoria, 
                tipopregunta_criterio, 
                descripcion_calificacion1_criterio, 
                descripcion_calificacion2_criterio, 
                descripcion_calificacion3_criterio, 
                descripcion_calificacion4_criterio, 
                descripcion_calificacion5_criterio
            ) VALUES (
                :Nombre, 
                :Descripcion, 
                :Peso, 
                :IdCategoria, 
                :TipoPregunta, 
                :DescripcionCalificacion1, 
                :DescripcionCalificacion2, 
                :DescripcionCalificacion3, 
                :DescripcionCalificacion4, 
                :DescripcionCalificacion5
            )"
        );

        // Vincular los parámetros de la consulta con los datos proporcionados
        $sql->bindParam(":Nombre", $datos['Nombre']);
        $sql->bindParam(":Descripcion", $datos['Descripcion']);
        $sql->bindParam(":Peso", $datos['Peso']);
        $sql->bindParam(":IdCategoria", $datos['IdCategoria']);
        $sql->bindParam(":TipoPregunta", $datos['TipoPregunta']);
        $sql->bindParam(":DescripcionCalificacion1", $datos['DescripcionCalificacion1']);
        $sql->bindParam(":DescripcionCalificacion2", $datos['DescripcionCalificacion2']);
        $sql->bindParam(":DescripcionCalificacion3", $datos['DescripcionCalificacion3']);
        $sql->bindParam(":DescripcionCalificacion4", $datos['DescripcionCalificacion4']);
        $sql->bindParam(":DescripcionCalificacion5", $datos['DescripcionCalificacion5']);
        
        // Ejecutar la consulta SQL
        $sql->execute();

        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }
    
    /*----------  Modelo obtener último ID  ----------*/
    public static function obtener_ultimo_id() {
        $conexion = mainModel::conectar();
        try {
            // Preparar y ejecutar la consulta para obtener el último ID insertado
            $consulta = $conexion->prepare("SELECT MAX(id_criterio) AS ultimo_id FROM criterio");
            $consulta->execute();

            // Obtener el resultado de la consulta
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            // Devolver el último ID insertado
            return $resultado['ultimo_id'];
        } catch (PDOException $e) {
            // Manejo de errores en caso de excepción
            return null;
        }
    }

    /*----------  Modelo obtener info criterio con categoria ----------*/
    public static function obtener_info_criterio_con_categoria($id_criterio) {
        $conexion = mainModel::conectar();
    
        try {
            // Preparar y ejecutar la consulta para obtener la información del criterio con la información de la categoría asociada
            $consulta = $conexion->prepare(
                "SELECT 
                    c.nombre_categoria, 
                    c.peso_categoria, 
                    cr.nombre_criterio, 
                    cr.peso_criterio, 
                    cr.id_criterio, 
                    cr.id_categoria,
                    cr.tipopregunta_criterio,
                    cr.descripcion_calificacion1_criterio,
                    cr.descripcion_calificacion2_criterio,
                    cr.descripcion_calificacion3_criterio,
                    cr.descripcion_calificacion4_criterio,
                    cr.descripcion_calificacion5_criterio
                FROM criterio AS cr 
                INNER JOIN categoria AS c ON cr.id_categoria = c.id_categoria 
                WHERE id_criterio = :IDCriterio"
            );
            $consulta->bindParam(":IDCriterio", $id_criterio);
            $consulta->execute();
    
            // Obtener el resultado de la consulta
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
            // Devolver la información del criterio con la información de la categoría asociada
            return $resultado;
        } catch (PDOException $e) {
            // Manejo de errores en caso de excepción
            return null;
        }
    }

    /*----------  Modelo datos criterio  ----------*/
    protected static function datos_criterio_modelo($tipo, $id) {
        if ($tipo == "Unico") {
            // Preparar y ejecutar la consulta para obtener los datos de un criterio específico
            $sql = mainModel::conectar()->prepare("SELECT * FROM criterio WHERE id_criterio = :ID");
            $sql->bindParam(":ID", $id);
        } elseif ($tipo == "Conteo") {
            // Preparar y ejecutar la consulta para obtener el número total de criterios
            $sql = mainModel::conectar()->prepare("SELECT id_criterio FROM criterio");
        }
        $sql->execute();
        return $sql;
    }

    /*----------  Modelo actualizar criterio  ----------*/
    protected static function actualizar_criterio_modelo($datos) {
        // Preparar la consulta SQL para actualizar los datos de un criterio
        $sql = mainModel::conectar()->prepare(
            "UPDATE criterio SET 
                nombre_criterio = :Nombre, 
                descripcion_criterio = :Descripcion, 
                peso_criterio = :Peso, 
                id_categoria = :IdCategoria,
                tipopregunta_criterio = :TipoPregunta,
                descripcion_calificacion1_criterio = :DescripcionCalificacion1,
                descripcion_calificacion2_criterio = :DescripcionCalificacion2,
                descripcion_calificacion3_criterio = :DescripcionCalificacion3,
                descripcion_calificacion4_criterio = :DescripcionCalificacion4,
                descripcion_calificacion5_criterio = :DescripcionCalificacion5
            WHERE id_criterio = :ID"
        );

        // Vincular los parámetros de la consulta con los datos proporcionados
        $sql->bindParam(":Nombre", $datos['Nombre']);
        $sql->bindParam(":Descripcion", $datos['Descripcion']);
        $sql->bindParam(":Peso", $datos['Peso']);
        $sql->bindParam(":IdCategoria", $datos['IdCategoria']);
        $sql->bindParam(":TipoPregunta", $datos['TipoPregunta']);
        $sql->bindParam(":DescripcionCalificacion1", $datos['DescripcionCalificacion1']);
        $sql->bindParam(":DescripcionCalificacion2", $datos['DescripcionCalificacion2']);
        $sql->bindParam(":DescripcionCalificacion3", $datos['DescripcionCalificacion3']);
        $sql->bindParam(":DescripcionCalificacion4", $datos['DescripcionCalificacion4']);
        $sql->bindParam(":DescripcionCalificacion5", $datos['DescripcionCalificacion5']);
        $sql->bindParam(":ID", $datos['ID']);
        
        // Ejecutar la consulta SQL
        $sql->execute();
        
        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }

    /*----------  Modelo eliminar criterio  ----------*/
    protected static function eliminar_criterio_modelo($id) {
        // Preparar la consulta SQL para eliminar un criterio
        $sql = mainModel::conectar()->prepare("DELETE FROM criterio WHERE id_criterio = :ID");

        // Vincular el parámetro de la consulta con el ID proporcionado
        $sql->bindParam(":ID", $id);
        
        // Ejecutar la consulta SQL
        $sql->execute();
        
        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }
}