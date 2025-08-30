<?php
	// Verifica si la petición es AJAX para incluir el archivo de modelo correspondiente
    if($peticionAjax){
		require_once "../modelos/evaluacionModelo.php";
	}else{
		require_once "./modelos/evaluacionModelo.php";
    }
	// Definición de la clase evaluacionControlador que extiende de evaluacionModelo
    class evaluacionControlador extends evaluacionModelo{

		/*----------  Controlador listar clientes evaluación ----------*/
		public function listar_clientes_evaluacion_controlador() {
			// Ejecuta una consulta para obtener todos los registros de la tabla 'cliente'
			$clientes = mainModel::ejecutar_consulta_simple("SELECT * FROM cliente");

			// Inicializa las opciones del dropdown con una opción por defecto
			$options = '<option value="">Selecciona un cliente</option>';

			// Verifica si se encontraron registros
			if ($clientes->rowCount() >= 1) {
				// Recorre cada registro de la tabla 'cliente'
				while ($cliente = $clientes->fetch(PDO::FETCH_ASSOC)) {
					// Añade una opción al dropdown por cada cliente encontrado
					$options .= '<option value="' . $cliente['id_cliente'] . '">' . $cliente['nombre_cliente'] . '</option>';
				}
			}

			// Retorna las opciones generadas para el dropdown
			return $options;
		} /*-- Fin controlador --*/

		/*----------  Controlador seleccionar cliente evaluacion ----------*/
		public function agregar_cliente_evaluacion_controlador($id_agregar_cliente) {
			// Limpiamos el ID del cliente
			$id_cliente = mainModel::limpiar_cadena($id_agregar_cliente);
		
			// Verificamos si el cliente existe en la base de datos
			$check_cliente = mainModel::ejecutar_consulta_simple("SELECT * FROM cliente WHERE id_cliente = :id_cliente", array(':id_cliente' => $id_cliente));
		
			// Si no se encuentra el cliente, devolvemos un mensaje de error
			if($check_cliente->rowCount() <= 0) {
				$alerta = array(
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error inesperado",
					"Texto" => "No hemos podido agregar el cliente debido a un error, por favor inténtelo nuevamente.",
					"Tipo" => "error"
				);
				return json_encode($alerta);
			} else {
				// El cliente existe en la base de datos, obtenemos sus datos
				$campos = $check_cliente->fetch();
		
				// Iniciamos la sesión si aún no se ha iniciado
				if (session_status() == PHP_SESSION_NONE) {
					session_start(['name' => 'xcoring']);
				}
		
				// Verificamos si $_SESSION['datos_cliente'] está vacío o no está definido
				if(empty($_SESSION['datos_cliente'])) {
					// Creamos la variable $_SESSION['datos_cliente'] como un array
					$_SESSION['datos_cliente'] = array();
				}
		
				// Agregamos el cliente al array $_SESSION['datos_cliente']
				$_SESSION['datos_cliente'][] = array(
					"IDCliente" => $campos['id_cliente'],
					"NombreCliente" => $campos['nombre_cliente']
				);
		
				// Devolvemos un mensaje de éxito
				$alerta = array(
					"Alerta" => "recargar",
					"Titulo" => "¡Cliente Seleccionado!",
					"Texto" => "El cliente se seleccionó para realizar una evaluación.",
					"Tipo" => "success"
				);
				return json_encode($alerta);
			}
		}	/*-- Fin controlador --*/	
		
		/*----------  Controlador eliminar cliente evaluacion ----------*/
		public function eliminar_cliente_evaluacion_controlador() {

			/*== Iniciando la sesión ==*/
			session_start(['name' => 'xcoring']);

			// Verificar si existen datos del proyecto y eliminarlos si es necesario
			if (isset($_SESSION['datos_proyecto'])) {
				// Eliminar los datos del proyecto de la sesión
				unset($_SESSION['datos_proyecto']);
			}
			if (isset($_SESSION['datos_criterio'])) {
				// Eliminar los datos del criterio de la sesión
				unset($_SESSION['datos_criterio']);
			}
			if (isset($_SESSION['datos_proveedor'])) {
				// Eliminar los datos del proveedor de la sesión
				unset($_SESSION['datos_proveedor']);
			}
			if (isset($_SESSION['datos_usuario'])) {
				// Eliminar los datos del usuario de la sesión
				unset($_SESSION['datos_usuario']);
			}
			if (isset($_SESSION['datos_categoria'])) {
				// Eliminar los datos de la categoría de la sesión
				unset($_SESSION['datos_categoria']);
			}
			// Eliminar los datos del cliente de la sesión
			unset($_SESSION['datos_cliente']);

			// Verificar si los datos del cliente han sido eliminados correctamente
			if (empty($_SESSION['datos_cliente'])) {
				// Crear una alerta de éxito si los datos del cliente fueron eliminados
				$alerta = [
					"Alerta" => "redireccionar",
					"URL" => SERVERURL . "home/"
				];
			} else {
				// Crear una alerta de error si los datos del cliente no pudieron ser eliminados
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No hemos podido deseleccionar el cliente, por favor intente nuevamente.",
					"Tipo" => "error"
				];
			}
			// Devolver la alerta como JSON
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*----------  Controlador listar proyecto evaluacion ----------*/
		public function listar_proyecto_evaluacion_controlador() {

			/*== Iniciando la sesión ==*/
			session_start(['name' => 'xcoring']);

			/*== Recuperando el ID del cliente desde la sesión ==*/
			$cliente = $_SESSION['datos_cliente'][0]['IDCliente'];

			// Consultar los proyectos del cliente
			$proyectos = mainModel::ejecutar_consulta_simple("SELECT * FROM proyecto WHERE id_cliente = '$cliente'");

			// Inicializar las opciones del selector con una opción predeterminada
			$options = '<option value="">Selecciona un proyecto</option>';

			// Verificar si hay proyectos y agregar cada proyecto como una opción
			if ($proyectos->rowCount() >= 1) {
				while ($proyecto = $proyectos->fetch(PDO::FETCH_ASSOC)) {
					$options .= '<option value="' . $proyecto['id_proyecto'] . '">' . $proyecto['nombre_proyecto'] . '</option>';
				}
			}

			// Devolver las opciones como una cadena
			return $options;
		} /*-- Fin controlador --*/

		/*----------  Controlador agregar proyecto evaluacion ----------*/
		public function agregar_proyecto_evaluacion_controlador($id_agregar_proyecto){
			// Limpiamos el ID del proyecto
			$id_proyecto = mainModel::limpiar_cadena($id_agregar_proyecto);
			
			// Verificamos si el proyecto existe en la base de datos
			$check_proyecto = mainModel::ejecutar_consulta_simple("SELECT * FROM proyecto WHERE id_proyecto = :id_proyecto", array(':id_proyecto' => $id_proyecto));

			// Si no se encuentra el proyecto, devolvemos un mensaje de error
			if($check_proyecto->rowCount() <= 0) {
				$alerta = array(
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No hemos podido seleccionar el proyecto debido a un error, por favor inténtelo nuevamente.",
					"Tipo" => "error"
				);
				return json_encode($alerta);
			} else {
				// El proyecto existe en la base de datos, obtenemos sus datos
				$campos = $check_proyecto->fetch();

				// Iniciamos la sesión si aún no se ha iniciado
				if (session_status() == PHP_SESSION_NONE) {
					session_start(['name' => 'xcoring']);
				}

				// Verificamos si $_SESSION['datos_proyecto'] está vacío o no está definido
				if(empty($_SESSION['datos_proyecto'])) {
					// Creamos la variable $_SESSION['datos_proyecto'] como un array
					$_SESSION['datos_proyecto'] = array();
				}

				// Agregamos el proyecto al array $_SESSION['datos_proyecto']
				$_SESSION['datos_proyecto'][] = array(
					"IDProyecto" => $campos['id_proyecto'],
					"NombreProyecto" => $campos['nombre_proyecto']
				);

				// Devolvemos un mensaje de éxito
				$alerta = array(
					"Alerta" => "recargar",
					"Titulo" => "¡Proyecto Seleccionado!",
					"Texto" => "El proyecto se seleccionó para realizar una evaluación.",
					"Tipo" => "success"
				);
				return json_encode($alerta);
			}
		} /*-- Fin controlador --*/

		/*----------  Controlador eliminar proyecto evaluacion ----------*/
		public function eliminar_proyecto_evaluacion_controlador() {

			/*== Iniciando la sesión ==*/
			session_start(['name' => 'xcoring']);

			// Eliminar la variable de sesión 'datos_proyecto' si existe
			if (isset($_SESSION['datos_proyecto'])) {
				unset($_SESSION['datos_proyecto']);
			}
			
			// Eliminar la variable de sesión 'datos_criterio' si existe
			if (isset($_SESSION['datos_criterio'])) {
				unset($_SESSION['datos_criterio']);
			}
			
			// Eliminar la variable de sesión 'datos_proveedor' si existe
			if (isset($_SESSION['datos_proveedor'])) {
				unset($_SESSION['datos_proveedor']);
			}
			
			// Eliminar la variable de sesión 'datos_usuario' si existe
			if (isset($_SESSION['datos_usuario'])) {
				unset($_SESSION['datos_usuario']);
			}
			
			// Eliminar la variable de sesión 'datos_categoria' si existe
			if (isset($_SESSION['datos_categoria'])) {
				unset($_SESSION['datos_categoria']);
			}

			// Verificar si se eliminaron correctamente los datos del proyecto
			if (empty($_SESSION['datos_proyecto'])) {
				$alerta = [
					"Alerta" => "redireccionar",
					"URL" => SERVERURL . "home/"
				];
			} else {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No hemos podido deseleccionar el proyecto, por favor intente nuevamente.",
					"Tipo" => "error"
				];
			}

			// Devolver la respuesta como JSON
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*----------  Controlador eliminar proveedor evaluacion ----------*/
		public function eliminar_proveedor_evaluacion_controlador() {
			/*== Recuperando id del proveedor ==*/
			$id_proveedor = mainModel::limpiar_cadena($_POST['id_eliminar_proveedor']);

			/*== Iniciando la sesión ==*/
			session_start(['name' => 'xcoring']);

			// Verificar si existen datos de proveedores en la sesión
			if (isset($_SESSION['datos_proveedor'])) {
				// Recorrer la lista de proveedores en la sesión
				foreach ($_SESSION['datos_proveedor'] as $key => $proveedor) {
					if ($proveedor['ID'] == $id_proveedor) {
						// Eliminar el proveedor si se encuentra en la sesión
						unset($_SESSION['datos_proveedor'][$key]);

						// Crear una alerta de éxito
						$alerta = [
							"Alerta" => "recargar",
							"Titulo" => "¡Proveedor Deseleccionado!",
							"Texto" => "El proveedor se ha deseleccionado.",
							"Tipo" => "success"
						];
						echo json_encode($alerta);
						exit();
					}
				}
			}

			// Si el proveedor no se encuentra en la sesión, crear una alerta de error
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Error",
				"Texto" => "El proveedor que intenta deseleccionar no se encuentra en la sesión.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*----------  Controlador eliminar criterio evaluación ----------*/
		public function eliminar_criterio_evaluacion_controlador() {
			/*== Recuperando id del item ==*/
			$id_criterio = mainModel::limpiar_cadena($_POST['id_eliminar_criterio']);

			/*== Iniciando la sesión ==*/
			session_start(['name' => 'xcoring']);

			// Verificar si existen datos de criterios en la sesión
			if (isset($_SESSION['datos_criterio'])) {
				// Recorrer la lista de criterios en la sesión
				foreach ($_SESSION['datos_criterio'] as $key => $criterio) {
					if ($criterio['ID'] == $id_criterio) {
						// Eliminar el criterio si se encuentra en la sesión
						unset($_SESSION['datos_criterio'][$key]);

						// Crear una alerta de éxito
						$alerta = [
							"Alerta" => "recargar",
							"Titulo" => "¡Criterio Deseleccionado!",
							"Texto" => "El criterio deseleccionado se ha eliminado.",
							"Tipo" => "success"
						];
						echo json_encode($alerta);
						exit();
					}
				}
			}

			// Si el criterio no se encuentra en la sesión, crear una alerta de error
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Error",
				"Texto" => "El criterio que intenta deseleccionar no se encuentra.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*----------  Controlador eliminar categoría evaluación ----------*/
		public function eliminar_categoria_evaluacion_controlador() {
			/*== Recuperando id del item ==*/
			$id_categoria = mainModel::limpiar_cadena($_POST['id_eliminar_categoria']);

			/*== Iniciando la sesión ==*/
			session_start(['name' => 'xcoring']);

			// Verificar si existen datos de categorías en la sesión
			if (isset($_SESSION['datos_categoria'])) {
				// Recorrer la lista de categorías en la sesión
				foreach ($_SESSION['datos_categoria'] as $key => $categoria) {
					if ($categoria['ID'] == $id_categoria) {
						// Eliminar la categoría si se encuentra en la sesión
						unset($_SESSION['datos_categoria'][$key]);

						// Crear una alerta de éxito
						$alerta = [
							"Alerta" => "recargar",
							"Titulo" => "¡Categoría Deseleccionada!",
							"Texto" => "La categoría se ha deseleccionado.",
							"Tipo" => "success"
						];
						echo json_encode($alerta);
						exit();
					}
				}
			}

			// Si la categoría no se encuentra en la sesión, crear una alerta de error
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Error",
				"Texto" => "La categoría que intenta deseleccionar no se encuentra.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*---------- Controlador eliminar evaluador evaluación ----------*/
		public function eliminar_evaluador_evaluacion_controlador() {
			/*== Recuperando id del item ==*/
			$id_usuario = mainModel::limpiar_cadena($_POST['id_eliminar_evaluador']);

			/*== Iniciando la sesión ==*/
			session_start(['name' => 'xcoring']);

			// Verificar si existen datos de evaluadores en la sesión
			if (isset($_SESSION['datos_usuario'])) {
				// Recorrer la lista de evaluadores en la sesión
				foreach ($_SESSION['datos_usuario'] as $key => $usuario) {
					if ($usuario['ID'] == $id_usuario) {
						// Eliminar el evaluador si se encuentra en la sesión
						unset($_SESSION['datos_usuario'][$key]);

						// Crear una alerta de éxito
						$alerta = [
							"Alerta" => "recargar",
							"Titulo" => "¡Evaluador Deseleccionado!",
							"Texto" => "El evaluador se ha deseleccionado.",
							"Tipo" => "success"
						];
						echo json_encode($alerta);
						exit();
					}
				}
			}

			// Si el evaluador no se encuentra en la sesión, crear una alerta de error
			$alerta = [
				"Alerta" => "simple",
				"Titulo" => "Error",
				"Texto" => "El evaluador que intenta deseleccionar no se encuentra.",
				"Tipo" => "error"
			];
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*---------- Controlador agregar evaluación ----------*/
		public function agregar_evaluacion_controlador() {
			// Iniciando la sesión
			session_start(['name' => 'xcoring']);

			// Comprobando criterios
			if (empty($_SESSION['datos_cliente']) || empty($_SESSION['datos_proyecto']) || empty($_SESSION['datos_proveedor']) || empty($_SESSION['datos_criterio']) || empty($_SESSION['datos_usuario'])) {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrió un error",
					"Texto" => "No has seleccionado todos los datos necesarios para la evaluación.",
					"Tipo" => "error"
				];
				echo json_encode($alerta);
				exit();
			}

			// Recibiendo datos del formulario
			$observacion = "nada"; // Asigna un valor predeterminado a la observación

			// Obtener el ID del proyecto y del cliente desde la sesión
			$proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];
			$cliente = $_SESSION['datos_cliente'][0]['IDCliente'];

			// Validar la observación
			if ($observacion != "") {
				if (mainModel::verificar_datos("[a-zA-Z ]{1,400}", $observacion)) {
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Ocurrió un error",
						"Texto" => "La observación no coincide con el formato solicitado.",
						"Tipo" => "error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			// Preparando datos de la evaluación
			$datos_evaluacion_reg = [
				"Proyecto" => $proyecto,
				"Cliente" => $cliente,
				"Observacion" => $observacion
			];

			// Recorriendo los datos de los proveedores
			foreach ($_SESSION['datos_proveedor'] as $proveedor) {
				// Asignar el ID del proveedor actual
				$proveedor_id = $proveedor['ID'];

				// Recorriendo los datos de los usuarios
				foreach ($_SESSION['datos_usuario'] as $evaluador) {
					// Asignar el ID del evaluador actual
					$evaluador_id = $evaluador['ID'];

					// Asignar el proveedor actual a los datos de evaluación
					$datos_evaluacion_reg["Proveedor"] = $proveedor_id;
					$datos_evaluacion_reg["Usuario"] = $evaluador_id;

					// Agregar cada criterio a la evaluación
					foreach ($_SESSION['datos_criterio'] as $criterio) {
						$datos_evaluacion_reg["Categoria"] = $criterio['IDC'];
						$datos_evaluacion_reg["Criterio"] = $criterio['ID'];

						// Agregar evaluación a la base de datos
						$agregar_evaluacion = evaluacionModelo::agregar_evaluacion_modelo($datos_evaluacion_reg);

						if ($agregar_evaluacion->rowCount() != 1) {
							$alerta=[
								"Alerta"=>"simple",
								"Titulo"=>"Ocurrió un error.",
								"Texto"=>"No se ha podido subir la evaluacion intentelo de nuevo",
								"Tipo"=>"error"
							];
							echo json_encode($alerta);
							exit();
						}
					}
				}
			}

			// Notificar éxito
			$alerta = [
				"Alerta" => "redireccionar",
				"URL" => SERVERURL . "evaluacion-list/"
			];
			echo json_encode($alerta);
		} /*-- Fin controlador --*/

		/*----------  Controlador tabla categoria  ----------*/
		public function tabla_categoria_controlador($pagina, $registros, $privilegio, $url, $busqueda) {

			// Recuperar el ID del proyecto desde la sesión
			$id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];

			// Limpiar los parámetros recibidos
			$pagina = mainModel::limpiar_cadena($pagina);
			$registros = mainModel::limpiar_cadena($registros);
			$privilegio = mainModel::limpiar_cadena($privilegio);
			$url = mainModel::limpiar_cadena($url);
			$url = SERVERURL . $url . "/";
			$busqueda = mainModel::limpiar_cadena($busqueda);
			$tabla = "";

			// Configurar la paginación
			$pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
			$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

			// Definir los campos a seleccionar
			$campos = "categoria.id_categoria, categoria.nombre_categoria, categoria.peso_categoria";

			// Crear la consulta SQL según si hay búsqueda o no
			if (isset($busqueda) && $busqueda != "") {
				$consulta = "SELECT SQL_CALC_FOUND_ROWS $campos 
							FROM categoria 
							WHERE categoria.id_categoria IN (
								SELECT id_categoria
								FROM evaluacion
								WHERE id_proyecto = $id_proyecto
							)
							GROUP BY categoria.id_categoria
							ORDER BY categoria.id_categoria DESC";
			} else {
				$consulta = "SELECT SQL_CALC_FOUND_ROWS $campos 
							FROM categoria 
							WHERE categoria.id_categoria IN (
								SELECT id_categoria
								FROM evaluacion
								WHERE id_proyecto = $id_proyecto
							)
							GROUP BY categoria.id_categoria
							ORDER BY categoria.id_categoria DESC";
			}

			// Conectar a la base de datos y ejecutar la consulta
			$conexion = mainModel::conectar();
			$datos = $conexion->query($consulta);
			$datos = $datos->fetchAll();

			// Obtener el número total de registros
			$total = $conexion->query("SELECT FOUND_ROWS()");
			$total = (int)$total->fetchColumn();
			$Npaginas = ceil($total / $registros);

			// Crear el cuerpo de la tabla
			$tabla .= '
			<div class="table-responsive">
			<table class="table table-dark table-sm">
				<thead>
					<tr class="text-center roboto-medium">
						<th>CATEGORÍA</th>
						<th>PESO(%)</th>';
						if ($privilegio == 1) {
							$tabla .= '<th>EDITAR</th>';
						}
						if ($privilegio == 1) {
							$tabla .= '<th>ELIMINAR</th>';
						}
					$tabla .= '</tr>
				</thead>
				<tbody>
			';

			$totalPesoCategorias = 0; // Inicializar la variable del peso total de las categorías

			// Iterar sobre los resultados de la consulta y crear las filas de la tabla
			foreach ($datos as $row) {
				$categoria_id = $row['id_categoria'];
				$categoria_nombre = $row['nombre_categoria'];
				$categoria_peso = $row['peso_categoria'];
				$totalPesoCategorias += $categoria_peso; // Sumar el peso de cada categoría al total

				$tabla .= '
					<tr class="text-center" >
						<td>' . $categoria_nombre . '</td>
						<td>' . $categoria_peso . '</td>';
						if ($privilegio == 1 || $privilegio == 2) {
							$tabla .= '
								<td>
									<a href="' . SERVERURL . 'categoria-update/' . mainModel::encryption($categoria_id) . '/" class="btn btn-dark">
										<i class="fas fa-sync-alt"></i>    
									</a>
								</td>
							';
						}
						if ($privilegio == 1) {
							$tabla .= '
								<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/evaluacionAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
										<input type="hidden" name="categoria_id_del" value="' . mainModel::encryption($categoria_id) . '">
										<button type="submit" class="btn btn-dark">
												<i class="far fa-trash-alt"></i>
										</button>
									</form>
								</td>
							';
						}
					$tabla .= '</tr>
				';
			}

			// Agregar la fila de total de peso y validación
			$tabla .= '
			<tr class="text-center" style="font-weight: bold; color: ' . ($totalPesoCategorias == 100 ? 'black' : ($totalPesoCategorias > 100 ? 'red' : 'orange')) . '">
				<td colspan="2">Total Peso: ' . $totalPesoCategorias . '</td>
				<td>
			';
			if ($totalPesoCategorias < 100) {
				$tabla .= 'Falta ' . (100 - $totalPesoCategorias) . ' para 100';
			} elseif ($totalPesoCategorias > 100) {
				$tabla .= 'Sobrepasa por ' . ($totalPesoCategorias - 100);
			}
			$tabla .= '
				</td>
			</tr>
			';

			$tabla .= '</tbody></table></div>';

			return $tabla;
		} /*-- Fin controlador --*/
		
		/*---------- Controlador tabla criterios ----------*/
		public function tabla_criterios_categoria_controlador($pagina, $registros, $privilegio, $url, $busqueda) {
			// Recuperar el ID del proyecto desde la sesión
			$id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];

			// Limpiar los parámetros recibidos
			$pagina = mainModel::limpiar_cadena($pagina);
			$registros = mainModel::limpiar_cadena($registros);
			$privilegio = mainModel::limpiar_cadena($privilegio);
			$url = mainModel::limpiar_cadena($url);
			$url = SERVERURL . $url . "/";
			$busqueda = mainModel::limpiar_cadena($busqueda);
			$tabla = "";

			// Configurar la paginación
			$pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
			$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

			// Conectar a la base de datos
			$conexion = mainModel::conectar();

			// Obtener las categorías asociadas al proyecto desde la tabla de evaluación
			$consulta_categorias = "SELECT DISTINCT categoria.id_categoria, categoria.nombre_categoria
									FROM categoria
									INNER JOIN evaluacion ON categoria.id_categoria = evaluacion.id_categoria
									WHERE evaluacion.id_proyecto = $id_proyecto";
			$datos_categorias = $conexion->query($consulta_categorias);

			// Recorrer las categorías y obtener los criterios asociados a cada una
			while ($row_categoria = $datos_categorias->fetch(PDO::FETCH_ASSOC)) {
				$categoria_id = $row_categoria['id_categoria'];
				$categoria_nombre = $row_categoria['nombre_categoria'];

				// Consulta para obtener los criterios asociados a la categoría actual
				$consulta_criterios = "SELECT DISTINCT criterio.id_criterio, criterio.nombre_criterio, criterio.peso_criterio
									FROM criterio
									INNER JOIN evaluacion ON criterio.id_criterio = evaluacion.id_criterio
									WHERE evaluacion.id_proyecto = $id_proyecto
									AND criterio.id_categoria = $categoria_id";
				$datos_criterios = $conexion->query($consulta_criterios);

				$totalPesoCriterios = 0; // Almacenar el peso total de los criterios de esta categoría

				// Construir la tabla para esta categoría
				$tabla .= '
					<div class="col-md-6">
						<div class="table-responsive">
							<table class="table table-dark table-sm">
								<thead>
									<tr class="text-center roboto-medium">
										<th colspan="6">' . $categoria_nombre . '</th>
									</tr>
									<tr class="text-center roboto-medium">
										<th>CRITERIO</th>
										<th>PESO(%)</th>';
				if ($privilegio == 1) {
					$tabla .= '<th>EDITAR</th>';
					$tabla .= '<th>ELIMINAR</th>';
				}
				$tabla .= '</tr>
								</thead>
								<tbody>
				';

				// Iterar sobre los criterios de esta categoría
				while ($row_criterio = $datos_criterios->fetch(PDO::FETCH_ASSOC)) {
					$criterio_id = $row_criterio['id_criterio'];
					$criterio_nombre = $row_criterio['nombre_criterio'];
					$criterio_peso = $row_criterio['peso_criterio'];
					$totalPesoCriterios += $criterio_peso; // Sumar el peso de cada criterio al total

					// Agregar fila para el criterio actual
					$tabla .= '
						<tr class="text-center">
							<td>' . $criterio_nombre . '</td>
							<td>' . $criterio_peso . '</td>';
					if ($privilegio == 1 || $privilegio == 2) {
						$tabla .= '
							<td>
								<a href="' . SERVERURL . 'criterio-update/' . mainModel::encryption($criterio_id) . '/" class="btn btn-dark">
									<i class="fas fa-sync-alt"></i>
								</a>
							</td>
						';
					}
					if ($privilegio == 1) {
						$tabla .= '
							<td>
								<form class="FormularioAjax" action="' . SERVERURL . 'ajax/evaluacionAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
									<input type="hidden" name="criterio_id_del" value="' . mainModel::encryption($criterio_id) . '">
									<button type="submit" class="btn btn-dark">
										<i class="far fa-trash-alt"></i>
									</button>
								</form>
							</td>
						';
					}
					$tabla .= '</tr>';
				}

				// Agregar la fila de total de peso y validación para esta categoría
				$tabla .= '
					<tr class="text-center" style="font-weight: bold; color: ' . ($totalPesoCriterios == 100 ? 'black' : ($totalPesoCriterios > 100 ? 'red' : 'orange')) . '">
						<td colspan="2">Total Peso: ' . $totalPesoCriterios . '</td>
						<td>';
				if ($totalPesoCriterios < 100) {
					$tabla .= 'Falta ' . (100 - $totalPesoCriterios) . ' para 100';
				} elseif ($totalPesoCriterios > 100) {
					$tabla .= 'Sobrepasa por ' . ($totalPesoCriterios - 100);
				}
				$tabla .= '</td>
					</tr>
				';

				$tabla .= '</tbody></table></div></div>';
			}

			return $tabla;
		} /*-- Fin controlador --*/		
		
		/*----------  Controlador tabla proveedor  ----------*/
		public function tabla_proveedor_controlador($pagina, $registros, $privilegio, $url, $busqueda) {
			// Recuperar el ID del proyecto desde la sesión
			$id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];

			// Limpiar los parámetros recibidos
			$pagina = mainModel::limpiar_cadena($pagina);
			$registros = mainModel::limpiar_cadena($registros);
			$privilegio = mainModel::limpiar_cadena($privilegio);
			$url = mainModel::limpiar_cadena($url);
			$url = SERVERURL . $url . "/";
			$busqueda = mainModel::limpiar_cadena($busqueda);
			$tabla = "";

			// Configurar la paginación
			$pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
			$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

			// Conectar a la base de datos
			$conexion = mainModel::conectar();

			// Consulta para obtener los proveedores asociados al proyecto desde la tabla de evaluación
			$consulta_proveedores = "SELECT DISTINCT proveedor.id_proveedor, proveedor.nombre_proveedor
									FROM proveedor
									INNER JOIN evaluacion ON proveedor.id_proveedor = evaluacion.id_proveedor
									WHERE evaluacion.id_proyecto = $id_proyecto";
			$datos_proveedores = $conexion->query($consulta_proveedores);

			$totalProveedores = $datos_proveedores->rowCount();

			if ($totalProveedores > 0) {
				### Cuerpo de la tabla ###
				$tabla .= '
				<div class="table-responsive">
				<table class="table table-dark table-sm">
					<thead>
						<tr class="text-center roboto-medium">
							<th>PROVEEDOR</th>';
							if($privilegio == 1){
								$tabla .= '<th>EDITAR</th>';
							}
							if($privilegio == 1){
								$tabla .= '<th>ELIMINAR</th>';
							}
						$tabla .= '</tr>
					</thead>
					<tbody>
				';

				// Iterar sobre los proveedores obtenidos
				while ($row = $datos_proveedores->fetch(PDO::FETCH_ASSOC)) {
					$proveedor_id = $row['id_proveedor'];
					$proveedor_nombre = $row['nombre_proveedor'];

					// Agregar fila para el proveedor actual
					$tabla .= '
					<tr class="text-center">
						<td>'.$proveedor_nombre.'</td>';
						if($privilegio == 1 || $privilegio == 2){
							$tabla .= '
							<td>
								<a href="'.SERVERURL.'proveedor-update/'.mainModel::encryption($proveedor_id).'/" class="btn btn-dark">
									<i class="fas fa-sync-alt"></i>    
								</a>
							</td>
							';
						}
						if($privilegio == 1){
							$tabla .= '
							<td>
								<form class="FormularioAjax" action="'.SERVERURL.'ajax/evaluacionAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
									<input type="hidden" name="proveedor_id_del" value="'.mainModel::encryption($proveedor_id).'">
									<button type="submit" class="btn btn-dark">
										<i class="far fa-trash-alt"></i>
									</button> 
								</form>
							</td>
							';
						}
					$tabla .= '</tr>';
				}

				$tabla .= '</tbody></table></div>';
			} else {
				// No se encontraron proveedores asociados al proyecto
				$tabla .= '<p>No se encontraron proveedores asociados al proyecto.</p>';
			}

			return $tabla;
		} /*-- Fin controlador --*/

		/*---------- Controlador tabla evaluadores ----------*/
		public function tablaEvaluadoresControlador($pagina, $registros, $privilegio, $url, $busqueda) {
			// Recuperar el ID del proyecto desde la sesión
			$id_proyecto = $_SESSION['datos_proyecto'][0]['IDProyecto'];

			// Limpiar los parámetros recibidos
			$pagina = mainModel::limpiar_cadena($pagina);
			$registros = mainModel::limpiar_cadena($registros);
			$privilegio = mainModel::limpiar_cadena($privilegio);
			$url = mainModel::limpiar_cadena($url);
			$url = SERVERURL . $url . "/";
			$busqueda = mainModel::limpiar_cadena($busqueda);
			$tabla = "";

			// Configurar la paginación
			$pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
			$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

			// Consulta para obtener los proveedores presentes en la tabla de evaluación
			$consulta_proveedores_evaluacion = "SELECT DISTINCT proveedor.id_proveedor, proveedor.nombre_proveedor 
												FROM proveedor 
												INNER JOIN evaluacion ON proveedor.id_proveedor = evaluacion.id_proveedor 
												WHERE evaluacion.id_proyecto = $id_proyecto";

			// Conectar a la base de datos y ejecutar la consulta
			$conexion = mainModel::conectar();
			$datos_proveedores = $conexion->query($consulta_proveedores_evaluacion);
			$datos_proveedores = $datos_proveedores->fetchAll();

			// Construir el encabezado de la tabla
			$tabla .= '<div class="table-responsive">
						<table class="table table-dark table-sm">
							<thead>
								<tr class="text-center roboto-medium">
									<th>Evaluador</th>';

			// Iterar sobre los proveedores para crear las columnas dinámicas
			foreach ($datos_proveedores as $proveedor) {
				$proveedor_id = $proveedor['id_proveedor'];
				$proveedor_nombre = $proveedor['nombre_proveedor'];
				$tabla .= '<th>' . $proveedor_nombre . '</th>';
			}
			$tabla .= '<th>Total</th><th>Editar</th><th>Recordatorio</th><th>Eliminar</th></tr>
					</thead>
					<tbody>';

			// Consulta para recuperar los evaluadores de la tabla evaluacion
			$campos_evaluadores = "evaluacion.id_usuario, usuario.nombre_usuario, usuario.apellido_usuario, usuario.email_usuario";

			$consulta_evaluadores = "SELECT SQL_CALC_FOUND_ROWS $campos_evaluadores 
									FROM evaluacion 
									INNER JOIN usuario ON evaluacion.id_usuario = usuario.id_usuario
									WHERE evaluacion.id_proyecto = $id_proyecto
									GROUP BY evaluacion.id_usuario
									ORDER BY evaluacion.id_usuario DESC";

			// Ejecutar la consulta y obtener los datos de los evaluadores
			$datos_evaluadores = $conexion->query($consulta_evaluadores);
			$datos_evaluadores = $datos_evaluadores->fetchAll();

			// Iterar sobre los evaluadores
			foreach ($datos_evaluadores as $evaluador) {
				$evaluador_id = $evaluador['id_usuario'];
				$evaluador_nombre = $evaluador['nombre_usuario'] . ' ' . $evaluador['apellido_usuario'];
				$tabla .= '<tr class="text-center"><td>' . $evaluador_nombre . '</td>';

				// Iterar sobre los proveedores para obtener el porcentaje calificado por cada uno
				$total_calificaciones = 0;
				$total_criterios = 0;
				foreach ($datos_proveedores as $proveedor) {
					$proveedor_id = $proveedor['id_proveedor'];
					// Consulta para obtener la calificación de este evaluador para este proveedor
					$consulta_calificacion = "SELECT COUNT(*) AS total
											FROM evaluacion
											WHERE id_proveedor = $proveedor_id
											AND id_usuario = $evaluador_id
											AND calificacion_evaluacion != 0";
					$datos_calificacion = $conexion->query($consulta_calificacion);
					$resultado_calificacion = $datos_calificacion->fetch();
					$total_calificaciones += $resultado_calificacion['total'];

					// Consulta para obtener el número total de criterios asociados a este proveedor
					$consulta_criterios_proveedor = "SELECT COUNT(*) AS total_criterios
													FROM evaluacion 
													WHERE id_proyecto = $id_proyecto
													AND id_proveedor = $proveedor_id";
					$datos_criterios_proveedor = $conexion->query($consulta_criterios_proveedor);
					$resultado_criterios_proveedor = $datos_criterios_proveedor->fetch();
					$total_criterios += $resultado_criterios_proveedor['total_criterios'];

					// Calcular el porcentaje calificado por este proveedor
					$porcentaje_calificado = ($resultado_calificacion['total'] / $resultado_criterios_proveedor['total_criterios']) * 100;
					$tabla .= '<td>' . round($porcentaje_calificado, 2) . '%</td>';
				}

				// Calcular el porcentaje total para este evaluador
				$porcentaje_total = ($total_calificaciones / $total_criterios) * 100;
				$tabla .= '<td>' . round($porcentaje_total, 2) . '%</td>';

				// Agregar botón de editar
				if ($privilegio == 1 || $privilegio == 2) {
					$tabla .= '
						<td>
							<a href="' . SERVERURL . 'user-update/' . mainModel::encryption($evaluador_id) . '/" class="btn btn-dark">
								<i class="fas fa-sync-alt"></i>    
							</a>
						</td>
					';
				}
				$tabla .= '<td>
							<form class="FormularioAjax" action="' . SERVERURL . 'ajax/evaluacionAjax.php" method="POST" data-form="email" enctype="multipart/form-data" autocomplete="off">
								<input type="hidden" name="evaluador_id" value="' . mainModel::encryption($evaluador_id) . '">
								<button type="submit" class="btn btn-dark">
									<i class="fas fa-envelope"></i> Enviar Correo
								</button>
							</form>
						</td>';
				// Agregar botón de eliminar (solo si el privilegio es 1)
				if ($privilegio == 1) {
					$tabla .= '
						<td>
							<form class="FormularioAjax" action="' . SERVERURL . 'ajax/evaluacionAjax.php" method="POST" data-form="delete" enctype="multipart/form-data" autocomplete="off" >
								<input type="hidden" name="user_id_del" value="' . mainModel::encryption($evaluador_id) . '">
								<button type="submit" class="btn btn-dark">
										<i class="far fa-trash-alt"></i>
								</button>
							</form>
						</td>
					';
				}

				$tabla .= '</tr>';
			}

			$tabla .= '</tbody></table></div>';

			return $tabla;
		}/*-- Fin controlador --*/

		/*---------- Controlador para enviar correo electrónico al evaluador ----------*/
		public function enviarCorreo() {
			// Verificar si se ha enviado el ID del evaluador
			if (isset($_POST['evaluador_id'])) {
				// Desencriptar y limpiar el ID del evaluador
				$evaluador_id = mainModel::decryption($_POST['evaluador_id']);
				$evaluador_id = mainModel::limpiar_cadena($evaluador_id);

				// Obtener el correo electrónico del evaluador desde la tabla de evaluación
				$conexion = mainModel::conectar();
				$consulta = "SELECT usuario.email_usuario
							FROM evaluacion 
							INNER JOIN usuario ON evaluacion.id_usuario = usuario.id_usuario
							WHERE evaluacion.id_usuario = :id_usuario
							LIMIT 1";
				$datos = $conexion->prepare($consulta);
				$datos->bindValue(":id_usuario", $evaluador_id, PDO::PARAM_INT);
				$datos->execute();
				$resultado = $datos->fetch(PDO::FETCH_ASSOC);
				$correo_evaluador = $resultado['email_usuario'];

				// Verificar si se obtuvo el correo electrónico del evaluador
				if (!empty($correo_evaluador)) {
					// Configurar el correo electrónico
					$to = $correo_evaluador;
					$subject = 'Recordatorio: Falta por calificar';
					$message = 'Hola, este es un recordatorio amigable para informarte que aún falta por calificar en el sistema. Por favor, accede al sistema y completa tus evaluaciones. Gracias.';
					$headers = 'From: tu@email.com' . "\r\n" .
							'Reply-To: tu@email.com' . "\r\n" .
							'X-Mailer: PHP/' . phpversion();

					// Enviar el correo electrónico
					if (mail($to, $subject, $message, $headers)) {
						$alerta = [
							"Alerta" => "simple",
							"Titulo" => "Correo enviado",
							"Texto" => "El correo electrónico ha sido enviado exitosamente al evaluador.",
							"Tipo" => "success"
						];
					} else {
						$alerta = [
							"Alerta" => "simple",
							"Titulo" => "Error al enviar correo",
							"Texto" => "Ocurrió un error al intentar enviar el correo electrónico al evaluador.",
							"Tipo" => "error"
						];
					}
				} else {
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Correo no encontrado",
						"Texto" => "No se encontró el correo electrónico del evaluador.",
						"Tipo" => "error"
					];
				}
			} else {
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Error",
					"Texto" => "No se proporcionó el ID del evaluador para enviar el correo electrónico.",
					"Tipo" => "error"
				];
			}

			// Devolver la alerta como JSON
			echo json_encode($alerta);
		}/*-- Fin controlador --*/

		/*---------- Controlador eliminar categoría y criterios asociados ----------*/
		public function eliminar_categoria2_evaluacion_controlador() {
			if (isset($_POST['categoria_id_del'])) {
				// Desencriptar y limpiar el ID de la categoría
				$categoria_id = mainModel::decryption($_POST['categoria_id_del']);
				$categoria_id = mainModel::limpiar_cadena($categoria_id);

				// Llamar al método del modelo para eliminar las entradas de evaluación asociadas a los criterios de la categoría y la categoría misma
				$resultado_eliminacion = EvaluacionModelo::eliminar_categoria_y_criterios_modelo($categoria_id);

				if ($resultado_eliminacion !== false) {
					// Si la eliminación fue exitosa, devolver un mensaje de éxito
					$alerta = [
						"Alerta" => "recargar",
						"Titulo" => "Categoría eliminada",
						"Texto" => "La categoría y las evaluaciones asociadas han sido eliminadas exitosamente.",
						"Tipo" => "success"
					];
				} else {
					// Si ocurrió un error durante la eliminación, devolver un mensaje de error
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Error al eliminar categoría",
						"Texto" => "Ocurrió un error al intentar eliminar la categoría y las evaluaciones asociadas.",
						"Tipo" => "error"
					];
				}
			} else {
				// Si no se proporcionó el ID de la categoría, devolver un mensaje de error
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Error",
					"Texto" => "No se proporcionó el ID de la categoría para eliminar las evaluaciones asociadas.",
					"Tipo" => "error"
				];
			}

			// Devolver la alerta como JSON
			echo json_encode($alerta);
		}
		/*-- Fin controlador --*/

		/*---------- Controlador eliminar criterios asociados ----------*/
		public function eliminarCriterioEvaluacion() {
			// Verificar si se ha enviado el ID del criterio
			if (isset($_POST['criterio_id_del'])) {
				// Desencriptar y limpiar el ID del criterio
				$criterio_id = mainModel::decryption($_POST['criterio_id_del']);
				$criterio_id = mainModel::limpiar_cadena($criterio_id);

				// Llamar al método del modelo para eliminar los criterios de la tabla evaluacion
				$resultado_eliminacion = EvaluacionModelo::eliminarCriterioEnEvaluacionModelo($criterio_id);

				// Verificar si la eliminación fue exitosa
				if ($resultado_eliminacion !== false) {
					// Si la eliminación fue exitosa, devolver un mensaje de éxito
					$alerta = [
						"Alerta" => "recargar",
						"Titulo" => "Criterios eliminados",
						"Texto" => "Los criterios y sus evaluaciones asociadas han sido eliminados exitosamente.",
						"Tipo" => "success"
					];
				} else {
					// Si ocurrió un error durante la eliminación, devolver un mensaje de error
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Error al eliminar criterios",
						"Texto" => "Ocurrió un error al intentar eliminar los criterios y sus evaluaciones asociadas.",
						"Tipo" => "error"
					];
				}
			} else {
				// Si no se proporcionó el ID del criterio, devolver un mensaje de error
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Error",
					"Texto" => "No se proporcionó el ID del criterio para eliminarlos y sus evaluaciones asociadas.",
					"Tipo" => "error"
				];
			}

			// Devolver la alerta como JSON
			echo json_encode($alerta);
		}
		/*-- Fin controlador --*/

		/*---------- Controlador eliminar proveedor asociados ----------*/
		public function eliminarProveedorEvaluacion() {
			// Verificar si se ha enviado el ID del proveedor
			if (isset($_POST['proveedor_id_del'])) {
				// Desencriptar y limpiar el ID del proveedor
				$proveedor_id = mainModel::decryption($_POST['proveedor_id_del']);
				$proveedor_id = mainModel::limpiar_cadena($proveedor_id);

				// Llamar al método del modelo para eliminar el proveedor y sus asociaciones en la tabla de evaluación
				$resultado_eliminacion = EvaluacionModelo::eliminarProveedorEnEvaluacionModelo($proveedor_id);

				// Verificar si la eliminación fue exitosa
				if ($resultado_eliminacion !== false) {
					// Si la eliminación fue exitosa, devolver un mensaje de éxito
					$alerta = [
						"Alerta" => "recargar",
						"Titulo" => "Proveedor eliminado",
						"Texto" => "El proveedor y sus asociaciones en la tabla de evaluación han sido eliminados exitosamente.",
						"Tipo" => "success"
					];
				} else {
					// Si ocurrió un error durante la eliminación, devolver un mensaje de error
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Error al eliminar proveedor",
						"Texto" => "Ocurrió un error al intentar eliminar el proveedor y sus asociaciones en la tabla de evaluación.",
						"Tipo" => "error"
					];
				}
			} else {
				// Si no se proporcionó el ID del proveedor, devolver un mensaje de error
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Error",
					"Texto" => "No se proporcionó el ID del proveedor para eliminarlo y sus asociaciones en la tabla de evaluación.",
					"Tipo" => "error"
				];
			}

			// Devolver la alerta como JSON
			echo json_encode($alerta);
		}/*-- Fin controlador --*/

		/*---------- Controlador eliminar evaluador ----------*/
		public function eliminarEvaluadorEvaluacion() {
			// Verificar si se ha enviado el ID del usuario a eliminar
			if (isset($_POST['user_id_del'])) {
				// Desencriptar y limpiar el ID del usuario
				$usuario_id = mainModel::decryption($_POST['user_id_del']);
				$usuario_id = mainModel::limpiar_cadena($usuario_id);

				// Llamar al método del modelo para eliminar el usuario de la tabla evaluacion
				$resultado_eliminacion = evaluacionModelo::eliminarUsuarioEnEvaluacionModelo($usuario_id);

				// Verificar si la eliminación fue exitosa
				if ($resultado_eliminacion !== false) {
					// Si la eliminación fue exitosa, devolver un mensaje de éxito
					$alerta = [
						"Alerta" => "recargar",
						"Titulo" => "Usuario eliminado",
						"Texto" => "El usuario y sus asociaciones en la tabla de evaluación han sido eliminados exitosamente.",
						"Tipo" => "success"
					];
				} else {
					// Si ocurrió un error durante la eliminación, devolver un mensaje de error
					$alerta = [
						"Alerta" => "simple",
						"Titulo" => "Error al eliminar usuario",
						"Texto" => "Ocurrió un error al intentar eliminar el usuario y sus asociaciones en la tabla de evaluación.",
						"Tipo" => "error"
					];
				}
			} else {
				// Si no se proporcionó el ID del usuario, devolver un mensaje de error
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Error",
					"Texto" => "No se proporcionó el ID del usuario para eliminarlo y sus asociaciones en la tabla de evaluación.",
					"Tipo" => "error"
				];
			}

			// Devolver la alerta como JSON
			echo json_encode($alerta);
		}/*-- Fin controlador --*/
	}
