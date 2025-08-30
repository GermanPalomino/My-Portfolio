<div class="login-container">
	<!-- Contenedor principal para el contenido de inicio de sesión -->
	<div class="login-content">
		<!-- Logotipo de la empresa -->
		<p class="text-center">
			<!-- <img src="<?php echo SERVERURL; ?>vistas/assets/img/logo itx.png" class="img-fluid"> -->
		</p>
		<!-- Mensaje de bienvenida -->
		<p class="text-center">
			<b>Bienvenido por favor inicia sesión con tu cuenta</b>
		</p>
		<!-- Formulario de inicio de sesión -->
		<form method="POST" autocomplete="off">
			<!-- Campo para el correo electrónico -->
			<div class="form-group">
				<label for="UserName" class="bmd-label-floating"><i class="fas fa-user-circle"></i> &nbsp; Correo electrónico</label>
				<input type="text" class="form-control" id="UserName" name="email_log" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required="">
			</div>
			<!-- Campo para la contraseña -->
			<div class="form-group">
				<label for="UserPassword" class="bmd-label-floating"><i class="fas fa-key"></i> &nbsp; Contraseña</label>
				<input type="password" class="form-control" id="UserPassword" name="pss_log" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required="">
			</div>
			<!-- Botón para enviar el formulario -->
			<button type="submit" class="btn-login text-center">LOG IN</button>
		</form>
	</div>
</div>
<?php
	// Verificar si se han enviado los datos del formulario de inicio de sesión
	if(isset($_POST['email_log']) && isset($_POST['pss_log'])){
		// Incluir el controlador de login
		require_once "./controladores/loginControlador.php";

		// Crear una instancia del controlador de login
		$ins_login = new loginControlador();
		
		// Llamar al método para iniciar sesión y mostrar el resultado
		echo $ins_login->iniciar_sesion_controlador();
	}
?>