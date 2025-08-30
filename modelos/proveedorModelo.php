<?php

    require_once "mainModel.php";

    class proveedorModelo extends mainModel{

        /* 
            Función agregar_proveedor_modelo:
            Parámetros:
                - $datos: Un array asociativo que contiene los datos del proveedor a agregar, como el nombre, el nombre de contacto, el correo electrónico, el teléfono y el ID del proyecto.
            Descripción:
                Este método permite agregar un nuevo proveedor al sistema.
                Ejecuta una consulta SQL para insertar los datos del proveedor en la tabla proveedor.
            Retorno:
                Un objeto PDOStatement que contiene el resultado de la consulta.
        */
        protected static function agregar_proveedor_modelo($datos){
            $sql=mainModel::conectar()->prepare("INSERT INTO proveedor(nombre_proveedor,nombreContacto_proveedor,emailContacto_proveedor,telefonoContacto_proveedor,id_proyecto) VALUES(:Nombre,:nombreContacto_proveedor,:Email,:Telefono,:Proyecto)");

            $sql->bindParam(":Nombre",$datos['Nombre']);
            $sql->bindParam(":nombreContacto_proveedor",$datos['nombreContacto_proveedor']);
            $sql->bindParam(":Email",$datos['Email']);
            $sql->bindParam(":Telefono",$datos['Telefono']);
            $sql->bindParam(":Proyecto",$datos['Proyecto']);
            $sql->execute();

            return $sql;
        }

        /* 
            Función obtener_ultimo_id:
            Descripción:
                Este método permite obtener el último ID insertado en la tabla de proveedores.
                Ejecuta una consulta SQL para seleccionar el máximo ID de la tabla proveedor.
            Retorno:
                El último ID insertado en la tabla de proveedores.
        */
        public static function obtener_ultimo_id() {
            $conexion = mainModel::conectar();
            try {
                // Preparar y ejecutar la consulta para obtener el último ID insertado
                $consulta = $conexion->prepare("SELECT MAX(id_proveedor) AS ultimo_id FROM proveedor");
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

        /* 
            Función obtener_info_proveedor_con_proyecto:
            Parámetros:
                - $id_proveedor: El ID del proveedor del cual se desea obtener la información.
            Descripción:
                Este método permite obtener la información de un proveedor y su proyecto asociado.
                Ejecuta una consulta SQL para seleccionar los datos del proveedor con el ID proporcionado.
            Retorno:
                Un array asociativo que contiene la información del proveedor y su proyecto asociado.
        */
        public static function obtener_info_proveedor_con_proyecto($id_proveedor) {
            $conexion = mainModel::conectar();
        
            try {
                // Preparar y ejecutar la consulta para obtener la información del proveedor con el ID proporcionado
                $consulta = $conexion->prepare("SELECT * FROM proveedor  WHERE id_proveedor=:IDProveedor");
                $consulta->bindParam(":IDProveedor", $id_proveedor);
                $consulta->execute();
        
                // Obtener el resultado de la consulta
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        
                // Devolver la información del proveedor
                return $resultado;
            } catch (PDOException $e) {
                // Manejo de errores en caso de excepción
                return null;
            }
        }

        /* 
            Función datos_proveedor_modelo:
            Parámetros:
                - $tipo: El tipo de datos a recuperar, puede ser "Unico" para un único proveedor o "Conteo" para obtener el número total de proveedores.
                - $id: El ID del proveedor si se está recuperando un único proveedor.
            Descripción:
                Este método permite obtener los datos de uno o todos los proveedores.
                Ejecuta una consulta SQL para seleccionar los datos según el tipo especificado.
            Retorno:
                Un objeto PDOStatement que contiene el resultado de la consulta.
        */
        protected static function datos_proveedor_modelo($tipo,$id){
            if($tipo=="Unico"){
                $sql=mainModel::conectar()->prepare("SELECT * FROM proveedor WHERE id_proveedor=:ID");
                $sql->bindParam(":ID",$id);
            }elseif($tipo=="Conteo"){
                $sql=mainModel::conectar()->prepare("SELECT id_proveedor FROM proveedor");
            }
            $sql->execute();
            return $sql;
        }

        /* 
            Función actualizar_proveedor_modelo:
            Parámetros:
                - $datos: Un array asociativo que contiene los datos del proveedor a actualizar, como el nombre, el nombre de contacto, el correo electrónico, el teléfono y el ID del proveedor.
            Descripción:
                Este método permite actualizar la información de un proveedor en el sistema.
                Ejecuta una consulta SQL para actualizar los datos del proveedor con el ID proporcionado.
            Retorno:
                Un objeto PDOStatement que contiene el resultado de la consulta.
        */
        protected static function actualizar_proveedor_modelo($datos){
            $sql=mainModel::conectar()->prepare("UPDATE proveedor SET nombre_proveedor=:Nombre,nombreContacto_proveedor=:nombreContacto_proveedor,emailContacto_proveedor=:Email,telefonoContacto_proveedor=:Telefono WHERE id_proveedor=:ID");
    
            $sql->bindParam(":Nombre",$datos['Nombre']);
            $sql->bindParam(":nombreContacto_proveedor",$datos['nombreContacto_proveedor']);
            $sql->bindParam(":Email",$datos['Email']);
            $sql->bindParam(":Telefono",$datos['Telefono']);
            $sql->bindParam(":ID",$datos['ID']);
            $sql->execute();
            
            return $sql;
        }

        /* 
            Función eliminar_proveedor_modelo:
            Parámetros:
                - $id: El ID del proveedor que se desea eliminar.
            Descripción:
                Este método permite eliminar un proveedor del sistema.
                Ejecuta una consulta SQL para eliminar el proveedor con el ID proporcionado.
            Retorno:
                Un objeto PDOStatement que contiene el resultado de la consulta.
        */
        protected static function eliminar_proveedor_modelo($id){
            $sql=mainModel::conectar()->prepare("DELETE FROM proveedor WHERE id_proveedor=:ID");

            $sql->bindParam(":ID",$id);
            $sql->execute();
            
            return $sql;
        }

    }