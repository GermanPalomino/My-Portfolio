<?php

require_once "mainModel.php";

class proyectoModelo extends mainModel{

    /* 
    Método: agregar_proyecto_modelo
    Descripción: Agrega un nuevo proyecto a la base de datos.
    Parámetros:
        - $datos (array): Datos del proyecto a agregar (nombre, email de contacto, id del cliente).
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function agregar_proyecto_modelo($datos){
        $sql = mainModel::conectar()->prepare("INSERT INTO proyecto(nombre_proyecto, emailContacto_proyecto, id_cliente) VALUES(:Nombre, :Email, :IdCliente)");
    
        $sql->bindParam(":Nombre", $datos['Nombre']);
        $sql->bindParam(":Email", $datos['Email']);
        $sql->bindParam(":IdCliente", $datos['IdCliente']);
        $sql->execute();
    
        return $sql;
    }

    protected static function agregar_rol_modelo($datos){
        $sql = mainModel::conectar()->prepare("INSERT INTO roles(nombre_rol, id_proyecto) VALUES(:NombreRol, :IdProyecto)");
    
        $sql->bindParam(":NombreRol", $datos['NombreRol']);
        $sql->bindParam(":IdProyecto", $datos['IdProyecto']);
        $sql->execute();
    
        return $sql;
    }

    /* 
    Método: obtener_ultimo_id
    Descripción: Obtiene el último ID de proyecto insertado en la base de datos.
    Retorno:
        - Último ID insertado (int|null): Último ID de proyecto insertado o null en caso de error.
    */
    public static function obtener_ultimo_id() {
        $conexion = mainModel::conectar();
        try {
            $consulta = $conexion->prepare("SELECT MAX(id_proyecto) AS ultimo_id FROM proyecto");
            $consulta->execute();
    
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            return $resultado['ultimo_id'];
        } catch (PDOException $e) {
            return null;
        }
    }

    /* 
    Método: obtener_info_proyecto
    Descripción: Obtiene la información de un proyecto específico.
    Parámetros:
        - $id_proyecto (int): ID del proyecto a obtener.
    Retorno:
        - Información del proyecto (array|null): Datos del proyecto o null en caso de error.
    */
    public static function obtener_info_proyecto($id_proyecto) {
        $conexion = mainModel::conectar();
    
        try {
            $consulta = $conexion->prepare("SELECT * FROM proyecto WHERE id_proyecto=:IDProyecto");
            $consulta->bindParam(":IDProyecto", $id_proyecto);
            $consulta->execute();
    
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            return null;
        }
    }

    /* 
    Método: datos_proyecto_modelo
    Descripción: Obtiene datos de uno o varios proyectos según el tipo de búsqueda.
    Parámetros:
        - $tipo (string): Tipo de búsqueda (Unico para un proyecto específico, Conteo para el conteo total de proyectos).
        - $id (int|null): ID del proyecto a buscar (opcional).
    Retorno:
        - Datos del proyecto (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function datos_proyecto_modelo($tipo, $id){
        if($tipo=="Unico"){
            $sql = mainModel::conectar()->prepare("SELECT * FROM proyecto WHERE id_proyecto=:ID");
            $sql->bindParam(":ID", $id);
        } elseif($tipo=="Conteo"){
            $sql = mainModel::conectar()->prepare("SELECT id_proyecto FROM proyecto");
        }
        $sql->execute();
        return $sql;
    }
    
    /* 
    Método: obtener_roles_proyecto_modelo
    Descripción: Obtiene los roles asociados a un proyecto.
    Parámetros:
        - $id_proyecto (int): ID del proyecto.
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function obtener_roles_proyecto_modelo($id_proyecto){
        $sql = mainModel::conectar()->prepare("SELECT nombre_rol FROM roles WHERE id_proyecto=:ID");
        $sql->bindParam(":ID", $id_proyecto);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /* 
    Método: actualizar_proyecto_modelo
    Descripción: Actualiza los datos de un proyecto en la base de datos.
    Parámetros:
        - $datos (array): Datos actualizados del proyecto (nombre, email de contacto, ID).
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function actualizar_proyecto_modelo($datos){
        $sql = mainModel::conectar()->prepare("UPDATE proyecto SET nombre_proyecto=:Nombre,emailContacto_proyecto=:Email WHERE id_proyecto=:ID");
        $sql->bindParam(":Nombre", $datos['Nombre']);
        $sql->bindParam(":Email", $datos['Email']);
        $sql->bindParam(":ID", $datos['ID']);
        $sql->execute();
        return $sql;
    }
    
    /* 
    Método: eliminar_proyecto_modelo
    Descripción: Elimina un proyecto de la base de datos.
    Parámetros:
        - $id (int): ID del proyecto a eliminar.
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function eliminar_proyecto_modelo($id){
    $sql=mainModel::conectar()->prepare("DELETE FROM proyecto WHERE id_proyecto=:ID");
    $sql->bindParam(":ID", $id);
    $sql->execute();
    
    return $sql;
    }

    /* 
    Método: eliminar_roles_proyecto_modelo
    Descripción: Elimina todos los roles asociados a un proyecto.
    Parámetros:
        - $id_proyecto (int): ID del proyecto.
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function eliminar_roles_proyecto_modelo($id_proyecto){
        $sql = mainModel::conectar()->prepare("DELETE FROM roles WHERE id_proyecto=:ID");
        $sql->bindParam(":ID", $id_proyecto);
        $sql->execute();
        return $sql;
    }
}