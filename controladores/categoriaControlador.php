	<?php

	// Verifica si la petición es AJAX para incluir el archivo de modelo correspondiente
	if($peticionAjax){
		require_once "../modelos/categoriaModelo.php";
	}else{
		require_once "./modelos/categoriaModelo.php";
	}

	// Definición de la clase categoriaControlador que extiende de categoriaModelo
	class categoriaControlador extends categoriaModelo{

		/*----------  Controlador agregar categoría  ----------*/
		public function agregar_categoria_controlador(){
			// Iniciar la sesión con el nombre 'xcoring'
			session_start(['name' => 'xcoring']);
			
			// Limpiar y asignar los datos enviados por POST
			$nombre = mainModel::limpiar_cadena($_POST['categoria_nombre_reg']);
			$peso = mainModel::limpiar_cadena($_POST['categoria_peso_reg']);
			$descripcion = mainModel::limpiar_cadena($_POST['categoria_descripcion_reg']);
			$id_rol = mainModel::limpiar_cadena($_POST['id_rol_reg']);  // Añadir la limpieza del id_rol
		
			// Comprobar que los campos no estén vacíos
			if($nombre == "" || $peso == "" || $descripcion == "" || $id_rol == ""){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No has llenado todos los campos que son requeridos.",
					"Tipo" => "error"
				];
				return json_encode($alerta);
			}
		
			// Verificar la integridad de los datos
			if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,35}", $nombre)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "El nombre no coincide con el formato solicitado.",
					"Tipo" => "error"
				];
				return json_encode($alerta);
			}
		
			if(mainModel::verificar_datos("[0-9]{1,9}", $peso)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "El peso no coincide con el formato solicitado.",
					"Tipo" => "error"
				];
				return json_encode($alerta);
			}
		
			if($descripcion != ""){
				if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}", $descripcion)){
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error",
						"Texto" => "El detalle no coincide con el formato solicitado. Solo se permiten los siguientes símbolos () . , # -",
						"Tipo" => "error"
					];
					return json_encode($alerta);
				}
			}
		
			// Iniciar sesión si no está iniciada
			if (session_status() == PHP_SESSION_NONE) {
				session_start(['name'=>'xcoring']);
			}
		
			// Verificar si $_SESSION['datos_categoria'] está inicializado como un arreglo
			if (!isset($_SESSION['datos_categoria'])) {
				$_SESSION['datos_categoria'] = array();
			}
		
			// Datos de la categoría para registrar
			$datos_categoria_reg = [
				"Nombre" => $nombre,
				"Peso" => $peso,
				"Descripcion" => $descripcion,
				"IdProyecto" => $_SESSION['datos_proyecto'][0]['IDProyecto'],
				"IdRol" => $id_rol  // Añadir el id_rol a los datos de la categoría
			];
		
			// Llamar al modelo para agregar la categoría
			$agregar_categoria = categoriaModelo::agregar_categoria_modelo($datos_categoria_reg);
			
			if($agregar_categoria->rowCount() == 1){
				$ultimo_id = categoriaModelo::obtener_ultimo_id();
				$this->guardar_datos_variable($ultimo_id);
				$alerta = [
					"Alerta" => "redireccionar",
					"URL" => SERVERURL . "evaluacion-new/"
				];
				return json_encode($alerta); // Devolver la respuesta como JSON
			} else {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No hemos podido registrar la categoría, por favor intente nuevamente.",
					"Tipo" => "error"
				];
				return json_encode($alerta);
			}
		}/*-- Fin controlador --*/

		/*----------  Controlador guardar datos categoría en variable  ----------*/
		public function guardar_datos_variable($id_categoria) {
			// Obtener la información de la categoría
			$info_categoria = categoriaModelo::obtener_info_categoria($id_categoria);
		
			// Verificar si la categoría ya existe en $_SESSION['datos_categoria']
			$categoria_existente = false;
			foreach ($_SESSION['datos_categoria'] as $categoria) {
				if ($categoria['ID'] == $info_categoria['id_categoria']) {
					$categoria_existente = true;
					break;
				}
			}
		
			// Si la categoría no existe, agregarla a $_SESSION['datos_categoria']
			if (!$categoria_existente) {
				// Agregar la categoría a $_SESSION['datos_categoria']
				$_SESSION['datos_categoria'][] = [
					"ID" => $info_categoria['id_categoria'],
					"Nombre" => $info_categoria['nombre_categoria'],
					"Peso" => $info_categoria['peso_categoria']
				];
			}
		} /*-- Fin controlador --*/

		/*----------  Controlador paginador categoría  ----------*/
		public function paginador_categoria_controlador($pagina, $registros, $privilegio, $url, $busqueda){
			//Llamar al IDProyecto guardado en la variable sesion para hacer la consulta en base en el 
			$id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
			//Metodos para el paginador y tabla
			$pagina = mainModel::limpiar_cadena($pagina);
			$registros = mainModel::limpiar_cadena($registros);
			$privilegio = mainModel::limpiar_cadena($privilegio);
		
			$url = mainModel::limpiar_cadena($url);
			$url = SERVERURL.$url."/";
		
			$busqueda = mainModel::limpiar_cadena($busqueda);
			$tabla = "";
		
			$pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
			$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
		
			// Realizar la consulta SQL dependiendo de si hay una búsqueda o no
			if(isset($busqueda) && $busqueda != ""){
				$consulta = "SELECT SQL_CALC_FOUND_ROWS c.*, p.nombre_proyecto 
							FROM categoria c
							INNER JOIN proyecto p ON c.id_proyecto = p.id_proyecto
							WHERE c.nombre_categoria LIKE '%$busqueda%'
							ORDER BY c.nombre_categoria ASC LIMIT $inicio,$registros";
			} else {
				$consulta = "SELECT SQL_CALC_FOUND_ROWS c.*, p.nombre_proyecto 
							FROM categoria c
							INNER JOIN proyecto p ON c.id_proyecto = p.id_proyecto
							WHERE p.id_proyecto = $id_proyecto
							ORDER BY c.nombre_categoria ASC
							LIMIT $inicio, $registros";
			}
		
			$conexion = mainModel::conectar();
		
			$datos = $conexion->query($consulta);
			$datos = $datos->fetchAll();
			$total = $conexion->query("SELECT FOUND_ROWS()");
			$total = (int)$total->fetchColumn();
			$Npaginas = ceil($total / $registros);
		
			// Crear la tabla HTML con los datos de las categorías
			$tabla .= '
				<div class="table-responsive">
				<table class="table table-dark table-sm">
					<thead>
						<tr class="text-center roboto-medium">
							<th>#</th>
							<th>NOMBRE</th>
							<th>PESO(%)</th>
							<th>DESCRIPCION</th>';
							if($privilegio == 1 || $privilegio == 2){
								$tabla .= '<th>EDITAR</th>';
							}
							if($privilegio == 1 || $privilegio == 2){
								$tabla .= '<th>ELIMINAR</th>';
							}
						$tabla .= '</tr>
					</thead>
					<tbody>
			';
		
			// Verificar si hay datos para mostrar
			if($total >= 1 && $pagina <= $Npaginas){
				$contador = $inicio + 1;
				$reg_inicio = $inicio + 1;
				foreach($datos as $rows){
					$tabla .= '
							<tr class="text-center" >
								<td>'.$contador.'</td>
								<td>'.$rows['nombre_categoria'].'</td>
								<td>'.$rows['peso_categoria'].'</td>
								<td>'.$rows['descripcion_categoria'].'</td>';
								if($privilegio == 1 || $privilegio == 2){
									$tabla .= '
										<td>
											<a href="'.SERVERURL.'categoria-update/'.mainModel::encryption($rows['id_categoria']).'/" class="btn btn-dark">
												<i class="fas fa-sync-alt"></i>    
											</a>
										</td>
									';
								}
								if($privilegio == 1 || $privilegio == 2){
									$tabla .= '
										<td>
											<form class="FormularioAjax" action="'.SERVERURL.'ajax/categoriaAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
												<input type="hidden" name="categoria_id_del" value="'.mainModel::encryption($rows['id_categoria']).'">
												<button type="submit" class="btn btn-dark">
														<i class="far fa-trash-alt"></i>
												</button>
											</form>
										</td>
									';
								}
							$tabla .= '</tr>
						';
					$contador++;
				}
				$reg_final = $contador - 1;
			} else {
				if($total >= 1){
					$tabla .= '
						<tr class="text-center" >
							<td colspan="8">
								<a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">
									Haga clic acá para recargar el listado
								</a>
							</td>
						</tr>
					';
				} else {
					$tabla .= '
						<tr class="text-center" >
							<td colspan="8">
								No hay registros en el sistema
							</td>
						</tr>
					';
				}
			}
		
			$tabla .= '</tbody></table></div>';
		
			// Paginación
			if($total >= 1 && $pagina <= $Npaginas){
				$tabla .= '<p class="text-right">Mostrando categorías '.$reg_inicio.' al '.$reg_final.' de un total de '.$total.'</p>';
		
				$tabla .= mainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
			}
		
			return $tabla;
		} /*-- Fin controlador --*/

		/*----------  Controlador seleccionar categoría para evaluación  ----------*/
		public function agregar_categoria_variable($categoria_id) {
			// Limpiar el ID de la categoría
			$id_categoria = mainModel::limpiar_cadena($categoria_id);

			// Verificar si la categoría existe en la base de datos
			$check_categoria = mainModel::ejecutar_consulta_simple("SELECT * FROM categoria WHERE id_categoria = :id_categoria", array(':id_categoria' => $id_categoria));

			// Si no se encuentra la categoría, devolver un mensaje de error
			if($check_categoria->rowCount() <= 0) {
				$alerta = array(
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "No hemos podido agregar la categoría debido a un error, por favor inténtelo nuevamente.",
					"Tipo" => "error"
				);
				return json_encode($alerta);
			} else {
				// La categoría existe en la base de datos, obtener sus datos
				$campos = $check_categoria->fetch();

				// Iniciar la sesión si aún no se ha iniciado
				if (session_status() == PHP_SESSION_NONE) {
					session_start(['name' => 'xcoring']);
				}

				// Verificar si $_SESSION['datos_categoria'] está vacío o no está definido
				if(empty($_SESSION['datos_categoria'])) {
					// Crear la variable $_SESSION['datos_categoria'] como un array
					$_SESSION['datos_categoria'] = array();
				}

				// Agregar la categoría al array $_SESSION['datos_categoria']
				$_SESSION['datos_categoria'][] = array(
					"ID" => $campos['id_categoria'],
					"Nombre" => $campos['nombre_categoria'],
					"Peso"=> $campos['peso_categoria']
				);

				// Devolver un mensaje de éxito
				$alerta = array(
					"Alerta" => "recargar",
					"Titulo" => "¡Categoría Seleccionada!",
					"Texto" => "La categoría se seleccionó para realizar una evaluación.",
					"Tipo" => "success"
				);
				return json_encode($alerta);
			}
		}

		/*----------  Controlador datos categoría  ----------*/
		public function datos_categoria_controlador($tipo, $id){
			$tipo = mainModel::limpiar_cadena($tipo);
			$id = mainModel::decryption($id);
			$id = mainModel::limpiar_cadena($id);

			return categoriaModelo::datos_categoria_modelo($tipo, $id);
		} /*-- Fin controlador --*/

		/*----------  Controlador actualizar categoría  ----------*/
		public function actualizar_categoria_controlador(){
			session_start(['name' => 'xcoring']);
			// Recuperar el ID de la categoría a actualizar
			$id = mainModel::decryption($_POST['categoria_id_up']);
			$id = mainModel::limpiar_cadena($id);
		
			// Comprobar que la categoría existe en la base de datos
			$check_categoria = mainModel::ejecutar_consulta_simple("SELECT * FROM categoria WHERE id_categoria='$id'");
			if($check_categoria->rowCount() <= 0){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No hemos encontrado la categoría en el sistema.",
					"Tipo" => "error"
				];
				return json_encode($alerta);
			} else {
				$campos = $check_categoria->fetch();
			}
		
			// Limpiar y asignar los datos enviados por POST
			$nombre = mainModel::limpiar_cadena($_POST['categoria_nombre_up']);
			$peso = mainModel::limpiar_cadena($_POST['categoria_peso_up']);
			$descripcion = mainModel::limpiar_cadena($_POST['categoria_descripcion_up']);
			$id_rol = mainModel::limpiar_cadena($_POST['id_rol_up']);  // Añadir la limpieza del id_rol
		
			// Comprobar que los campos no estén vacíos
			if($nombre == "" || $peso == "" || $descripcion == "" || $id_rol == ""){
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
			if(mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}", $nombre)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "El nombre no coincide con el formato solicitado.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if(mainModel::verificar_datos("[0-9]{1,9}", $peso)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "El peso no coincide con el formato solicitado.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			if(mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}", $descripcion)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "La descripción no coincide con el formato solicitado.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			// Verificar privilegios
			if($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}
		
			// Datos de la categoría para actualizar
			$datos_categoria_up = [
				"Nombre" => $nombre,
				"Peso" => $peso,
				"Descripcion" => $descripcion,
				"IdRol" => $id_rol,  // Añadir el id_rol a los datos de la categoría
				"ID" => $id
			];
		
			// Llamar al modelo para actualizar la categoría
			if(categoriaModelo::actualizar_categoria_modelo($datos_categoria_up)){
				// Actualizar la sesión si existe
				if(isset($_SESSION['datos_categoria']) && is_array($_SESSION['datos_categoria'])){
					foreach($_SESSION['datos_categoria'] as $key => $value){
						if($value['ID'] == $id){
							$_SESSION['datos_categoria'][$key]['Nombre'] = $nombre;
							$_SESSION['datos_categoria'][$key]['Peso'] = $peso;
							// Puedes añadir la descripción si también se requiere mantener en sesión
							break;
						}
					}
				}
				$alerta = [
					"Alerta" => "redireccionar",
					"URL" => SERVERURL . "evaluacion-new/"
				];
			} else {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No hemos podido actualizar los datos de la categoría, por favor intente nuevamente.",
					"Tipo" => "error"
				];
			}
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*----------  Controlador eliminar categoría  ----------*/
		public function eliminar_categoria_controlador(){
			// Recuperar el ID de la categoría a eliminar
			$id = mainModel::decryption($_POST['categoria_id_del']);
			$id = mainModel::limpiar_cadena($id);

			// Comprobar que la categoría existe en la base de datos
			$check_item = mainModel::ejecutar_consulta_simple("SELECT id_categoria FROM categoria WHERE id_categoria='$id'");
			if($check_item->rowCount() <= 0){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "La categoría que intenta eliminar no existe en el sistema.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}

			// Comprobar privilegios
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

			/*== Comprobando detalle prestamos ==*/
			/*$check_prestamos=mainModel::ejecutar_consulta_simple("SELECT id_categoria FROM detalle WHERE item_id='$id' LIMIT 1");
			if($check_prestamos->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error",
					"Texto"=>"No podemos eliminar el item debido a que tiene prestamos asociados, recomendamos deshabilitar este item si ya no será usado en el sistema.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}*/
			// Llamar al modelo para eliminar la categoría
			
			$eliminar_categoria = categoriaModelo::eliminar_categoria_modelo($id);

			if($eliminar_categoria->rowCount() == 1){
				$alerta = [
					"Alerta" => "recargar",
					"Titulo" => "¡Categoría Eliminada!",
					"Texto" => "La categoría ha sido eliminada del sistema exitosamente.",
					"Tipo" => "success"
				];
			}else{
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No hemos podido eliminar la categoría del sistema, por favor intente nuevamente.",
					"Tipo" => "error"
				];
			}
			echo json_encode($alerta);
		} /*-- Fin controlador --*/
	}