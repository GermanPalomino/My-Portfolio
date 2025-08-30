<?php
require_once "mainModel.php";

class usuarioModelo extends mainModel{

    /* 
    Método: agregar_usuario_modelo
    Descripción: Agrega un nuevo usuario a la base de datos.
    Parámetros:
        - $datos (array): Datos del usuario a agregar (nombre, apellido, email, contraseña, teléfono, rol, estado, id del proyecto).
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function agregar_usuario_modelo($datos){
        $sql = mainModel::conectar()->prepare("INSERT INTO usuario(nombre_usuario, apellido_usuario, email_usuario, pss_usuario, telefono_usuario, rol_usuario, usuario_estado, id_proyecto, id_rol) VALUES(:Nombre, :Apellido, :Email, :Pss, :Telefono, :Rol, :Estado, :IdProyecto, :IdRolAsociado)");
        
        $sql->bindParam(":Nombre", $datos['Nombre']);
        $sql->bindParam(":Apellido", $datos['Apellido']);
        $sql->bindParam(":Email", $datos['Email']);
        $sql->bindParam(":Pss", $datos['Pss']);
        $sql->bindParam(":Telefono", $datos['Telefono']);
        $sql->bindParam(":Rol", $datos['Rol']);
        $sql->bindParam(":Estado", $datos['Estado']);
        $sql->bindParam(":IdProyecto", $datos['IdProyecto']);
        $sql->bindParam(":IdRolAsociado", $datos['RolAsociado']);
        $sql->execute();
    
        return $sql;
    }

    /* 
    Método: obtener_ultimo_id_insertado
    Descripción: Obtiene el último ID de usuario insertado en la base de datos.
    Retorno:
        - Último ID insertado (int|null): Último ID de usuario insertado o null en caso de error.
    */
    public static function obtener_ultimo_id_insertado() {
        $consulta = mainModel::conectar()->prepare("SELECT MAX(id_usuario) AS ultimo_id FROM usuario");
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado['ultimo_id'];
    }

    /* 
    Método: datos_usuario_modelo
    Descripción: Obtiene datos de uno o varios usuarios según el tipo de búsqueda.
    Parámetros:
        - $tipo (string): Tipo de búsqueda (Unico para un usuario específico, Conteo para el conteo total de usuarios).
        - $id (int|null): ID del usuario a buscar (opcional).
    Retorno:
        - Datos del usuario (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function datos_usuario_modelo($tipo,$id){
        if($tipo=="Unico"){
            $sql=mainModel::conectar()->prepare("SELECT * FROM usuario WHERE id_usuario=:ID");
            $sql->bindParam(":ID",$id);
        }elseif($tipo=="Conteo"){
            $sql=mainModel::conectar()->prepare("SELECT id_usuario FROM usuario WHERE id_usuario!='1'");
        }
        
        $sql->execute();
        return $sql;
    }

    /* 
    Método: actualizar_usuario_modelo
    Descripción: Actualiza los datos de un usuario en la base de datos.
    Parámetros:
        - $datos (array): Datos actualizados del usuario (nombre, apellido, email, contraseña, teléfono, rol, estado, ID).
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function actualizar_usuario_modelo($datos){
        $sql=mainModel::conectar()->prepare("UPDATE usuario SET nombre_usuario=:Nombre, apellido_usuario=:Apellido, email_usuario=:Email, pss_usuario=:Pss, telefono_usuario=:Telefono, rol_usuario=:Rol, usuario_estado=:Estado, id_rol=:RolAsociado WHERE id_usuario=:ID");
    
        $sql->bindParam(":Nombre",$datos['Nombre']);
        $sql->bindParam(":Apellido",$datos['Apellido']);
        $sql->bindParam(":Email",$datos['Email']);
        $sql->bindParam(":Pss",$datos['Pss']);
        $sql->bindParam(":Telefono",$datos['Telefono']);
        $sql->bindParam(":Rol",$datos['Rol']);
        $sql->bindParam(":Estado",$datos['Estado']);
        $sql->bindParam(":RolAsociado",$datos['RolAsociado']);
        $sql->bindParam(":ID",$datos['ID']);
        $sql->execute();
        
        return $sql;
    }    

    /* 
    Método: eliminar_usuario_modelo
    Descripción: Elimina un usuario de la base de datos.
    Parámetros:
        - $id (int): ID del usuario a eliminar.
    Retorno:
        - $sql (PDOStatement): Resultado de la ejecución de la consulta SQL.
    */
    protected static function eliminar_usuario_modelo($id){
        $sql=mainModel::conectar()->prepare("DELETE FROM usuario WHERE id_usuario=:ID");

        $sql->bindParam(":ID",$id);
        $sql->execute();
        
        return $sql;
    }

    /* 
    Método: obtenerProyectos
    Descripción: Obtiene la lista de proyectos disponibles.
    Retorno:
        - Lista de proyectos (array): Resultado de la ejecución de la consulta SQL.
    */
    protected static function obtenerProyectos() {
        $sql = mainModel::conectar()->prepare("SELECT * FROM proyecto");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}