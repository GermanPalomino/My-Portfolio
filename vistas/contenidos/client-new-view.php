<!-- Encabezado de la página -->
<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas fa-building fa-fw"></i> &nbsp; CREAR CLIENTE
	</h3>
</div>

<!-- Navegación de pestañas -->
<div class="container-fluid">
	<ul class="full-box list-unstyled page-nav-tabs">
		<!-- Enlace para crear un nuevo cliente -->
		<li>
			<a class="active" href="<?php echo SERVERURL; ?>client-new/">
				<i class="fas fa-building fa-fw"></i> &nbsp; CREAR CLIENTE
			</a>
		</li>
		<!-- Enlace para ver la lista de clientes -->
		<li>
			<a href="<?php echo SERVERURL; ?>client-list/">
				<i class="fas fa-building fa-fw"></i> &nbsp; LISTA DE CLIENTES
			</a>
		</li>
	</ul>	
</div>

<!-- Formulario para crear un nuevo cliente -->
<div class="container-fluid">
	<form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/clienteAjax.php" method="POST" data-form="save" enctype="multipart/form-data" autocomplete="off">
		<fieldset>
			<legend><i class="fas fa-building fa-fw"></i> &nbsp; Información del cliente</legend><br>
			<div class="container-fluid">
				<div class="row">
					<!-- Columna izquierda del formulario -->
					<div class="col-md-6">
						<fieldset>
							<legend></legend>
							<!-- Campo para el nombre del cliente -->
							<div class="form-group">
								<label for="nombre_cliente" class="bmd-label-floating">Nombre Cliente</label>
								<input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="nombre_cliente_reg" id="nombre_cliente" maxlength="40" required="">
							</div>
							<!-- Información de contacto -->
							<legend>Información de contacto</legend>
							<!-- Campo para el teléfono del contacto -->
							<div class="form-group">
								<label for="telefonoContacto_cliente" class="bmd-label-floating">Teléfono</label>
								<input type="text" pattern="[0-9\+()]{8,20}" class="form-control" name="telefonoContacto_cliente_reg" id="telefonoContacto_cliente" maxlength="20" required="">
							</div>
						</fieldset>
					</div>
					<!-- Columna derecha del formulario -->
					<div class="col-md-6">
						<fieldset>
							<legend></legend>
							<!-- Campo para el nombre del contacto -->
							<div class="form-group">
								<label for="nombreContacto_cliente" class="bmd-label-floating">Nombre Contacto</label>
								<input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="nombreContacto_cliente_reg" id="nombreContacto_cliente" maxlength="40" required="">
							</div>
							<legend><br></legend>
							<!-- Campo para el email del contacto -->
							<div class="form-group">
								<label for="emailContacto_cliente" class="bmd-label-floating">Email</label>
								<input type="email" class="form-control" name="emailContacto_cliente_reg" id="emailContacto_cliente" maxlength="70" pattern="[a-zA-Z0-9$@.-]{7,100}" required="">
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</fieldset>
		<br><br><br>
		<!-- Botones para limpiar y guardar el formulario -->
		<p class="text-center">
			<button type="reset" class="btn btn-raised btn-secondary btn-sm">
				<i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR
			</button>
			&nbsp; &nbsp;
			<button type="submit" class="btn btn-raised btn-info btn-sm">
				<i class="far fa-save"></i> &nbsp; GUARDAR
			</button>
		</p>
	</form>
</div>