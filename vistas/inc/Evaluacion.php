<!-- Incluir las librerías necesarias -->
<script src="<?php echo SERVERURL; ?>vistas/js/jquery-3.4.1.min.js"></script>
<script src="<?php echo SERVERURL; ?>vistas/js/popper.min.js"></script>
<script src="<?php echo SERVERURL; ?>vistas/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo SERVERURL; ?>vistas/js/bootstrap.min.js"></script>

<script>
    /*----------  Evento cuando se muestra el modal de Cliente  ----------*/
    // Cuando el modal con el ID 'ModalCliente' se muestra, llama a la función cargar_clientes()
    $('#ModalCliente').on('shown.bs.modal', function() {
        cargar_clientes();
    });

    /*----------  Cargar Clientes  ----------*/
    // Función para cargar los clientes mediante una solicitud fetch
    function cargar_clientes() {
        fetch('<?php echo SERVERURL; ?>ajax/evaluacionAjax.php?listar_clientes=true')
            .then(respuesta => respuesta.text()) // Obtener la respuesta como texto
            .then(respuesta => {
                // Insertar la respuesta en el elemento con el ID 'select_cliente'
                document.getElementById('select_cliente').innerHTML = respuesta;
            });
    }

    /*----------  Agregar Cliente  ----------*/
    // Función para agregar un cliente seleccionado
    function agregar_cliente() {
        var select_cliente = document.getElementById('select_cliente'); // Obtener el elemento select de clientes
        var id_cliente = select_cliente.value; // Obtener el valor seleccionado

        if (id_cliente !== "") {
            // Crear un objeto FormData y agregar el ID del cliente
            let datos = new FormData();
            datos.append("id_agregar_cliente", id_cliente);

            // Hacer una solicitud fetch para enviar los datos del cliente al servidor
            fetch('<?php echo SERVERURL; ?>ajax/evaluacionAjax.php', {
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

    /*----------  Evento cuando se muestra el modal de Proyecto  ----------*/
    // Cuando el modal con el ID 'ModalProyecto' se muestra, llama a la función cargar_proyectos()
    $('#ModalProyecto').on('shown.bs.modal', function() {
        cargar_proyectos();
    });

    /*----------  Cargar Proyectos  ----------*/
    // Función para cargar los proyectos mediante una solicitud fetch
    function cargar_proyectos() {
        fetch('<?php echo SERVERURL; ?>ajax/evaluacionAjax.php?listar_proyectos=true')
            .then(respuesta => respuesta.text()) // Obtener la respuesta como texto
            .then(respuesta => {
                // Insertar la respuesta en el elemento con el ID 'select_proyecto'
                document.getElementById('select_proyecto').innerHTML = respuesta;
            });
    }

    /*----------  Agregar Proyecto  ----------*/
    // Función para agregar un proyecto seleccionado
    function agregar_proyecto() {
        var select_proyecto = document.getElementById('select_proyecto'); // Obtener el elemento select de proyectos
        var id_proyecto = select_proyecto.value; // Obtener el valor seleccionado

        if (id_proyecto !== "") {
            // Crear un objeto FormData y agregar el ID del proyecto
            let datos = new FormData();
            datos.append("id_agregar_proyecto", id_proyecto);

            // Hacer una solicitud fetch para enviar los datos del proyecto al servidor
            fetch('<?php echo SERVERURL; ?>ajax/evaluacionAjax.php', {
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