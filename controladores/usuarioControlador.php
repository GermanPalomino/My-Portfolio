<?php
// Dependiendo de si la petición es Ajax o no, se incluye el modelo correspondiente
if($peticionAjax){
	require_once "../modelos/usuarioModelo.php";
}else{
	require_once "./modelos/usuarioModelo.php";
}

// Definición de la clase usuarioControlador que extiende de usuarioModelo
class usuarioControlador extends usuarioModelo{

	// Método para agregar un nuevo usuario
	public function agregar_usuario_controlador(){
		// Iniciar sesión si no está iniciada
		session_start(['name'=>'xcoring']);
		
		// Limpiar y asignar los datos recibidos del formulario
		$nombre = mainModel::limpiar_cadena($_POST['usuario_nombre_reg']);
		$apellido = mainModel::limpiar_cadena($_POST['usuario_apellido_reg']);
		$telefono = mainModel::limpiar_cadena($_POST['usuario_telefono_reg']);
		$email = mainModel::limpiar_cadena($_POST['usuario_email_reg']);
		$clave1 = mainModel::limpiar_cadena($_POST['usuario_clave_1_reg']);
		$clave2 = mainModel::limpiar_cadena($_POST['usuario_clave_2_reg']);
		$rol_asociado = mainModel::limpiar_cadena($_POST['rol_asociado_proyecto']);    
		$privilegio = mainModel::limpiar_cadena($_POST['usuario_privilegio_reg']);
	
		// Comprobar si hay campos obligatorios vacíos
		if($nombre == "" || $apellido == "" || $email == "" || $clave1 == "" || $clave2 == ""){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No has llenado todos los campos que son requeridos.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Verificar que el nombre cumpla con el formato solicitado
		if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $nombre)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El nombre no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Verificar que el apellido cumpla con el formato solicitado
		if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $apellido)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El apellido no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Verificar que el teléfono cumpla con el formato solicitado
		if($telefono != ""){
			if(mainModel::verificar_datos("[0-9\+()]{8,20}", $telefono)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "El teléfono no coincide con el formato solicitado.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}
	
		// Verificar que el email cumpla con el formato solicitado
		if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El nombre de usuario no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Verificar que las contraseñas cumplan con el formato solicitado
		if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $clave2)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "Las contraseñas no coinciden con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Comprobar si el email ya está registrado
		if($email != ""){
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				$checkEmail = mainModel::ejecutar_consulta_simple("SELECT email_usuario FROM usuario WHERE email_usuario='$email'");
				if($checkEmail->rowCount() > 0){
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error",
						"Texto" => "El correo electrónico ingresado ya se encuentra registrado en el sistema.",
						"Tipo" => "error"
					];
					echo json_encode($alerta);
					exit();
				}
			}else{
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "Ha ingresado un correo electrónico no válido.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}
	
		// Comprobar que las contraseñas coincidan
		if($clave1 != $clave2){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "Las claves que acaba de ingresar no coinciden.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}else{
			$pss = mainModel::encryption($clave1);
		}
	
		// Comprobar que el privilegio esté en el rango permitido
		if($privilegio < 1 || $privilegio > 3){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El privilegio seleccionado no es válido.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Preparar los datos del usuario para el registro
		$datos_usuario_reg = [
			"Nombre" => $nombre,
			"Apellido" => $apellido,
			"Email" => $email,
			"Pss" => $pss,
			"Rol" => $privilegio,
			"Telefono" => $telefono,
			"Estado" => "Activa",
			"IdProyecto" => $_SESSION['datos_proyecto'][0]['IDProyecto'],
			"RolAsociado" => $rol_asociado
		];
		
		// Intentar agregar el usuario a la base de datos
		$agregar_usuario = usuarioModelo::agregar_usuario_modelo($datos_usuario_reg);
	
		if($agregar_usuario->rowCount() == 1){
			// Enviar correo de registro
			$envio_correo = $this->enviarCorreoRegistro([
				"nombre" => $nombre,
				"apellido" => $apellido,
				"email" => $email
			]);
	
			// Obtener el último ID insertado y guardar los datos del usuario en la sesión
			$ultimo_id_insertado = usuarioModelo::obtener_ultimo_id_insertado();
			$this->guardar_datos_usuario_controlador($ultimo_id_insertado, $nombre, $apellido);
	
			// Mensaje de éxito en el envío del correo
			$mensaje_correo = $envio_correo ? "El correo de registro ha sido enviado correctamente." : "No se pudo enviar el correo de registro.";
	
			// Alerta de éxito en el registro del usuario
			$alerta = [
				"Alerta" => "redireccionar",
				"URL" => SERVERURL . "evaluacion-new/"
			];
			return json_encode($alerta);
		}else{
			// Alerta de error si no se pudo registrar el usuario
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No hemos podido registrar el usuario por favor intente nuevamente.",
				"Tipo" => "error"
			];
			return json_encode($alerta);
		}
		
	} /*-- Fin controlador --*/

