<script>
	/*----------  Función para agregar un cliente  ----------*/
	function agregarCliente(idCliente) {
		// Verificar si se ha proporcionado un ID de cliente
		if (idCliente !== "") {
			// Crear un objeto FormData para enviar los datos del cliente
			let datos = new FormData();
			datos.append("cliente_id", idCliente);

			// Hacer una solicitud fetch para enviar los datos al servidor
			fetch('<?php echo SERVERURL; ?>ajax/clienteAjax.php', {
				method: 'POST',
				body: datos
			})
			.then(respuesta => respuesta.json()) // Parsear la respuesta como JSON
			.then(respuesta => {
				// Recargar la página después de agregar el cliente
				location.reload();
			});
		} else {
			// Mostrar una alerta si no se ha seleccionado un cliente
			alert("Por favor, seleccione un cliente antes de agregarlo.");
		}
	}

    /*----------  Función para agregar un proyecto  ----------*/
	function agregarProyecto(idProyecto) {
		// Verificar si se ha proporcionado un ID de proyecto
		if (idProyecto !== "") {
			// Crear un objeto FormData para enviar los datos del proyecto
			let datos = new FormData();
			datos.append("proyecto_id", idProyecto);

			// Hacer una solicitud fetch para enviar los datos al servidor
			fetch('<?php echo SERVERURL; ?>ajax/proyectoAjax.php', {
				method: 'POST',
				body: datos
			})
			.then(respuesta => respuesta.json()) // Parsear la respuesta como JSON
			.then(respuesta => {
				// Recargar la página después de agregar el proyecto
				location.reload();
			});
		} else {
			// Mostrar una alerta si no se ha seleccionado un proyecto
			alert("Por favor, seleccione un proyecto antes de agregarlo.");
		}
	}
</script>