<?php

	require_once "mainModel.php";

	class loginModelo extends mainModel{

		/* 
			Función iniciar_sesion_modelo:
			Parámetros:
				- $datos: Un array asociativo que contiene los datos de inicio de sesión del usuario, como el correo electrónico y la contraseña.
			Descripción:
				Este método permite iniciar sesión en el sistema.
				Realiza una consulta SQL para seleccionar un usuario activo con el correo electrónico y la contraseña proporcionados.
			Retorno:
				Un objeto PDOStatement que contiene el resultado de la consulta.
		*/
		protected static function iniciar_sesion_modelo($datos){
			// Consulta para seleccionar un usuario activo con el correo electrónico y contraseña proporcionados
			$sql=mainModel::conectar()->prepare("SELECT * FROM usuario WHERE email_usuario=:Email AND pss_usuario=:Pss AND usuario_estado='Activa'");
			$sql->bindParam(':Email',$datos['Email']);
			$sql->bindParam(':Pss',$datos['Pss']);
			$sql->execute();
			return $sql;
		}
		
	}
