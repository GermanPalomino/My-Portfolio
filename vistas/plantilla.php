<!DOCTYPE html>
<html lang="es">
<head>
	<!-- Configuración de los metadatos -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	
	<!-- Título de la página -->
	<title><?php echo COMPANY; ?></title>

	<!-- Incluir archivos de estilos y scripts -->
	<?php include './vistas/inc/Link.php'; ?>
</head>
<body>
	<?php 
		// Variable para indicar si la petición es Ajax
		$peticionAjax = false;

		// Incluir el controlador de vistas
		require_once "./controladores/vistasControlador.php";

		// Instanciar el controlador de vistas
		$IV = new vistasControlador();
		$vista = $IV->obtener_vistas_controlador();

		// Comprobar si la vista es login o 404
		if($vista == "login" || $vista == "404"){
			// Incluir la vista correspondiente
			require_once "./vistas/contenidos/".$vista."-view.php";
		} else {
			// Iniciar la sesión
			session_start(['name' => 'xcoring']);

			// Obtener el nombre de la página desde la URL
			$pagina = explode("/", $_GET['views']);

			// Incluir el controlador de login
			require_once "./controladores/loginControlador.php";
			$lc = new loginControlador();

			// Forzar el cierre de sesión si no están definidas las variables de sesión
			if(!isset($_SESSION['token_xcoring']) || !isset($_SESSION['email_xcoring']) || !isset($_SESSION['privilegio_xcoring']) || !isset($_SESSION['nombre_xcoring'])){
				echo $lc->forzar_cierre_sesion_controlador();
				exit();
			}
	?>
	<!-- Contenedor principal -->
	<main class="full-box main-container">
		<!-- Incluir la barra de navegación lateral -->
		<?php include './vistas/inc/NavLateral.php'; ?>
		
		<!-- Contenido de la página -->
		<section class="full-box page-content">
			<?php 
				// Incluir la barra de navegación superior
				include './vistas/inc/NavBar.php';

				// Incluir la vista correspondiente
				include $vista;
			?>
		</section>
	</main>
	<?php
			// Incluir el modal de cierre de sesión
			include './vistas/inc/LogOut.php';
		} 
		// Incluir los scripts
		include './vistas/inc/Script.php'; 
	?>
</body>
</html>