	// Método para guardar los datos del usuario en una variable de sesión
	public function guardar_datos_usuario_controlador($id_evaluador, $nombre, $apellido) {
		// Iniciar sesión si no está iniciada
		if (session_status() == PHP_SESSION_NONE) {
			session_start(['name'=>'xcoring']);
		}

		// Verificar si la variable de sesión está inicializada como un arreglo
		if (!isset($_SESSION['datos_usuario'])) {
			$_SESSION['datos_usuario'] = array();
		}

		// Agregar el usuario al arreglo en la variable de sesión
		$_SESSION['datos_usuario'][] = [
			"ID" => $id_evaluador,
			"Nombre" => $nombre,
			"Apellido" => $apellido
		];
	}

	// Método para enviar un correo de registro al usuario
	private function enviarCorreoRegistro($usuario_datos){
		// Asignar los datos del usuario
		$nombre = $usuario_datos['nombre'];
		$apellido = $usuario_datos['apellido'];
		$email = $usuario_datos['email'];
	
		// Asunto y mensaje del correo electrónico
		$asunto = 'Bienvenido a nuestro sitio';
		$mensaje = "Hola $nombre $apellido,\n\nGracias por registrarte en nuestro sitio. Tu cuenta ha sido creada correctamente.\n\nSaludos,\nEquipo de nuestro sitio";
	
		// Cabeceras adicionales
		$cabeceras = 'From: postmaster@localhost.com\r\n';
	
		// Enviar el correo electrónico
		return mail($email, $asunto, $mensaje, $cabeceras);
	}

