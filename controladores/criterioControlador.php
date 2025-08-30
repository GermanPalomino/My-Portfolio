<?php

// Verifica si la petición es AJAX para incluir el archivo de modelo correspondiente
if($peticionAjax){
	require_once "../modelos/criterioModelo.php";
}else{
	require_once "./modelos/criterioModelo.php";
}

// Definición de la clase criterioControlador que extiende de criterioModelo
class criterioControlador extends criterioModelo{

	/*----------  Controlador agregar criterio  ----------*/
	public function agregar_criterio_controlador(){
		// Limpiar y asignar los datos enviados por POST
		$nombre = mainModel::limpiar_cadena($_POST['criterio_nombre_reg']);
		$descripcion = mainModel::limpiar_cadena($_POST['criterio_descripcion_reg']);
		$peso = mainModel::limpiar_cadena($_POST['criterio_peso_reg']);
		$id_categoria = mainModel::limpiar_cadena($_POST['idcategoria_reg']);
		$tipo_pregunta = mainModel::limpiar_cadena($_POST['tipo_pregunta']);
		$descripcion_calificacion1 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_1']);
		$descripcion_calificacion2 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_2']);
		$descripcion_calificacion3 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_3']);
		$descripcion_calificacion4 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_4']);
		$descripcion_calificacion5 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_5']);

		// Comprobar que los campos no estén vacíos
		if ($nombre == "" || $peso == "" || $descripcion == "" || $tipo_pregunta == "") {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No has llenado todos los campos que son requeridos.",
				"Tipo" => "error"
			];
			return json_encode($alerta); // Devolver la respuesta como JSON
		}

