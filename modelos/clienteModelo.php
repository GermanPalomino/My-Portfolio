<?php

require_once "mainModel.php";

class clienteModelo extends mainModel{

    /* 
        Este modelo maneja todas las operaciones relacionadas con los clientes en la base de datos.
        Incluye métodos para agregar, obtener, actualizar y eliminar clientes.
    */

    /*----------  Modelo agregar cliente  ----------*/
    protected static function agregar_cliente_modelo($datos){
        // Preparar la consulta SQL para insertar un nuevo cliente
        $sql=mainModel::conectar()->prepare("INSERT INTO cliente(nombre_cliente,nombreContacto_cliente,telefonoContacto_cliente,emailContacto_cliente) VALUES(:Nombre,:nombreContacto_cliente,:Telefono,:Email)");

        // Vincular los parámetros de la consulta con los datos proporcionados
        $sql->bindParam(":Nombre",$datos['Nombre']);
        $sql->bindParam(":nombreContacto_cliente",$datos['nombreContacto_cliente']);
        $sql->bindParam(":Telefono",$datos['Telefono']);
        $sql->bindParam(":Email",$datos['Email']);
        
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
            $consulta = $conexion->prepare("SELECT MAX(id_cliente) AS ultimo_id FROM cliente");
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

    /*----------  Modelo obtener info cliente ----------*/
    public static function obtener_info_cliente($id_cliente) {
        $conexion = mainModel::conectar();
    
        try {
            // Preparar y ejecutar la consulta para obtener la información de un cliente específico
            $consulta = $conexion->prepare("SELECT * FROM cliente WHERE id_cliente=:IDCliente");
            $consulta->bindParam(":IDCliente", $id_cliente);
            $consulta->execute();
    
            // Obtener el resultado de la consulta
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
            // Devolver la información del cliente
            return $resultado;
        } catch (PDOException $e) {
            // Manejo de errores en caso de excepción
            return null;
        }
    }
    
    /*----------  Modelo datos cliente  ----------*/
    protected static function datos_cliente_modelo($tipo,$id){
        if($tipo=="Unico"){
            // Preparar y ejecutar la consulta para obtener los datos de un cliente específico
            $sql=mainModel::conectar()->prepare("SELECT * FROM cliente WHERE id_cliente=:ID");
            $sql->bindParam(":ID",$id);
        }elseif($tipo=="Conteo"){
            // Preparar y ejecutar la consulta para obtener el número total de clientes
            $sql=mainModel::conectar()->prepare("SELECT id_cliente FROM cliente");
        }
        $sql->execute();
        return $sql;
    }

    /*----------  Modelo actualizar cliente  ----------*/
    protected static function actualizar_cliente_modelo($datos){
        // Preparar la consulta SQL para actualizar los datos de un cliente
        $sql=mainModel::conectar()->prepare("UPDATE cliente SET nombre_cliente=:Nombre,nombreContacto_cliente=:nombreContacto_cliente,telefonoContacto_cliente=:Telefono,emailContacto_cliente=:Email WHERE id_cliente=:ID");

        // Vincular los parámetros de la consulta con los datos proporcionados
        $sql->bindParam(":Nombre",$datos['Nombre']);
        $sql->bindParam(":nombreContacto_cliente",$datos['nombreContacto_cliente']);
        $sql->bindParam(":Telefono",$datos['Telefono']);
        $sql->bindParam(":Email",$datos['Email']);
        $sql->bindParam(":ID",$datos['ID']);
        
        // Ejecutar la consulta SQL
        $sql->execute();
        
        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }

    /*----------  Modelo eliminar cliente  ----------*/
    protected static function eliminar_cliente_modelo($id){
        // Preparar la consulta SQL para eliminar un cliente
        $sql=mainModel::conectar()->prepare("DELETE FROM cliente WHERE id_cliente=:ID");

        // Vincular el parámetro de la consulta con el ID proporcionado
        $sql->bindParam(":ID",$id);
        
        // Ejecutar la consulta SQL
        $sql->execute();
        
        // Devolver el resultado de la ejecución de la consulta
        return $sql;
    }

    /*----------  Modelo obtener cliente  ----------*/
    protected static function obtenercliente() {
        // Preparar y ejecutar la consulta para obtener todos los clientes
        $sql = mainModel::conectar()->prepare("SELECT * FROM cliente");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
}