	// Método para paginar los usuarios
	public function paginador_usuario_controlador($pagina, $registros, $rol, $id, $url, $busqueda){

		// Limpiar y asignar las variables recibidas
		$pagina = mainModel::limpiar_cadena($pagina);
		$registros = mainModel::limpiar_cadena($registros);
		$rol = mainModel::limpiar_cadena($rol);
		$id = mainModel::limpiar_cadena($id);

		$url = mainModel::limpiar_cadena($url);
		$url = SERVERURL . $url . "/";
		
		$busqueda = mainModel::limpiar_cadena($busqueda);
		$tabla = "";

		// Determinar el inicio de la paginación
		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		// Construir la consulta SQL dependiendo de si hay una búsqueda activa
		if(isset($busqueda) && $busqueda != ""){
			$consulta = "SELECT SQL_CALC_FOUND_ROWS u.*, p.nombre_proyecto 
			FROM usuario u
			LEFT JOIN proyecto p ON u.id_proyecto = p.id_proyecto
			WHERE ((u.id_usuario != '$id' AND u.id_usuario != '1') AND (u.nombre_usuario LIKE '%$busqueda%' OR u.apellido_usuario LIKE '%$busqueda%' OR u.telefono_usuario LIKE '%$busqueda%' OR u.email_usuario LIKE '%$busqueda%')) ORDER BY u.nombre_usuario ASC LIMIT $inicio, $registros";
		}else{
			$consulta = "SELECT SQL_CALC_FOUND_ROWS u.*, p.nombre_proyecto FROM usuario u LEFT JOIN proyecto p ON u.id_proyecto = p.id_proyecto WHERE u.id_usuario != '$id' AND u.id_usuario != '1' ORDER BY u.nombre_usuario ASC LIMIT $inicio, $registros";
		}

		// Conectar a la base de datos y ejecutar la consulta
		$conexion = mainModel::conectar();
		$datos = $conexion->query($consulta);
		$datos = $datos->fetchAll();

		// Obtener el total de registros encontrados
		$total = $conexion->query("SELECT FOUND_ROWS()");
		$total = (int) $total->fetchColumn();

		$Npaginas = ceil($total / $registros);

		// Construir la tabla de resultados
		$tabla .= '
			<div class="table-responsive">
			<table class="table table-dark table-sm">
				<thead>
					<tr class="text-center roboto-medium">
						<th>#</th>
						<th>NOMBRE</th>
						<th>APELLIDO</th>
						<th>TELÉFONO</th>
						<th>EMAIL</th>
						<th>PROYECTO</th>
						<th>EDITAR</th>
						<th>ELIMINAR</th>
					</tr>
				</thead>
				<tbody>
		';

		if($total >= 1 && $pagina <= $Npaginas){
			$contador = $inicio + 1;
			$reg_inicio = $inicio + 1;
			foreach($datos as $rows){
				$tabla .= '
					<tr class="text-center" >
						<td>'.$contador.'</td>
						<td>'.$rows['nombre_usuario'].'</td>
						<td>'.$rows['apellido_usuario'].'</td>
						<td>'.$rows['telefono_usuario'].'</td>
						<td>'.$rows['email_usuario'].'</td>
						<td>'.$rows['nombre_proyecto'].'</td> 
						<td>
							<a href="'.SERVERURL.'user-update/'.mainModel::encryption($rows['id_usuario']).'/" class="btn btn-dark">
									<i class="fas fa-sync-alt"></i>	
							</a>
						</td>
						<td>
							<form class="FormularioAjax" action="'.SERVERURL.'ajax/usuarioAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
								<input type="hidden" name="usuario_id_del" value="'.mainModel::encryption($rows['id_usuario']).'">
								<button type="submit" class="btn btn-dark">
										<i class="far fa-trash-alt"></i>
								</button>
							</form>
						</td>
					</tr>
				';
				$contador++;
			}
			$reg_final = $contador - 1;
		}else{
			if($total >= 1){
				$tabla .= '
					<tr class="text-center" >
						<td colspan="9">
							<a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">
								Haga clic acá para recargar el listado
							</a>
						</td>
					</tr>
				';
			}else{
				$tabla .= '
					<tr class="text-center" >
						<td colspan="9">
							No hay registros en el sistema
						</td>
					</tr>
				';
			}
		}

		$tabla .= '</tbody></table></div>';

		// Agregar la paginación
		if($total >= 1 && $pagina <= $Npaginas){
			$tabla .= '<p class="text-right">Mostrando usuario '.$reg_inicio.' al '.$reg_final.' de un total de '.$total.'</p>';
			$tabla .= mainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
		}

		return $tabla;
	} /*-- Fin controlador --*/

	// Método para obtener datos de un usuario específico
	public function datos_usuario_controlador($tipo, $id){
		$tipo = mainModel::limpiar_cadena($tipo);
		$id = mainModel::decryption($id);
		$id = mainModel::limpiar_cadena($id);

		return usuarioModelo::datos_usuario_modelo($tipo, $id);
	} /*-- Fin controlador --*/