		// Verificar la integridad de los datos
		if (mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}", $nombre)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El nombre del criterio no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			return json_encode($alerta); // Devolver la respuesta como JSON
		}
		if ($descripcion != "") {
			if (mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}", $descripcion)) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "La descripción no coincide con el formato solicitado. Solo se permiten los siguientes símbolos () . , # -",
					"Tipo" => "error"
				];
				return json_encode($alerta); // Devolver la respuesta como JSON
			}
		}
		if (mainModel::verificar_datos("[0-9]{1,9}", $peso)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El peso del criterio no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			return json_encode($alerta); // Devolver la respuesta como JSON
		}

		// Iniciar sesión si no está iniciada
		if (session_status() == PHP_SESSION_NONE) {
			session_start(['name'=>'xcoring']);
		}

		// Verificar si $_SESSION['datos_criterio'] está inicializado como un arreglo
		if (!isset($_SESSION['datos_criterio'])) {
			$_SESSION['datos_criterio'] = array();
		}

		// Guardar el criterio en la base de datos
		$datos_criterio_reg = [
			"Nombre" => $nombre,
			"Descripcion" => $descripcion,
			"Peso" => $peso,
			"IdCategoria" => $id_categoria,
			"TipoPregunta" => $tipo_pregunta,
			"DescripcionCalificacion1" => $descripcion_calificacion1,
			"DescripcionCalificacion2" => $descripcion_calificacion2,
			"DescripcionCalificacion3" => $descripcion_calificacion3,
			"DescripcionCalificacion4" => $descripcion_calificacion4,
			"DescripcionCalificacion5" => $descripcion_calificacion5
		];

		$agregar_criterio = criterioModelo::agregar_criterio_modelo($datos_criterio_reg);

		if ($agregar_criterio->rowCount() == 1) {
			// Generar la alerta de éxito
			$alerta = [
				"Alerta" => "redireccionar",
				"URL" => SERVERURL . "evaluacion-new/"
			];
			// Obtener el último ID insertado
			$ultimo_id = criterioModelo::obtener_ultimo_id();
			$this->guardar_datos_variable($ultimo_id);
			return json_encode($alerta); // Devolver la respuesta como JSON
		} else {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No hemos podido registrar el criterio, por favor intente nuevamente.",
				"Tipo" => "error"
			];
			return json_encode($alerta); // Devolver la respuesta como JSON
		}
	}/*-- Fin controlador --*/

	/*----------  Controlador guardar datos criterio en variable  ----------*/
	public function guardar_datos_variable($id_criterio) {
		// Obtener la información del criterio con la información de la categoría asociada
		$info_criterio = criterioModelo::obtener_info_criterio_con_categoria($id_criterio);
	
		// Verificar si el criterio ya existe en $_SESSION['datos_criterio']
		$criterio_existente = false;
		foreach ($_SESSION['datos_criterio'] as $criterio) {
			if ($criterio['ID'] == $info_criterio['id_criterio']) {
				$criterio_existente = true;
				break;
			}
		}
	
		// Si el criterio no existe, agregarlo a $_SESSION['datos_criterio']
		if (!$criterio_existente) {
			// Agregar el criterio a $_SESSION['datos_criterio']
			$_SESSION['datos_criterio'][] = [
				"ID" => $info_criterio['id_criterio'],
				"Nombre" => $info_criterio['nombre_criterio'],
				"Peso" => $info_criterio['peso_criterio'],
				"IDC" => $info_criterio['id_categoria'],
				"NombreC" => $info_criterio['nombre_categoria'],
				"PesoC" => $info_criterio['peso_categoria']
			];
		}
	} /*-- Fin controlador --*/

	/*----------  Controlador paginador criterio  ----------*/
	public function paginador_criterio_controlador($pagina, $registros, $privilegio, $url, $busqueda){
		require_once "./vistas/inc/BtnSeleccionar.php";
		$id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
		$pagina = mainModel::limpiar_cadena($pagina);
		$registros = mainModel::limpiar_cadena($registros);
		$privilegio = mainModel::limpiar_cadena($privilegio);
	
		$url = mainModel::limpiar_cadena($url);
		$url = SERVERURL.$url."/";
	
		$busqueda = mainModel::limpiar_cadena($busqueda);
		$tabla = "";
	
		$pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
	
		// Realizar la consulta SQL dependiendo de si hay una búsqueda o no
		if(isset($busqueda) && $busqueda != ""){
			$consulta = "SELECT SQL_CALC_FOUND_ROWS c.*, p.nombre_categoria
						FROM criterio c
						INNER JOIN categoria p ON c.id_categoria = p.id_categoria
						WHERE c.nombre_criterio LIKE '%$busqueda%'
						ORDER BY c.nombre_criterio ASC LIMIT $inicio, $registros";
		} else {
			$consulta = "SELECT SQL_CALC_FOUND_ROWS c.*, cat.nombre_categoria 
						FROM criterio c
						INNER JOIN categoria cat ON c.id_categoria = cat.id_categoria
						INNER JOIN proyecto p ON cat.id_proyecto = p.id_proyecto
						WHERE cat.id_proyecto = $id_proyecto
						ORDER BY cat.nombre_categoria ASC, c.nombre_criterio ASC
						LIMIT $inicio, $registros";
		}
	
		$conexion = mainModel::conectar();
		$datos = $conexion->query($consulta);
		$datos = $datos->fetchAll();
		$total = $conexion->query("SELECT FOUND_ROWS()");
		$total = (int) $total->fetchColumn();
		$Npaginas = ceil($total / $registros);
	
		// Crear la tabla HTML con los datos de los criterios
		$tabla .= '
			<div class="table-responsive">
			<table class="table table-dark table-sm">
				<thead>
					<tr class="text-center roboto-medium">
						<th>#</th>
						<th>NOMBRE</th>
						<th>PESO(%)</th>
						<th>DESCRIPCION</th>
						<th>CATEGORIA</th>';
						if($privilegio == 1 || $privilegio == 2){
							$tabla .= '<th>EDITAR</th>';
						}
						if($privilegio == 1){
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
					<tr class="text-center">
						<td>'.$contador.'</td>
						<td>'.$rows['nombre_criterio'].'</td>
						<td>'.$rows['peso_criterio'].'</td>
						<td>'.$rows['descripcion_criterio'].'</td>
						<td>'.$rows['nombre_categoria'].'</td>';
						if($privilegio == 1 || $privilegio == 2){
							$tabla .= '
							<td>
								<a href="'.SERVERURL.'criterio-update/'.mainModel::encryption($rows['id_criterio']).'/" class="btn btn-dark">
									<i class="fas fa-sync-alt"></i>    
								</a>
							</td>';
						}
						if($privilegio == 1){
							$tabla .= '
							<td>
								<form class="FormularioAjax" action="'.SERVERURL.'ajax/criterioAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off">
									<input type="hidden" name="criterio_id_del" value="'.mainModel::encryption($rows['id_criterio']).'">
									<button type="submit" class="btn btn-dark">
										<i class="far fa-trash-alt"></i>
									</button>
								</form>
							</td>';
						}
					$tabla .= '</tr>';
				$contador++;
			}
			$reg_final = $contador - 1;
		} else {
			if($total >= 1){
				$tabla .= '
					<tr class="text-center">
						<td colspan="8">
							<a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">
								Haga clic acá para recargar el listado
							</a>
						</td>
					</tr>';
			} else {
				$tabla .= '
					<tr class="text-center">
						<td colspan="8">
							No hay registros en el sistema
						</td>
					</tr>';
			}
		}
	
		$tabla .= '</tbody></table></div>';
	
		// Paginación
		if($total >= 1 && $pagina <= $Npaginas){
			$tabla .= '<p class="text-right">Mostrando criterio '.$reg_inicio.' al '.$reg_final.' de un total de '.$total.'</p>';
			$tabla .= mainModel::paginador_tablas($pagina, $Npaginas, $url, 7);
		}
	
		return $tabla;
	} /*-- Fin controlador --*/

	/*----------  Controlador seleccionar criterio para evaluación  ----------*/
	public function agregar_criterio_variable($criterio_id) {
		// Limpiar el ID del criterio
		$id_criterio = mainModel::limpiar_cadena($criterio_id);

		// Verificar si el criterio existe en la base de datos
		$check_criterio = mainModel::ejecutar_consulta_simple("SELECT * FROM criterio WHERE id_criterio = :id_criterio", array(':id_criterio' => $id_criterio));

		// Si no se encuentra el criterio, devolver un mensaje de error
		if($check_criterio->rowCount() <= 0) {
			$alerta = array(
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error inesperado",
				"Texto" => "No hemos podido agregar el criterio debido a un error, por favor inténtelo nuevamente.",
				"Tipo" => "error"
			);
			return json_encode($alerta);
		} else {
			// El criterio existe en la base de datos, obtener sus datos
			$campos = $check_criterio->fetch();

			// Iniciar la sesión si aún no se ha iniciado
			if (session_status() == PHP_SESSION_NONE) {
				session_start(['name' => 'xcoring']);
			}

			// Verificar si $_SESSION['datos_criterio'] está vacío o no está definido
			if(empty($_SESSION['datos_criterio'])) {
				// Crear la variable $_SESSION['datos_criterio'] como un array
				$_SESSION['datos_criterio'] = array();
			}

			// Agregar el criterio al array $_SESSION['datos_criterio']
			$_SESSION['datos_criterio'][] = array(
				"ID" => $campos['id_criterio'],
				"Nombre" => $campos['nombre_criterio'],
				"Peso" => $campos['peso_criterio'],
				"IDC" => $campos['id_categoria'],
				"NombreC" => $campos['nombre_categoria'],
				"PesoC" => $campos['peso_categoria']
			);

			// Devolver un mensaje de éxito
			$alerta = array(
				"Alerta" => "recargar",
				"Titulo" => "¡Criterio Seleccionado!",
				"Texto" => "El criterio se seleccionó para realizar una evaluación.",
				"Tipo" => "success"
			);
			return json_encode($alerta);
		}
	}

	/*----------  Controlador datos criterio  ----------*/
	public function datos_criterio_controlador($tipo, $id){
		$tipo = mainModel::limpiar_cadena($tipo);
		$id = mainModel::decryption($id);
		$id = mainModel::limpiar_cadena($id);

		return criterioModelo::datos_criterio_modelo($tipo, $id);
	} /*-- Fin controlador --*/

	/*----------  Controlador actualizar criterio  ----------*/
	public function actualizar_criterio_controlador() {
		session_start(['name' => 'xcoring']);
		// Recuperar el ID del criterio a actualizar
		$id = mainModel::decryption($_POST['criterio_id_up']);
		$id = mainModel::limpiar_cadena($id);

		// Comprobar que el criterio existe en la base de datos
		$check_criterio = mainModel::ejecutar_consulta_simple("SELECT * FROM criterio WHERE id_criterio='$id'");
		if ($check_criterio->rowCount() <= 0) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No hemos encontrado el criterio en el sistema.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		} else {
			$campos = $check_criterio->fetch();
		}

		// Limpiar y asignar los datos enviados por POST
		$nombre = mainModel::limpiar_cadena($_POST['criterio_nombre_up']);
		$descripcion = mainModel::limpiar_cadena($_POST['criterio_descripcion_up']);
		$peso = mainModel::limpiar_cadena($_POST['criterio_peso_up']);
		$id_categoria = mainModel::limpiar_cadena($_POST['id_categoria_criterio_up']);
		$tipo_pregunta = mainModel::limpiar_cadena($_POST['tipo_pregunta_up']);
		$descripcion_calificacion1 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_1_up']);
		$descripcion_calificacion2 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_2_up']);
		$descripcion_calificacion3 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_3_up']);
		$descripcion_calificacion4 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_4_up']);
		$descripcion_calificacion5 = mainModel::limpiar_cadena($_POST['descripcion_calificacion_5_up']);

		// Comprobar que los campos no estén vacíos
		if ($nombre == "" || $descripcion == "" || $peso == "" || $tipo_pregunta == "") {
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
		if (mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}", $nombre)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El nombre del criterio no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,1000}", $descripcion)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "La descripción no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[0-9]{1,9}", $peso)) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El peso del criterio no coincide con el formato solicitado.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		// Verificar privilegios
		if ($_SESSION['privilegio_xcoring'] < 1 || $_SESSION['privilegio_xcoring'] > 2) {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No tienes los permisos necesarios para realizar esta operación en el sistema.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		// Datos del criterio para actualizar
		$datos_criterio_up = [
			"Nombre" => $nombre,
			"Descripcion" => $descripcion,
			"Peso" => $peso,
			"IdCategoria" => $id_categoria,
			"TipoPregunta" => $tipo_pregunta,
			"DescripcionCalificacion1" => $descripcion_calificacion1,
			"DescripcionCalificacion2" => $descripcion_calificacion2,
			"DescripcionCalificacion3" => $descripcion_calificacion3,
			"DescripcionCalificacion4" => $descripcion_calificacion4,
			"DescripcionCalificacion5" => $descripcion_calificacion5,
			"ID" => $id
		];

		// Llamar al modelo para actualizar el criterio
		if (criterioModelo::actualizar_criterio_modelo($datos_criterio_up)) {
			// Actualizar la sesión si existe
			if (isset($_SESSION['datos_criterio']) && is_array($_SESSION['datos_criterio'])) {
				foreach ($_SESSION['datos_criterio'] as $key => $value) {
					if ($value['ID'] == $id) {
						$_SESSION['datos_criterio'][$key]['Nombre'] = $nombre;
						$_SESSION['datos_criterio'][$key]['Peso'] = $peso;
						$_SESSION['datos_criterio'][$key]['IDC'] = $id_categoria;
						$_SESSION['datos_criterio'][$key]['TipoPregunta'] = $tipo_pregunta;
						$_SESSION['datos_criterio'][$key]['DescripcionCalificacion1'] = $descripcion_calificacion1;
						$_SESSION['datos_criterio'][$key]['DescripcionCalificacion2'] = $descripcion_calificacion2;
						$_SESSION['datos_criterio'][$key]['DescripcionCalificacion3'] = $descripcion_calificacion3;
						$_SESSION['datos_criterio'][$key]['DescripcionCalificacion4'] = $descripcion_calificacion4;
						$_SESSION['datos_criterio'][$key]['DescripcionCalificacion5'] = $descripcion_calificacion5;
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
				"Texto" => "No hemos podido actualizar los datos del criterio, por favor intente nuevamente.",
				"Tipo" => "error"
			];
		}
		echo json_encode($alerta);
	}/*-- Fin controlador --*/

	/*----------  Controlador eliminar criterio  ----------*/
	public function eliminar_criterio_controlador(){
		// Recuperar el ID del criterio a eliminar
		$id = mainModel::decryption($_POST['criterio_id_del']);
		$id = mainModel::limpiar_cadena($id);

		// Comprobar que el criterio existe en la base de datos
		$check_criterio = mainModel::ejecutar_consulta_simple("SELECT id_criterio FROM criterio WHERE id_criterio='$id'");
		if($check_criterio->rowCount() <= 0){
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "El criterio que intenta eliminar no existe en el sistema.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		// Comprobar privilegios
		session_start(['name' => 'xcoring']);
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

		// Llamar al modelo para eliminar el criterio
		$eliminar_criterio = criterioModelo::eliminar_criterio_modelo($id);

		if($eliminar_criterio->rowCount() == 1){
			$alerta = [
				"Alerta" => "recargar",
				"Titulo" => "¡Criterio Eliminado!",
				"Texto" => "El criterio ha sido eliminado del sistema exitosamente.",
				"Tipo" => "success"
			];
		} else {
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Ocurrió un error",
				"Texto" => "No hemos podido eliminar el criterio del sistema, por favor intente nuevamente.",
				"Tipo" => "error"
			];
		}
		echo json_encode($alerta);
	} /*-- Fin controlador --*/
}