<?php

require_once "mainModel.php";

class categoriaModelo extends mainModel{

    /* 
        Este modelo maneja todas las operaciones relacionadas con las categorías en la base de datos.
        Incluye métodos para agregar, obtener, actualizar y eliminar categorías.
    */

    /*----------  Modelo agregar categoria  ----------*/
    protected static function agregar_categoria_modelo($datos){
        // Preparar la consulta SQL para insertar una nueva categoría
        $sql=mainModel::conectar()->prepare("INSERT INTO categoria(nombre_categoria, peso_categoria, descripcion_categoria, id_proyecto, id_rol) VALUES(:Nombre, :Peso, :Descripcion, :IdProyecto, :IdRol)");
    
        // Vincular los parámetros de la consulta con los datos proporcionados
        $sql->bindParam(":Nombre", $datos['Nombre']);
        $sql->bindParam(":Peso", $datos['Peso']);
        $sql->bindParam(":Descripcion", $datos['Descripcion']);
        $sql->bindParam(":IdProyecto", $datos['IdProyecto']);
        $sql->bindParam(":IdRol", $datos['IdRol']); 
        
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
            $consulta = $conexion->prepare("SELECT MAX(id_categoria) AS ultimo_id FROM categoria");
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

    /*----------  Modelo obtener info categoria ----------*/
    public static function obtener_info_categoria($id_categoria) {
        $conexion = mainModel::conectar();
    
        try {
            // Preparar y ejecutar la consulta para obtener la información de una categoría específica
            $consulta = $conexion->prepare("SELECT * FROM categoria WHERE id_categoria=:ID");
            $consulta->bindParam(":ID", $id_categoria);
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

	/*----------  Modelo datos categoria  ----------*/
    protected static function datos_categoria_modelo($tipo,$id){
        if($tipo=="Unico"){
            // Preparar y ejecutar la consulta para obtener los datos de una categoría específica
            $sql=mainModel::conectar()->prepare("SELECT * FROM categoria WHERE id_categoria=:ID");
            $sql->bindParam(":ID",$id);
        }elseif($tipo=="Conteo"){
            // Preparar y ejecutar la consulta para obtener el número total de categorías
            $sql=mainModel::conectar()->prepare("SELECT id_categoria FROM categoria");
        }
        // Ejecutar la consulta
        $sql->execute();
        return $sql;
    }

    /*----------  Modelo actualizar categoria  ----------*/
    protected static function actualizar_categoria_modelo($datos){
        // Preparar la consulta SQL para actualizar los datos de una categoría
        $sql=mainModel::conectar()->prepare("UPDATE categoria SET nombre_categoria=:Nombre, peso_categoria=:Peso, descripcion_categoria=:Descripcion, id_rol=:IdRol WHERE id_categoria=:ID");
    
        // Vincular los parámetros de la consulta con los datos proporcionados
        $sql->bindParam(":Nombre", $datos['Nombre']);
        $sql->bindParam(":Peso", $datos['Peso']);
        $sql->bindParam(":Descripcion", $datos['Descripcion']);
        $sql->bindParam(":IdRol", $datos['IdRol']);  // Vincular el id_rol
        $sql->bindParam(":ID", $datos['ID']);
        
        // Ejecutar la consulta SQL
        $sql->execute();
        
        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }

    /*----------  Modelo eliminar categoria  ----------*/
    protected static function eliminar_categoria_modelo($id){
        // Preparar la consulta SQL para eliminar una categoría
        $sql=mainModel::conectar()->prepare("DELETE FROM categoria WHERE id_categoria=:ID");

        // Vincular el parámetro de la consulta con el ID proporcionado
        $sql->bindParam(":ID",$id);
        
        // Ejecutar la consulta SQL
        $sql->execute();
        
        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }
}