	// Método para actualizar los datos de un usuario
	public function actualizar_usuario_controlador(){
		session_start(['name'=>'xcoring']);
		
		// Recuperar el ID del usuario a actualizar
		$id = mainModel::decryption($_POST['usuario_id_up']);
		$id = mainModel::limpiar_cadena($id);
	
		// Comprobar que el usuario existe en la base de datos
		$check_user = mainModel::ejecutar_consulta_simple("SELECT * FROM usuario WHERE id_usuario='$id'");
		if($check_user->rowCount() <= 0){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No hemos encontrado el usuario en el sistema.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}else{
			$campos = $check_user->fetch();
		}
		
		// Recuperar los datos del formulario
		$nombre = mainModel::limpiar_cadena($_POST['usuario_nombre_up']);
		$apellido = mainModel::limpiar_cadena($_POST['usuario_apellido_up']);
		$telefono = mainModel::limpiar_cadena($_POST['usuario_telefono_up']);
		$email = mainModel::limpiar_cadena($_POST['usuario_email_up']);
		$rol_asociado = mainModel::limpiar_cadena($_POST['rol_asociado_up']);
		
		$estado = isset($_POST['usuario_estado_up']) ? mainModel::limpiar_cadena($_POST['usuario_estado_up']) : $campos['usuario_estado'];
		$rol = isset($_POST['usuario_privilegio_up']) ? mainModel::limpiar_cadena($_POST['usuario_privilegio_up']) : $campos['rol_usuario'];
	
		$admin_email = mainModel::limpiar_cadena($_POST['email_admin']);
		$admin_pss = mainModel::limpiar_cadena($_POST['clave_admin']);
		$tipo_cuenta = mainModel::limpiar_cadena($_POST['tipo_cuenta']);
	
		// Comprobar si hay campos obligatorios vacíos
		if($nombre == "" || $apellido == "" || $email == "" || $admin_email == "" || $admin_pss == ""){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No has llenado todos los campos que son requeridos.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Verificar la integridad de los datos
		if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $nombre)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El nombre no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $apellido)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El apellido no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		if($telefono != ""){
			if(mainModel::verificar_datos("[0-9\+()]{8,20}", $telefono)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "El teléfono no coincide con el formato solicitado.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}
	
		if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $email)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El email no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{1,35}", $admin_email)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El correo no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $admin_pss)){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "Tu clave no coincide con el formato solicitado",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		$admin_pss = mainModel::encryption($admin_pss);
		
		if($rol < 1 || $rol > 3){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El privilegio seleccionado no es válido.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		if($estado != "Activa" && $estado != "Deshabilitada"){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El estado de la cuenta no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Comprobar si el nuevo email ya está registrado
		if($email != $campos['email_usuario'] && $email != ""){
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				$check_email = mainModel::ejecutar_consulta_simple("SELECT email_usuario FROM usuario WHERE email_usuario='$email'");
				if($check_email->rowCount() > 0){
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error",
						"Texto" => "El nuevo email ingresado ya se encuentra registrado en el sistema.",
						"Tipo" => "error"
					];
					echo json_encode($alerta);
					exit();
				}
			}else{
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "Ha ingresado un correo electrónico no válido.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		}
	
		// Comprobar si hay nuevas contraseñas y si coinciden
		if($_POST['usuario_clave_nueva_1'] != "" || $_POST['usuario_clave_nueva_2'] != ""){
			if($_POST['usuario_clave_nueva_1'] != $_POST['usuario_clave_nueva_2']){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "Las nuevas contraseñas que acaba de ingresar no coinciden.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}else{
				if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $_POST['usuario_clave_nueva_1']) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $_POST['usuario_clave_nueva_2'])){
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error",
						"Texto" => "Las nuevas contraseñas no coinciden con el formato solicitado.",
						"Tipo" => "error"
					];
					echo json_encode($alerta);
					exit();
				}
				$pss = mainModel::encryption($_POST['usuario_clave_nueva_1']);
			}
		}else{
			$pss = $campos['pss_usuario'];
		}
	
		// Verificar la autenticidad del usuario administrador
		if($tipo_cuenta == "Propia"){
			$check_cuenta = mainModel::ejecutar_consulta_simple("SELECT id_usuario FROM usuario WHERE email_usuario='$admin_email' AND pss_usuario='$admin_pss' AND id_usuario='$id'");
		}else{
			if($_SESSION['privilegio_xcoring'] != 1){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
	
			$check_cuenta = mainModel::ejecutar_consulta_simple("SELECT id_usuario FROM usuario WHERE email_usuario='$admin_email' AND pss_usuario='$admin_pss'");
		}
	
		if($check_cuenta->rowCount() <= 0){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "Nombre de usuario o contraseña de administrador incorrectos.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}
	
		// Preparar los datos del usuario para actualizar
		$datos_usuario_up = [
			"Nombre" => $nombre,
			"Apellido" => $apellido,
			"Telefono" => $telefono,
			"Email" => $email,
			"Pss" => $pss,
			"Rol" => $rol,
			"Estado" => $estado,
			"ID" => $id,
			"RolAsociado" => $rol_asociado
		];
	
		// Intentar actualizar los datos del usuario en la base de datos
		if(usuarioModelo::actualizar_usuario_modelo($datos_usuario_up)){
			// Actualizar los datos del usuario en la sesión si existen
			if(isset($_SESSION['datos_usuario']) && is_array($_SESSION['datos_usuario'])){
				foreach($_SESSION['datos_usuario'] as $key => $value){
					if($value['ID'] == $id){
						$_SESSION['datos_usuario'][$key]['Nombre'] = $nombre;
						$_SESSION['datos_usuario'][$key]['Apellido'] = $apellido;
						break;
					}
				}
			}
			// Actualizar los datos del usuario en la sesión si es la cuenta propia
			if($tipo_cuenta == "Propia"){
				$_SESSION['nombre_xcoring'] = $nombre;
				$_SESSION['apellido_xcoring'] = $apellido;
				$_SESSION['email_xcoring'] = $email;
			}
	
			// Alerta de éxito en la actualización del usuario
			$alerta = [
				"Alerta" => "redireccionar",
				"URL" => SERVERURL . "evaluacion-new/"
			];
		}else{
			// Alerta de error si no se pudo actualizar el usuario
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No hemos podido actualizar los datos, por favor intente nuevamente.",
				"Tipo" => "error"
			];
		}
		echo json_encode($alerta);
	}/*-- Fin controlador --*/

	// Método para eliminar un usuario
	public function eliminar_usuario_controlador(){
		// Recuperar el ID del usuario a eliminar
		$id = mainModel::decryption($_POST['usuario_id_del']);
		$id = mainModel::limpiar_cadena($id);

		// Comprobar que el usuario principal no se puede eliminar
		if($id == 1){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No podemos eliminar el usuario principal del sistema.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		// Comprobar que el usuario existe en la base de datos
		$check_usuario = mainModel::ejecutar_consulta_simple("SELECT id_usuario FROM usuario WHERE id_usuario='$id'");
		if($check_usuario->rowCount() <= 0){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El usuario que intenta eliminar no existe en el sistema.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		// Comprobar los privilegios del usuario actual
		session_start(['name'=>'xcoring']);
		if($_SESSION['privilegio_xcoring'] != 1){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		// Intentar eliminar el usuario de la base de datos
		$eliminar_usuario = usuarioModelo::eliminar_usuario_modelo($id);

		if($eliminar_usuario->rowCount() == 1){
			// Alerta de éxito en la eliminación del usuario
			$alerta = [
				"Alerta" => "recargar",
				"Titulo" => "¡Usuario Eliminado!",
				"Texto" => "El usuario ha sido eliminado del sistema exitosamente.",
				"Tipo" => "success"
			];
		}else{
			// Alerta de error si no se pudo eliminar el usuario
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No hemos podido eliminar el usuario del sistema, por favor intente nuevamente.",
				"Tipo" => "error"
			];
		}
		echo json_encode($alerta);
	} /*-- Fin controlador --*/
}